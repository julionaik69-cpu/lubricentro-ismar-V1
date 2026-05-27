<?php
require_once '../app/models/Servicio.php';

class ServicioController {
    private $db;
    private $servicioModel;

    public function __construct($db) {
        $this->db = $db;
        $this->servicioModel = new Servicio($db);
        
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index() {
        $servicios = $this->servicioModel->listar();
        require_once '../app/views/servicios/index.php';
    }

    public function create() {
        require_once '../app/views/servicios/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->servicioModel->nombre      = trim($_POST['nombre'] ?? '');
            $this->servicioModel->descripcion = trim($_POST['descripcion'] ?? '');
            $this->servicioModel->precio      = $_POST['precio'] ?? 0.00;

            if ($this->servicioModel->existeNombre($this->servicioModel->nombre)) {
                echo "<script>alert('Este nombre de servicio ya existe registrado.'); window.history.back();</script>";
                return;
            }

            if($this->servicioModel->crear()) {
                header("Location: index.php?route=servicios&ok=1");
            } else {
                echo "Error al registrar el servicio.";
            }
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $servicio = $this->servicioModel->getById($id);
        
        if (!$servicio) {
            header("Location: index.php?route=servicios&error=notfound");
            exit;
        }
        
        require_once '../app/views/servicios/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_servicio'] ?? 0;
            
            $this->servicioModel->nombre      = trim($_POST['nombre'] ?? '');
            $this->servicioModel->descripcion = trim($_POST['descripcion'] ?? '');
            $this->servicioModel->precio      = $_POST['precio'] ?? 0.00;

            if($this->servicioModel->actualizar($id)) {
                header("Location: index.php?route=servicios&ok=2");
            } else {
                header("Location: index.php?route=servicios&error=1");
            }
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        if($this->servicioModel->eliminar($id)) {
            header("Location: index.php?route=servicios&ok=3");
        } else {
            header("Location: index.php?route=servicios&error=1");
        }
        exit;
    }
}
?>