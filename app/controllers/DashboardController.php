<?php

class DashboardController {
    private $db;
    private $dashboardModel;

    public function __construct($db) {
        $this->db = $db;
        // Cargamos el modelo asegurándonos de que exista
        $modelPath = '../app/models/Dashboard.php';
        if (file_exists($modelPath)) {
            require_once $modelPath;
            $this->dashboardModel = new Dashboard($db);
        }
    }

    public function index() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Candado de seguridad básico: Si no hay sesión, al login
        if (!isset($_SESSION['user_id'])) { 
            header("Location: index.php?route=login"); 
            exit; 
        }

        // Inicializamos variables de estadísticas con rescate por si el modelo falla
        $ventas_hoy = 0;
        $total_productos = 0;
        $stock_bajo = 0;
        $tickets_hoy = 0;
        $ventas_recientes = [];
        $datos_grafico = [];

        try {
            if ($this->dashboardModel) {
                $ventas_hoy = $this->dashboardModel->getVentasHoy() ?? 0;
                $total_productos = $this->dashboardModel->getTotalProductos() ?? 0;
                $stock_bajo = $this->dashboardModel->getStockBajo() ?? 0;
                $tickets_hoy = $this->dashboardModel->getTicketsHoy() ?? 0;
                $ventas_recientes = $this->dashboardModel->getVentasRecientes() ?? [];
                $datos_grafico = $this->dashboardModel->getDatosGraficoSemanales() ?? [];
            }
        } catch (Throwable $t) {
            // Si el modelo de ropa antiguo falla en las consultas del lubricentro, rescata en silencio
            error_log("Error en estadísticas de Dashboard: " . $t->getMessage());
        }

        // Inicializamos los contadores del tablero semáforo preventivo
        $total_vencidos = 0;
        $proximos_a_vencer = 0;
        $al_dia = 0;
        $limite_dias = 30; // Valor base por defecto

        // 🔥 CAPA DE PROTECCIÓN: Evitamos que el sistema muera si las tablas aún no existen
        try {
            // 1. Buscamos la configuración de días
            $stmtConfig = $this->db->query("SELECT valor FROM configuraciones WHERE clave = 'dias_alerta' LIMIT 1");
            if ($stmtConfig) {
                $configRow = $stmtConfig->fetch(PDO::FETCH_ASSOC);
                if ($configRow) $limite_dias = (int)$configRow['valor'];
            }
        } catch (PDOException $e) {
            $limite_dias = 30; // Si no existe la tabla configuraciones, usa 30
        }

        try {
            // 2. Consultamos mantenimientos preventivos
            $qSemaforo = "SELECT MAX(fecha) as fecha_ultimo FROM ventas GROUP BY placa";
            // Usamos la tabla 'ventas' y 'placa' que sí sabemos que existen y tienen datos
            $stmtSem = $this->db->query($qSemaforo);
            
            if ($stmtSem) {
                $mantenimientos = $stmtSem->fetchAll(PDO::FETCH_ASSOC);
                $hoy = new DateTime();

                foreach ($mantenimientos as $m) {
                    if (!empty($m['fecha_ultimo'])) {
                        $fecha_ultimo = new DateTime($m['fecha_ultimo']);
                        $dias_transcurridos = $hoy->diff($fecha_ultimo)->days;

                        if ($dias_transcurridos > $limite_dias) {
                            $total_vencidos++; // Rojo
                        } elseif ($dias_transcurridos >= 15 && $dias_transcurridos <= $limite_dias) {
                            $proximos_a_vencer++; // Amarillo
                        } else {
                            $al_dia++; // Verde
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            // Si falla la consulta o no hay placas, los semáforos se quedan en 0 de forma segura
            error_log("Error en semáforo preventivo: " . $e->getMessage());
        }

        // Enviamos todas las variables limpias y procesadas a la interfaz premium sin romperse
        require_once '../app/views/dashboard/index.php';
    }
}
?>