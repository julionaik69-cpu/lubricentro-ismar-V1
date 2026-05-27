<?php
require_once '../app/models/Configuracion.php';

class ConfigController {
    private $db;
    private $configModel;

    public function __construct($db) {
        $this->db = $db;
        $this->configModel = new Configuracion($db);
        
        if (session_status() == PHP_SESSION_NONE) session_start();
        // Ajuste de rol: Asegúrate que coincida con tu base de datos
        if (!isset($_SESSION['user_rol']) || strtoupper($_SESSION['user_rol']) != 'ADMIN') {
            header("Location: index.php?route=dashboard");
            exit;
        }
    }

    public function index() {
        // Traer la configuración actual de la BD
        $stmt = $this->db->query("SELECT * FROM configuracion LIMIT 1");
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        // Incluir la vista pasándole la variable $empresa
        require_once 'app/views/configuracion/empresa.php';
    }

    public function guardar() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // 1. Recoger los datos del formulario
        $ruc = $_POST['ruc'] ?? '';
        $nombre = $_POST['nombre_empresa'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        // ... las credenciales de sunat si van en el mismo formulario:
        $usuario_sol = $_POST['usuario_sol'] ?? '';
        $clave_sol = $_POST['clave_sol'] ?? '';

        // 2. Ejecutar el UPDATE o INSERT en la Base de Datos
        // Asegúrate de que tu modelo ejecute la consulta usando estas variables de arriba
        $sql = "UPDATE configuracion SET 
                ruc = :ruc, 
                nombre_empresa = :nombre, 
                telefono = :telefono,
                usuario_sol = :usuario_sol,
                clave_sol = :clave_sol 
                WHERE id = 1"; // O como manejes tu ID de empresa
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ruc' => $ruc,
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':usuario_sol' => $usuario_sol,
            ':clave_sol' => $clave_sol
        ]);

        // 3. Redireccionar de vuelta para no duplicar envío
        header("Location: index.php?route=config_empresa");
        exit;
    }
}
}
?>