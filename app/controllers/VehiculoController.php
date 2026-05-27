<?php
require_once '../app/models/Vehiculo.php';
require_once '../app/models/Cliente.php';

class VehiculoController {
    private $db;
    private $vehiculoModel;
    private $clienteModel;

    public function __construct($db) {
        $this->db = $db;
        $this->vehiculoModel = new Vehiculo($db);
        $this->clienteModel = new Cliente($db);
        
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index() {
        // Si viene un id_cliente por GET, filtramos los vehículos de ese cliente específico
        $id_cliente = $_GET['id_cliente'] ?? null;
        
        if ($id_cliente) {
            $vehiculos = $this->vehiculoModel->listarPorCliente($id_cliente);
            $clienteEspecifico = $this->clienteModel->getById($id_cliente);
        } else {
            $vehiculos = $this->vehiculoModel->listar();
            $clienteEspecifico = null;
        }
        
        require_once '../app/views/vehiculos/index.php';
    }

    public function create() {
        // Necesitamos listar todos los clientes para el selector desplegable del formulario
        $clientes = $this->clienteModel->listar();
        $id_cliente_preseleccionado = $_GET['id_cliente'] ?? null;
        require_once '../app/views/vehiculos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->vehiculoModel->id_cliente    = $_POST['id_cliente'];
            $this->vehiculoModel->placa         = trim($_POST['placa']);
            $this->vehiculoModel->marca         = trim($_POST['marca']);
            $this->vehiculoModel->modelo        = trim($_POST['modelo']);
            $this->vehiculoModel->anio          = !empty($_POST['anio']) ? $_POST['anio'] : null;
            $this->vehiculoModel->color         = trim($_POST['color'] ?? '');
            $this->vehiculoModel->kilometraje   = !empty($_POST['kilometraje']) ? $_POST['kilometraje'] : 0;
            $this->vehiculoModel->tipo_vehiculo = $_POST['tipo_vehiculo'] ?? 'Auto';

            if ($this->vehiculoModel->existePlaca($this->vehiculoModel->placa)) {
                echo "<script>alert('Esta placa ya se encuentra registrada en el sistema.'); window.history.back();</script>";
                return;
            }

            if($this->vehiculoModel->crear()) {
                header("Location: index.php?route=vehiculos&ok=1");
            } else {
                echo "Error al registrar el vehículo.";
            }
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $vehiculo = $this->vehiculoModel->getById($id);
        
        if (!$vehiculo) {
            header("Location: index.php?route=vehiculos&error=notfound");
            exit;
        }
        
        $clientes = $this->clienteModel->listar();
        require_once '../app/views/vehiculos/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_vehicle'] ?? 0;
            
            $this->vehiculoModel->id_cliente    = $_POST['id_cliente'];
            $this->vehiculoModel->placa         = trim($_POST['placa']);
            $this->vehiculoModel->marca         = trim($_POST['marca']);
            $this->vehiculoModel->modelo        = trim($_POST['modelo']);
            $this->vehiculoModel->anio          = !empty($_POST['anio']) ? $_POST['anio'] : null;
            $this->vehiculoModel->color         = trim($_POST['color'] ?? '');
            $this->vehiculoModel->kilometraje   = !empty($_POST['kilometraje']) ? $_POST['kilometraje'] : 0;
            $this->vehiculoModel->tipo_vehiculo = $_POST['tipo_vehiculo'] ?? 'Auto';
            $this->vehiculoModel->dias_alerta   = isset($_POST['dias_alerta']) ? intval($_POST['dias_alerta']) : 30; // 👈 AGREGAR ESTA LÍNEA

            if($this->vehiculoModel->actualizar($id)) {
                header("Location: index.php?route=vehiculos&ok=2");
            } else {
                header("Location: index.php?route=vehiculos&error=1");
            }
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        if($this->vehiculoModel->eliminar($id)) {
            header("Location: index.php?route=vehiculos&ok=3");
        } else {
            header("Location: index.php?route=vehiculos&error=1");
        }
        exit;
    }

    // NUEVO MÉTODO AJAX: Retorna los vehículos de un cliente en formato JSON
    public function buscarPorCliente() {
        $id_cliente = $_GET['id_cliente'] ?? 0;
        $vehiculos = $this->vehiculoModel->listarPorCliente($id_cliente);
        
        header('Content-Type: application/json');
        echo json_encode($vehiculos);
        exit;
    }
    public function exportarExcel() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

        try {
            // Consulta limpia uniendo vehículo con su propietario
            $stmt = $this->db->query("
                SELECT 
                    v.id_vehicle AS id, 
                    v.placa, 
                    v.marca, 
                    v.modelo, 
                    v.anio, 
                    v.tipo_vehiculo, 
                    v.kilometraje, 
                    v.color,
                    c.nombre AS propietario 
                FROM vehiculos v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                ORDER BY v.id_vehicle DESC
            ");
            $autos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Rescate por si las columnas varían ligeramente
            $stmt = $this->db->query("SELECT * FROM vehiculos ORDER BY 1 DESC");
            $autos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Reporte_Vehiculos_Ismar_".date('d-m-Y').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo chr(0xEF) . chr(0xBB) . chr(0xBF); // BOM UTF-8

        echo "<table border='1'>";
        echo "<tr style='background-color: #1E3A5F; color: white; font-weight: bold; text-align: center;'>";
        echo "<th>ID</th>";
        echo "<th>Placa / Matrícula</th>";
        echo "<th>Propietario Asociado</th>";
        echo "<th>Tipo de Unidad</th>";
        echo "<th>Marca</th>";
        echo "<th>Modelo</th>";
        echo "<th>Año</th>";
        echo "<th>Kilometraje (Km)</th>";
        echo "<th>Color</th>";
        echo "</tr>";

        foreach ($autos as $a) {
            $id = $a['id'] ?? $a['id_vehicle'] ?? '---';
            echo "<tr>";
            echo "<td style='text-align: center;'>" . $id . "</td>";
            echo "<td style='text-align: center; font-weight: bold; font-family: monospace;'>" . strtoupper($a['placa']) . "</td>";
            echo "<td>" . htmlspecialchars($a['propietario'] ?? 'Sin Propietario') . "</td>";
            echo "<td>" . htmlspecialchars($a['tipo_vehiculo'] ?? 'Auto') . "</td>";
            echo "<td>" . htmlspecialchars($a['marca']) . "</td>";
            echo "<td>" . htmlspecialchars($a['modelo']) . "</td>";
            echo "<td style='text-align: center;'> shadow" . ($a['anio'] ?? '---') . "</td>";
            echo "<td style='text-align: right; font-weight: bold;'>" . number_format($a['kilometraje'] ?? 0, 0, '.', ',') . "</td>";
            echo "<td>" . htmlspecialchars($a['color'] ?? '---') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
    public function guardar_vehiculo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente    = intval($_POST['id_cliente']);
            $placa         = strtoupper(trim($_POST['placa']));
            $tipo_vehiculo = $_POST['tipo_vehiculo'];
            $anio          = !empty($_POST['anio']) ? intval($_POST['anio']) : null;
            $marca         = trim($_POST['marca']);
            $modelo        = trim($_POST['modelo']);
            $kilometraje   = floatval($_POST['kilometraje']);
            $color         = trim($_POST['color'] ?? '');
            
            // 🔥 CAPTURAMOS LA NUEVA VARIABLE DEL FORMULARIO
            $dias_alerta   = isset($_POST['dias_alerta']) ? intval($_POST['dias_alerta']) : 30;

            try {
                // Modificamos el INSERT para incluir la nueva columna de tu SQLite
                $sql = "INSERT INTO vehiculos (id_cliente, placa, tipo_vehiculo, anio, marca, modelo, kilometraje, color, dias_alerta) 
                        VALUES (:id_cliente, :placa, :tipo, :anio, :marca, :modelo, :km, :color, :dias_alerta)";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id_cliente', $id_cliente);
                $stmt->bindParam(':placa', $placa);
                $stmt->bindParam(':tipo', $tipo_vehiculo);
                $stmt->bindParam(':anio', $anio);
                $stmt->bindParam(':marca', $marca);
                $stmt->bindParam(':modelo', $modelo);
                $stmt->bindParam(':km', $kilometraje);
                $stmt->bindParam(':color', $color);
                $stmt->bindParam(':dias_alerta', $dias_alerta); // Inyección segura
                
                $stmt->execute();
                
                header("Location: index.php?route=vehiculos&ok=1");
            } catch (PDOException $e) {
                error_log("Error guardando vehículo: " . $e->getMessage());
                header("Location: index.php?route=nuevo_vehiculo&error=1");
            }
            exit;
        }
    }
}
?>