<?php
require_once '../app/models/Cliente.php';

class ClienteController {
    private $db;
    private $clienteModel;

    public function __construct($db) {
        $this->db = $db;
        $this->clienteModel = new Cliente($db);
        
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // El módulo de clientes lo pueden usar tanto administradores como empleados
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index() {
        $clientes = $this->clienteModel->listar();
        require_once '../app/views/clientes/index.php';
    }

    public function create() {
        require_once '../app/views/clientes/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->clienteModel->tipo_documento   = $_POST['tipo_documento'] ?? 'DNI';
            $this->clienteModel->numero_documento = trim($_POST['numero_documento'] ?? '');
            $this->clienteModel->nombre           = trim($_POST['nombre'] ?? '');
            $this->clienteModel->telefono         = trim($_POST['telefono'] ?? '');
            $this->clienteModel->direccion        = trim($_POST['direccion'] ?? '');
            $this->clienteModel->correo           = trim($_POST['correo'] ?? '');

            if ($this->clienteModel->existeDocumento($this->clienteModel->numero_documento)) {
                echo "<script>alert('El número de documento (DNI/RUC) ya está registrado.'); window.history.back();</script>";
                return;
            }

            if($this->clienteModel->crear()) {
                header("Location: index.php?route=clientes&ok=1");
            } else {
                echo "Error al registrar el cliente.";
            }
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            header("Location: index.php?route=clientes&error=notfound");
            exit;
        }
        
        require_once '../app/views/clientes/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_cliente'] ?? 0;
            
            $this->clienteModel->tipo_documento   = $_POST['tipo_documento'] ?? 'DNI';
            $this->clienteModel->numero_documento = trim($_POST['numero_documento'] ?? '');
            $this->clienteModel->nombre           = trim($_POST['nombre'] ?? '');
            $this->clienteModel->telefono         = trim($_POST['telefono'] ?? '');
            $this->clienteModel->direccion        = trim($_POST['direccion'] ?? '');
            $this->clienteModel->correo           = trim($_POST['correo'] ?? '');

            if($this->clienteModel->actualizar($id)) {
                header("Location: index.php?route=clientes&ok=2");
            } else {
                header("Location: index.php?route=clientes&error=1");
            }
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        if($this->clienteModel->eliminar($id)) {
            header("Location: index.php?route=clientes&ok=3");
        } else {
            header("Location: index.php?route=clientes&error=1");
        }
        exit;
    }

    // NUEVO: Exportación limpia a Excel sin librerías pesadas externas
    public function exportarExcel() {
        $clientes = $this->clienteModel->listar();
        
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Clientes_LubricentroIsmar.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Imprimir cabecera de la tabla con codificación UTF-8 para evitar caracteres raros en Excel
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo "<table border='1'>";
        echo "<tr style='background-color:#ffc107; font-weight:bold;'>
                <th>ID</th>
                <th>Tipo Doc.</th>
                <th>N° Documento</th>
                <th>Nombre / Razón Social</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Correo</th>
              </tr>";
              
        foreach ($clientes as $c) {
            echo "<tr>";
            echo "<td>".$c['id_cliente']."</td>";
            echo "<td>".$c['tipo_documento']."</td>";
            echo "<td>'".$c['numero_documento']."</td>"; // Comilla simple para evitar que Excel borre los ceros a la izquierda
            echo "<td>".$c['nombre']."</td>";
            echo "<td>".$c['telefono']."</td>";
            echo "<td>".$c['direccion']."</td>";
            echo "<td>".$c['correo']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}
?>