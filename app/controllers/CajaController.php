<?php
if (class_exists('CajaController')) return;

require_once '../app/models/Caja.php';
require_once '../app/models/Gasto.php';

class CajaController {
    private $db;
    private $cajaModel;
    private $gastoModel;

    public function __construct($db) {
        $this->db         = $db;
        $this->cajaModel  = new Caja($db);
        $this->gastoModel = new Gasto($db);
        if (session_status() == PHP_SESSION_NONE) session_start();
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login"); exit;
        }
    }

    public function apertura() {
        $this->requireLogin();
        $caja = $this->cajaModel->obtenerCajaAbierta($_SESSION['user_id']);
        if ($caja) {
            header("Location: index.php?route=dashboard&msg=caja_ya_abierta"); exit;
        }
        require_once '../app/views/caja/apertura.php';
    }

    public function guardar_apertura() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header("Location: index.php?route=caja_apertura"); exit;
        }
        $monto = floatval($_POST['monto_inicial'] ?? 0);
        if ($monto < 0) $monto = 0;

        $resultado = $this->cajaModel->abrir($_SESSION['user_id'], $monto);
        if ($resultado) {
            header("Location: index.php?route=dashboard&msg=caja_abierta");
        } else {
            header("Location: index.php?route=caja_apertura&error=ya_abierta");
        }
        exit;
    }

    public function cierre() {
        $this->requireLogin();
        $caja = $this->cajaModel->obtenerCajaAbierta($_SESSION['user_id']);
        if (!$caja) {
            echo "<script>alert('No tienes caja abierta.'); window.location.href='index.php?route=dashboard';</script>";
            exit;
        }

        $totales         = $this->cajaModel->calcularTotales($caja['id']);
        $total_gastos    = $this->gastoModel->getTotalGastos($caja['id']);
        $lista_gastos    = $this->gastoModel->listarPorCaja($caja['id']);
        $venta_efectivo  = $totales['venta_efectivo'] ?? 0;

        $montoApertura   = $caja['monto_apertura'] ?? $caja['monto_inicial'] ?? 0.00;
        $total_esperado_en_cajon = $montoApertura + $venta_efectivo - $total_gastos;

        require_once '../app/views/caja/cierre.php';
    }

    public function guardar_cierre() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header("Location: index.php?route=caja_cierre"); exit;
        }

        $id_caja     = intval($_POST['id_caja']);
        $monto_final = floatval($_POST['monto_final']);

        $caja = $this->cajaModel->obtenerCajaAbierta($_SESSION['user_id']);
        if (!$caja || $caja['id'] != $id_caja) {
            echo "<script>alert('Error: caja no válida.'); window.history.back();</script>";
            exit;
        }

        $totales = $this->cajaModel->calcularTotales($id_caja);
        $venta_total = $totales['venta_total'] ?? 0;
        
        // Ejecutamos el cierre oficial en la BD
        $this->cajaModel->cerrar($id_caja, $venta_total, $monto_final);

        // 🔥 LA MEJORA: En lugar de ir al dashboard, lo mandamos directo a ver su reporte PDF impreso en pantalla
        header("Location: index.php?route=imprimir_cierre&id=" . $id_caja);
        exit;
    }

    // 🔥 NUEVO MÉTODO: Historial de Auditoría para la dueña (Ver cierres antiguos)
    public function historial_cierres() {
        $this->requireLogin();
        
        // Solo administradores pueden auditar cierres históricos
        if (strtoupper($_SESSION['user_rol']) !== 'ADMIN') {
            header("Location: index.php?route=dashboard&error=unauthorized"); exit;
        }

        // Jalamos los cierres históricos uniendo el nombre del cajero
        $query = "
            SELECT c.*, u.nombre AS cajero_nombre 
            FROM cajas c
            LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.estado = 0 
            ORDER BY c.fecha_cierre DESC
        ";
        $stmt = $this->db->query($query);
        $cierres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once '../app/views/caja/historial.php';
    }

    // 🔥 NUEVO MÉTODO: Generador de Reporte Cuadrado Real (Servidor)
    public function imprimir_cierre_pdf() {
        $this->requireLogin();
        $id_caja = intval($_GET['id'] ?? 0);

        $caja = null;
        try {
            $stmt = $this->db->prepare("SELECT * FROM cajas WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id_caja);
            $stmt->execute();
            $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fallo en consulta simple de caja: " . $e->getMessage());
        }

        if (!$caja) {
            die("Error: El reporte de caja solicitado no existe en la base de datos.");
        }

        // Recuperamos el mapa de usuarios para el nombre del cajero
        $caja['cajero_nombre'] = $_SESSION['user_nombre'] ?? 'Cajero de Turno';
        try {
            $id_busqueda = $caja['id_usuario'] ?? $caja['usuario_id'] ?? 0;
            if ($id_busqueda > 0) {
                $stmtUsr = $this->db->prepare("SELECT nombre FROM usuarios WHERE id_usuario = :id LIMIT 1");
                $stmtUsr->bindParam(':id', $id_busqueda);
                $stmtUsr->execute();
                $usrRow = $stmtUsr->fetch(PDO::FETCH_ASSOC);
                if ($usrRow) { $caja['cajero_nombre'] = $usrRow['nombre']; }
            }
        } catch (Throwable $t) {}

        // Mapeo contable flexible según las columnas reales de tu SQLite
        $monto_apertura  = $caja['monto_apertura'] ?? $caja['monto_inicial'] ?? 0.00;
        $saldo_real      = $caja['monto_final'] ?? $caja['monto_cierre'] ?? $caja['venta_total'] ?? 0.00;
        
        $venta_efectivo  = 0.00;
        $venta_tarjeta   = 0.00;
        $total_gastos    = 0.00;
        $lista_gastos    = [];

        try {
            $totales = $this->cajaModel->calcularTotales($id_caja);
            $venta_efectivo  = $totales['venta_efectivo'] ?? 0.00;
            $venta_tarjeta   = $totales['venta_tarjeta'] ?? 0.00;
        } catch (Throwable $e) {}

        try {
            $total_gastos    = $this->gastoModel->getTotalGastos($id_caja) ?? 0.00;
            $lista_gastos    = $this->gastoModel->listarPorCaja($id_caja) ?? [];
        } catch (Throwable $e) {}
        
        $saldo_esperado  = $monto_apertura + $venta_efectivo - $total_gastos;
        $diferencia      = $saldo_real - $saldo_esperado;

        // Renderizamos la plantilla ejecutiva A4 que creamos
        require_once '../app/views/caja/reporte_vacio_pdf.php';
    }
}
?>