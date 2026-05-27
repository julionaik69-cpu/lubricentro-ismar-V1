<?php

class AlertaController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index() {
        // Obtener configuración de días límite
        $limite_dias = 30;
        try {
            $stmt = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'dias_alerta'");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $limite_dias = $row ? (int)$row['valor'] : 30;
        } catch (PDOException $e) {
            $limite_dias = 30;
        }

        // Actualizar límite si se envía por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dias_alerta_input'])) {
            $nuevo_limite = (int)$_POST['dias_alerta_input'];
            try {
                $this->db->exec("UPDATE configuracion SET valor = $nuevo_limite WHERE clave = 'dias_alerta'");
            } catch (PDOException $e) {
                $this->db->exec("CREATE TABLE IF NOT EXISTS configuracion (clave TEXT PRIMARY KEY, valor TEXT)");
                $this->db->exec("INSERT INTO configuracion (clave, valor) VALUES ('dias_alerta', $nuevo_limite)");
            }
            $limite_dias = $nuevo_limite;
            header("Location: index.php?route=alertas");
            exit;
        }

        // Obtener ventas con cliente y calcular días
        $query = "
            SELECT 
                v.id as id_venta,
                v.fecha,
                v.cliente_nombre,
                v.cliente_num_doc,
                v.total,
                v.placa,
                julianday('now') - julianday(v.fecha) as dias_transcurridos
            FROM ventas v
            WHERE v.placa IS NOT NULL AND v.placa != ''
            ORDER BY v.fecha DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Procesar cada venta
        $lista_general = [];
        foreach ($ventas as $v) {
            $dias = round($v['dias_transcurridos'] ?? 0);
            
            // Determinar semáforo según días
            if ($dias <= $limite_dias) {
                $semaforo = 'success';
                $estado_texto = 'Al día / Reciente';
            } elseif ($dias <= $limite_dias + 15) {
                $semaforo = 'warning';
                $estado_texto = 'Próximo a vencer';
            } else {
                $semaforo = 'danger';
                $estado_texto = 'Vencido (Llamar ya)';
            }
            
            // Obtener teléfono del cliente (si existe)
            $telefono = '';
            if (!empty($v['cliente_num_doc'])) {
                $stmtCli = $this->db->prepare("SELECT telefono FROM clientes WHERE numero_documento = :doc LIMIT 1");
                $stmtCli->bindParam(':doc', $v['cliente_num_doc']);
                $stmtCli->execute();
                $cliente = $stmtCli->fetch(PDO::FETCH_ASSOC);
                $telefono = $cliente ? $cliente['telefono'] : '999999999';
            }
            
            $lista_general[] = [
                'id_venta' => $v['id_venta'],
                'placa' => $v['placa'] ?? 'S/P',
                'cliente_nombre' => $v['cliente_nombre'] ?? 'Cliente General',
                'cliente_telefono' => $telefono,
                'fecha' => $v['fecha'],
                'dias_transcurridos' => $dias,
                'semaforo' => $semaforo,
                'estado_texto' => $estado_texto
            ];
        }
        // Aplicar filtros si existen
            $filtro = $_GET['filtro'] ?? '';

            if ($filtro == 'pendientes') {
                $lista_general = array_filter($lista_general, function($item) use ($limite_dias) {
                    return $item['dias_transcurridos'] <= $limite_dias;
                });
            } elseif ($filtro == 'vencidos') {
                $lista_general = array_filter($lista_general, function($item) use ($limite_dias) {
                    return $item['dias_transcurridos'] > $limite_dias && $item['dias_transcurridos'] <= 45;
                });
            } elseif ($filtro == 'criticos') {
                $lista_general = array_filter($lista_general, function($item) {
                    return $item['dias_transcurridos'] > 45;
                });
            }

            // Reindexar el array después del filtro
            $lista_general = array_values($lista_general);

        // Mensaje predeterminado de WhatsApp
        $mensaje_predeterminado = "Hola *[CLIENTE]*, te saluda el equipo de *Lubricentro Ismar*. Queríamos recordarte que tu vehículo con placa *[PLACA]* ya cumplió el tiempo estimado desde su último cambio de aceite. ¡Te esperamos para mantener tu motor al 100%! 🚗💨";

        try {
            $stmt = $this->db->query("SELECT valor FROM configuracion WHERE clave = 'mensaje_whatsapp'");
            $rowMensaje = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rowMensaje) {
                $mensaje_predeterminado = $rowMensaje['valor'];
            }
        } catch (PDOException $e) {
            // Usar mensaje por defecto
        }

        require_once '../app/views/alertas/index.php';
    }

    public function marcar_notificado() {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->db->prepare("UPDATE ventas SET estado_alerta = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: index.php?route=alertas");
        exit;
    }

    public function descartar() {
        $id_venta = $_GET['id'] ?? 0;
        $stmt = $this->db->prepare("UPDATE ventas SET estado_alerta = 2 WHERE id = :id");
        $stmt->bindParam(':id', $id_venta);
        $stmt->execute();
        header("Location: index.php?route=alertas");
        exit;
    }
}
?>