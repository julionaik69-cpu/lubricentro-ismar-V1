<?php
class DashboardController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        if (session_status() == PHP_SESSION_NONE) session_start();
        date_default_timezone_set('America/Lima');
    }
    
    public function index() {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
        
        // ==================== 1. VENTAS DE HOY ====================
        $hoy = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE DATE(fecha) = :hoy AND estado = 1");
        $stmt->execute([':hoy' => $hoy]);
        $ventas_hoy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // ==================== 2. TOTAL PRODUCTOS ====================
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE estado = 1");
        $stmt->execute();
        $total_productos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // ==================== 3. STOCK CRÍTICO ====================
        // Productos con stock menor o igual al stock mínimo
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo AND estado = 1");
        $stmt->execute();
        $stock_bajo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // ==================== 4. TICKETS DE HOY ====================
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = :hoy AND estado = 1");
        $stmt->execute([':hoy' => $hoy]);
        $tickets_hoy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // ==================== 5. DATOS PARA GRÁFICO (7 días) ====================
        $datos_grafico = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE DATE(fecha) = :fecha AND estado = 1");
            $stmt->execute([':fecha' => $fecha]);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $datos_grafico[] = ['fecha' => $fecha, 'total' => $total];
        }
        
        // ==================== 6. VENTAS RECIENTES ====================
        $stmt = $this->db->prepare("SELECT id, cliente_nombre, total, metodo_pago, fecha 
                                    FROM ventas 
                                    WHERE estado = 1 
                                    ORDER BY fecha DESC 
                                    LIMIT 10");
        $stmt->execute();
        $ventas_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ==================== 7. CONTROL DE ACEITE (Semáforo) ====================
        // Fecha límite: hace 30 días
        $fechaLimite = date('Y-m-d', strtotime('-30 days'));
        
        // Total de servicios realizados en los últimos 30 días
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT id_vehiculo) as total 
                                    FROM servicios_realizados 
                                    WHERE fecha_registro >= :fecha_limite");
        $stmt->execute([':fecha_limite' => $fechaLimite]);
        $vehiculos_al_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Servicios realizados entre 15 y 30 días (próximos a vencer)
        $fecha15 = date('Y-m-d', strtotime('-15 days'));
        $fecha30 = date('Y-m-d', strtotime('-30 days'));
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT id_vehiculo) as total 
                                    FROM servicios_realizados 
                                    WHERE fecha_registro BETWEEN :fecha30 AND :fecha15");
        $stmt->execute([':fecha15' => $fecha15, ':fecha30' => $fecha30]);
        $proximos_a_vencer = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Vehículos que NO tienen servicios en los últimos 30 días (vencidos)
        // Esta es una consulta más compleja: vehículos que han tenido servicio pero hace más de 30 días
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT v.id_vehicle) as total 
            FROM vehiculos v
            WHERE v.estado = 1 
            AND v.id_vehicle NOT IN (
                SELECT DISTINCT id_vehiculo 
                FROM servicios_realizados 
                WHERE fecha_registro >= :fecha_limite
            )
        ");
        $stmt->execute([':fecha_limite' => $fechaLimite]);
        $total_vencidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Ajustar al día
        $al_dia = $vehiculos_al_dia;
        
        // ==================== 8. DATOS ADICIONALES ====================
        // Verificar si hay caja abierta
        $stmt = $this->db->prepare("SELECT id FROM cajas WHERE usuario_id = :uid AND estado = 1 LIMIT 1");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $caja_abierta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no hay caja abierta, redirigir o mostrar alerta (opcional)
        if (!$caja_abierta) {
            $_SESSION['error_caja'] = "Debe aperturar caja antes de operar";
        }
        
        // Cargar la vista
        require_once '../app/views/dashboard/index.php';
    }
    
    // Método para registrar gastos rápidos
    public function guardar_gasto() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $monto = floatval($_POST['monto'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');
            
            if ($monto <= 0 || empty($descripcion)) {
                $_SESSION['error'] = "Complete todos los campos correctamente";
                header("Location: index.php?route=dashboard");
                exit;
            }
            
            // Insertar gasto con PostgreSQL
            $stmt = $this->db->prepare("INSERT INTO gastos (monto, descripcion, usuario_id, fecha) 
                                        VALUES (:monto, :desc, :uid, NOW())");
            $result = $stmt->execute([
                ':monto' => $monto,
                ':desc' => $descripcion,
                ':uid' => $_SESSION['user_id']
            ]);
            
            if ($result) {
                $_SESSION['mensaje'] = "Gasto registrado correctamente";
            } else {
                $_SESSION['error'] = "Error al registrar gasto";
            }
        }
        
        header("Location: index.php?route=dashboard");
        exit;
    }
}
?>