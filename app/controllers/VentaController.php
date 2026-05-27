<?php
require_once '../app/models/Venta.php';
require_once '../app/models/Caja.php';
require_once '../app/models/Cliente.php';
require_once '../app/models/Vehiculo.php';
require_once '../app/models/Servicio.php';

class VentaController {
    private $db;
    private $ventaModel;
    private $cajaModel;
    private $clienteModel;
    private $vehiculoModel;
    private $servicioModel;

    public function __construct($db) {
        $this->db            = $db;
        $this->ventaModel    = new Venta($db);
        $this->cajaModel     = new Caja($db);
        $this->clienteModel  = new Cliente($db);
        $this->vehiculoModel = new Vehiculo($db);
        $this->servicioModel = new Servicio($db);
        if (session_status() == PHP_SESSION_NONE) session_start();
        date_default_timezone_set('America/Lima');
    }

    private function verificarPermisoDeVenta() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login"); exit;
        }
        $caja = $this->cajaModel->obtenerCajaAbierta($_SESSION['user_id']);
        if (!$caja) {
            echo "<script>alert('⚠️ Debes APERTURAR CAJA antes de realizar operaciones de venta.'); window.location.href='index.php?route=caja_apertura';</script>";
            exit;
        }
        $fecha_apertura = date('Y-m-d', strtotime($caja['fecha_apertura']));
        $hoy = date('Y-m-d');
        if ($fecha_apertura < $hoy) {
            $this->cajaModel->cerrar($caja['id'], 0, 0);
            echo "<script>alert('ℹ️ Se cerró automáticamente la caja del día anterior. Abre una nueva caja.'); window.location.href='index.php?route=caja_apertura';</script>";
            exit;
        }
        return $caja;
    }

    public function index() {
        $this->verificarPermisoDeVenta();
        
        $termino = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
        
        if (!empty($termino)) {
            $productos = $this->ventaModel->buscarProducto($termino);
        } else {
            $productos = $this->ventaModel->listarProductosDisponibles(24);
        }

    
        $categorias = $this->ventaModel->listarCategorias();
        $serviciosManoObra = $this->servicioModel->listar();
        $clientes = $this->clienteModel->listar();
        
        $total_venta = 0;
        if (isset($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
                $total_venta += $item['subtotal'];
            }
        }
        
        require_once '../app/views/ventas/nueva.php';
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login"); exit;
        }
    }

    public function agregar() {
        $this->verificarPermisoDeVenta();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id       = intval($_POST['id_producto']);
            $tipo     = $_POST['tipo_item'] ?? 'PRODUCTO'; 
            $nombre   = htmlspecialchars($_POST['nombre']);
            $precio   = floatval($_POST['precio']);
            $cantidad = intval($_POST['cantidad'] ?? 1);
            
            if ($cantidad < 1) $cantidad = 1;

            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            $encontrado = false;
            foreach ($_SESSION['carrito'] as $key => $item) {
                if (($item['tipo'] ?? 'PRODUCTO') == $tipo && $item['id'] == $id) {
                    $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
                    $_SESSION['carrito'][$key]['subtotal'] = $_SESSION['carrito'][$key]['cantidad'] * $_SESSION['carrito'][$key]['precio'];
                    $encontrado = true;
                    break;
                }
            }
            
            if (!$encontrado) {
                $_SESSION['carrito'][] = [
                    'id'       => $id,
                    'tipo'     => $tipo,
                    'nombre'   => $nombre,
                    'precio'   => $precio,
                    'cantidad' => $cantidad,
                    'subtotal' => $precio * $cantidad
                ];
            }
        }
        
        $search = $_POST['search_term'] ?? '';
        header("Location: index.php?route=nueva_venta&buscar=" . urlencode($search));
        exit;
    }

    public function quitar() {
        if (isset($_GET['idx']) && isset($_SESSION['carrito'])) {
            $idx = intval($_GET['idx']);
            if (isset($_SESSION['carrito'][$idx])) {
                array_splice($_SESSION['carrito'], $idx, 1);
            }
        }
        $search = $_GET['buscar'] ?? '';
        header("Location: index.php?route=nueva_venta&buscar=" . urlencode($search));
        exit;
    }

    public function limpiar() {
        $_SESSION['carrito'] = [];
        header("Location: index.php?route=nueva_venta");
        exit;
    }

    public function finalizar() {
        $this->verificarPermisoDeVenta();
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?route=nueva_venta"); return;
        }

        $total_carrito = 0;
        foreach ($_SESSION['carrito'] as $item) $total_carrito += $item['subtotal'];

        $tipo_comprobante = $_POST['tipo_comprobante'] ?? '03';
        $id_cliente       = intval($_POST['id_cliente'] ?? 0);
        $id_vehiculo      = !empty($_POST['id_vehiculo']) ? intval($_POST['id_vehiculo']) : null;
        $km_actual        = !empty($_POST['km_actual']) ? floatval($_POST['km_actual']) : 0.00;
        $metodo_pago      = $_POST['metodo_pago'] ?? 'EFECTIVO';
        $descuento        = floatval($_POST['descuento'] ?? 0.00);

        $total_final = $total_carrito - $descuento;
        if($total_final < 0) $total_final = 0;

        $clienteData = $this->clienteModel->getById($id_cliente);
        $cliente_tipo_doc = $clienteData['tipo_documento'] ?? '1';
        $cliente_num_doc  = $clienteData['numero_documento'] ?? '00000000';
        $cliente_nombre   = $clienteData['nombre'] ?? 'Público General';

        $resultado = $this->ventaModel->registrarVenta(
            $_SESSION['user_id'],
            $tipo_comprobante,
            $cliente_tipo_doc,
            $cliente_num_doc,
            $cliente_nombre,
            $total_final,
            $_SESSION['carrito'],
            $metodo_pago
        );

        if ($resultado['ok']) {
            $id_venta = $resultado['id'];

            // CONDICIÓN INTELIGENTE DE LUBRICENTRO AUTOMATIZADA
            if (!empty($id_vehiculo)) {
                $this->vehiculoModel->actualizarKilometraje($id_vehiculo, $km_actual);
                $proximo_cambio = $km_actual + 5000;

                // Forzamos el guardado con la fecha de hoy en formato nativo SQLite (YYYY-MM-DD HH:MM:SS)
                $stmtH = $this->db->prepare("INSERT INTO servicios_realizados 
                    (id_venta, id_vehiculo, id_servicio, observaciones, kilometraje_actual, proximo_cambio, fecha_registro) 
                    VALUES (:id_v, :id_veh, :id_ser, :obs, :km_act, :prox, datetime('now', 'localtime'))");
                
                foreach ($_SESSION['carrito'] as $item) {
                    // Si se vende un servicio o un ítem relacionado, se guarda en el historial clínico del auto
                    $id_servicio_relacionado = ($item['tipo'] === 'SERVICIO') ? $item['id'] : 1; 
                    
                    $stmtH->execute([
                        ':id_v'   => $id_venta,
                        ':id_veh' => $id_vehiculo,
                        ':id_ser' => $id_servicio_relacionado,
                        ':obs'    => 'Mantenimiento de fluidos: Venta registrada de ' . $item['nombre'],
                        ':km_act' => $km_actual,
                        ':prox'   => $proximo_cambio
                    ]);
                }
            }

            $_SESSION['carrito'] = [];

            $stmtUpd = $this->db->prepare("UPDATE ventas SET estado_sunat = 'LOCAL' WHERE id = :id");
            $stmtUpd->execute([':id' => $id_venta]);

            $alerta = '✅ Venta registrada correctamente. Comprobante guardado en modo local.';
            /*  
            require_once '../app/sunat/SunatHelper.php';
            $ventaData = $this->ventaModel->getVentaById($id_venta);
            $detallesData = $this->ventaModel->getDetalleVenta($id_venta);
            
            $stmtCfg = $this->db->query("SELECT ruc, razon_social as nombre_empresa, direccion, telefono, email FROM configuracion_empresa LIMIT 1");
            $configData = $stmtCfg->fetch(PDO::FETCH_ASSOC);

            $sunatRes = SunatHelper::emitirComprobante($ventaData, $detallesData, $configData);
            
            $stmtUpd = $this->db->prepare("UPDATE ventas SET estado_sunat = :est WHERE id = :id");
            $stmtUpd->execute([':est' => $sunatRes['estado_sunat'], ':id' => $id_venta]);

            $alerta = $sunatRes['xml_generado'] 
                ? ($sunatRes['ok'] ? '✅ Comprobante enviado y aprobado por SUNAT' : '⚠️ Registrado. SUNAT: ' . $sunatRes['msg'])
                : '✅ Ticket registrado de forma interna local.';*/

            echo "<script>
                alert('$alerta');
                if(confirm('¿Desea imprimir el Ticket de servicio técnico?')) {
                    window.location.href = 'index.php?route=ver_ticket&id=$id_venta&print=1';
                } else {
                    window.location.href = 'index.php?route=nueva_venta';
                }
            </script>";
        } else {
            $msg = $resultado['msg'] ?? 'Error desconocido';
            echo "<script>alert('❌ Error de Base de Datos: $msg'); window.history.back();</script>";
        }
    }




    public function historial() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { 
            header("Location: index.php"); 
            exit; 
        }

        // Obtener fechas (GET o POST)
        $fecha_inicio = $_GET['fecha_inicio'] ?? $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_GET['fecha_fin'] ?? $_POST['fecha_fin'] ?? date('Y-m-d');

        $ventas = [];
        try {
            // Consulta con filtro de fechas CORRECTO
            $query = "
                SELECT 
                    v.id,
                    v.fecha,
                    v.total,
                    v.metodo_pago,
                    v.estado,
                    v.tipo_comprobante,
                    v.cliente_nombre,
                    v.cliente_num_doc
                FROM ventas v
                WHERE DATE(v.fecha) BETWEEN :inicio AND :fin
                ORDER BY v.fecha DESC, v.id DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->execute();
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en historial: " . $e->getMessage());
            // Fallback si hay error
            $stmt = $this->db->query("SELECT * FROM ventas ORDER BY id DESC LIMIT 50");
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once '../app/views/ventas/historial.php';
    }

    public function ver_ticket() {
        $this->requireLogin(); 
        if (!isset($_GET['id'])) {
            header("Location: index.php?route=historial_ventas"); exit;
        }
        
        $venta = $this->ventaModel->getVentaById($_GET['id']);
        $detalles = $this->ventaModel->getDetalleVenta($_GET['id']);
        
        // Seleccionamos los campos en un orden fijo para no depender de los nombres de columna
        $stmtCfg = $this->db->query("SELECT ruc, razon_social, direccion, telefono FROM configuracion_empresa LIMIT 1");
        $empresaRaw = $stmtCfg->fetch(PDO::FETCH_ASSOC);

        if ($empresaRaw) {
            // Convertimos los datos a un array indexado por números (0, 1, 2, 3) para asegurar la lectura
            $valores = array_values($empresaRaw);
            $empresa = [
                'ruc'          => $valores[0] ?? 'N/A',
                'razon_social' => $valores[1] ?? 'LUBRICENTRO ISMAR',
                'direccion'    => $valores[2] ?? 'Dirección',
                'telefono'     => $valores[3] ?? '---'
            ];
        } else {
            // Si la tabla está vacía totalmente
            $empresa = [
                'razon_social' => 'LUBRICENTRO ISMAR (Por Defecto)',
                'ruc'          => '00000000000',
                'direccion'    => 'Configure la empresa en el menú',
                'telefono'     => '---'
            ];
        }

        require_once '../app/views/ventas/ticket.php';
    }
    public function exportarExcel() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

        $ventas = [];
        
        // 🔥 INTENTO 1: Probamos con la estructura estándar de ropa ('id')
        try {
            $stmt = $this->db->query("
                SELECT 
                    v.id AS id, 
                    v.fecha, 
                    v.total, 
                    v.placa,
                    c.nombre AS cliente_nombre 
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                ORDER BY v.id DESC
            ");
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // 🔥 INTENTO 2: Si falla el primero, probamos con 'id_venta' o variantes
            try {
                $stmt = $this->db->query("
                    SELECT 
                        v.id_venta AS id, 
                        v.fecha, 
                        v.total, 
                        v.placa,
                        c.nombre AS cliente_nombre 
                    FROM ventas v
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                    ORDER BY v.id_venta DESC
                ");
                $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                // 🔥 INTENTO 3: Rescate definitivo sin JOINs por si las columnas de clientes variaron
                $stmt = $this->db->query("SELECT * FROM ventas ORDER BY 1 DESC");
                $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        // Configuración de cabeceras para descarga limpia en Brave
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Reporte_Ventas_Lubricentro_".date('d-m-Y').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // BOM UTF-8 para evitar problemas con eñes, tildes o caracteres de monedas
        echo chr(0xEF) . chr(0xBB) . chr(0xBF);

        echo "<table border='1'>";
        echo "<tr style='background-color: #1E3A5F; color: white; font-weight: bold; text-align: center;'>";
        echo "<th>ID Venta</th>";
        echo "<th>Fecha / Hora</th>";
        echo "<th>Cliente Propietario</th>";
        echo "<th>Placa Vehículo</th>";
        echo "<th>Total Recaudado</th>";
        echo "</tr>";

        foreach ($ventas as $v) {
            // Mapeo dinámico de llaves según lo que haya devuelto SQLite
            $idVenta = $v['id'] ?? $v['id_venta'] ?? $v['id_ventas'] ?? '---';
            $fechaFormateada = !empty($v['fecha']) ? date('d/m/Y H:i', strtotime($v['fecha'])) : '---';
            $cliente = $v['cliente_nombre'] ?? $v['nombre_cliente'] ?? 'Público General';
            $placa = !empty($v['placa']) ? strtoupper($v['placa']) : '---';
            $total = $v['total'] ?? 0;

            echo "<tr>";
            echo "<td style='text-align: center;'>" . $idVenta . "</td>";
            echo "<td style='text-align: center;'>" . $fechaFormateada . "</td>";
            echo "<td>" . htmlspecialchars($cliente) . "</td>";
            echo "<td style='text-align: center; font-weight: bold; font-family: monospace;'>" . $placa . "</td>";
            echo "<td style='text-align: right;'>S/ " . number_format($total, 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    // Clon de respaldo
    public function exportar_excel() {
        $this->exportarExcel();
    }
    public function anular() {
    // Verificar permisos
        $this->verificarPermisoDeVenta();
        
        $id_venta = $_GET['id'] ?? 0;
        if (!$id_venta) {
            header("Location: index.php?route=historial_ventas");
            exit;
        }
        
        try {
            // Obtener la venta
            $stmt = $this->db->prepare("SELECT * FROM ventas WHERE id = :id");
            $stmt->bindParam(':id', $id_venta);
            $stmt->execute();
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }
            
            if ($venta['estado'] == 0) {
                throw new Exception("Esta venta ya está anulada");
            }
            
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // 1. Obtener los detalles de la venta (usando venta_id)
            $stmtDet = $this->db->prepare("SELECT * FROM detalle_ventas WHERE venta_id = :id");
            $stmtDet->bindParam(':id', $id_venta);
            $stmtDet->execute();
            $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. Devolver stock para productos (usando item_id)
            foreach ($detalles as $item) {
                if ($item['tipo_item'] == 'PRODUCTO') {
                    $stmtStock = $this->db->prepare("UPDATE productos SET stock = stock + :cant WHERE id_producto = :id");
                    $stmtStock->bindParam(':cant', $item['cantidad']);
                    $stmtStock->bindParam(':id', $item['item_id']);
                    $stmtStock->execute();
                }
            }
            
            // 3. Anular la venta
            $stmtUpd = $this->db->prepare("UPDATE ventas SET estado = 0, estado_sunat = 'ANULADO' WHERE id = :id");
            $stmtUpd->bindParam(':id', $id_venta);
            $stmtUpd->execute();
            
            // 4. Commit de la transacción
            $this->db->commit();
            
            $_SESSION['mensaje'] = "Venta #{$id_venta} anulada correctamente. Stock devuelto.";
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }
        
        header("Location: index.php?route=historial_ventas");
        exit;
    }
}
?>