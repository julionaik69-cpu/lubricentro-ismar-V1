<?php
// Asegurar que el modelo se cargue correctamente respetando la arquitectura del lubricentro
$modelPath = '../app/models/Producto.php';
if (!file_exists($modelPath)) {
    die("ERROR: No se encuentra el archivo $modelPath");
}
require_once $modelPath;

// Cargamos el controlador de categorías, el cual ahora apunta a la tabla del lubricentro
require_once '../app/models/Categoria.php'; 

class ProductoController {
    private $db;
    private $productoModel;
    private $categoriaModel;

    public function __construct($db) {
        $this->db = $db;
        $this->productoModel = new Producto($db);
        $this->categoriaModel = new Categoria($db); // Usado para rellenar los selects de categorías de aceites/filtros
        
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index() {
        // El método listar ya nos devuelve el array asociativo mapeado con PDO en el modelo nuevo
        $productos = $this->productoModel->listar();
        require_once '../app/views/productos/index.php';
    }

    public function create() {
        // Listamos las categorías de insumos mecánicos para el formulario desplegable
        $stmt = $this->categoriaModel->listar();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/views/productos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->productoModel->codigo        = !empty($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $this->productoModel->nombre        = trim($_POST['nombre']);
            $this->productoModel->id_categoria  = $_POST['id_categoria'];
            $this->productoModel->marca         = trim($_POST['marca']);
            $this->productoModel->stock         = $_POST['stock'] ?? 0;
            $this->productoModel->stock_minimo  = $_POST['stock_minimo'] ?? 5;
            $this->productoModel->precio_compra = $_POST['precio_compra'];
            $this->productoModel->precio_venta  = $_POST['precio_venta'];
            $this->productoModel->unidad_medida = $_POST['unidad_medida'] ?? 'Unidad';

            if ($this->productoModel->crear()) {
                header("Location: index.php?route=productos&ok=1");
            } else {
                header("Location: index.php?route=nuevo_producto&error=duplicate");
            }
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $producto = $this->productoModel->getById($id);
        
        if (!$producto) {
            header("Location: index.php?route=productos&error=notfound");
            exit;
        }
        
        $stmt = $this->categoriaModel->listar();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once '../app/views/productos/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_producto'] ?? 0;
            
            $this->productoModel->codigo        = !empty($_POST['codigo']) ? trim($_POST['codigo']) : null;
            $this->productoModel->nombre        = trim($_POST['nombre']);
            $this->productoModel->id_categoria  = $_POST['id_categoria'];
            $this->productoModel->marca         = trim($_POST['marca']);
            $this->productoModel->stock_minimo  = $_POST['stock_minimo'] ?? 5;
            $this->productoModel->precio_compra = $_POST['precio_compra'];
            $this->productoModel->precio_venta  = $_POST['precio_venta'];
            $this->productoModel->unidad_medida = $_POST['unidad_medida'] ?? 'Unidad';

            if($this->productoModel->actualizar($id)) {
                header("Location: index.php?route=productos&ok=2");
            } else {
                header("Location: index.php?route=editar_producto&id=" . $id . "&error=duplicate");
            }
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        if($this->productoModel->eliminar($id)) {
            header("Location: index.php?route=productos&ok=3");
        } else {
            header("Location: index.php?route=productos&error=1");
        }
        exit;
    }

    // NUEVO: Método para actualizar stock de lubricantes manualmente desde el Kardex/Inventario rápido
    public function actualizar_stock_manual() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_producto = $_POST['id_producto'];
            $nuevo_stock = $_POST['nuevo_stock'];

            $query = "UPDATE productos SET stock = :stock WHERE id_producto = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':stock', $nuevo_stock);
            $stmt->bindParam(':id', $id_producto);
            $stmt->execute();

            header("Location: index.php?route=productos&ok=2");
            exit;
        }
    }

    // Exportación limpia a Excel adaptada a las columnas de un lubricentro
    public function exportarExcel() {
        $productos = $this->productoModel->listar();

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Inventario_LubricentroIsmar_' . date('Ymd_His') . '.xls');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo chr(0xEF) . chr(0xBB) . chr(0xBF); // BOM UTF-8

        echo '<table border="1">';
        echo '<tr style="background-color:#ffc107; color:black; font-weight:bold;">';
        echo '<th>ID</th>';
        echo '<th>Código</th>';
        echo '<th>Descripción del Insumo</th>';
        echo '<th>Marca</th>';
        echo '<th>Categoría</th>';
        echo '<th>Medida</th>';
        echo '<th>P. Compra</th>';
        echo '<th>P. Venta</th>';
        echo '<th>Stock Actual</th>';
        echo '<th>Estado Alerta</th>';
        echo '</tr>';

        foreach ($productos as $p) {
            // Lógica automatizada de estado de alertas en reporte
            $alerta = ($p['stock'] <= $p['stock_minimo']) ? 'STOCK CRÍTICO' : 'DISPONIBLE';
            $styleAlerta = ($p['stock'] <= $p['stock_minimo']) ? 'style="color:red;font-weight:bold;"' : '';

            echo '<tr>';
            echo '<td>' . $p['id_producto'] . '</td>';
            echo '<td>' . htmlspecialchars($p['codigo'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($p['nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($p['marca']) . '</td>';
            echo '<td>' . htmlspecialchars($p['nombre_categoria'] ?? 'Sin categoría') . '</td>';
            echo '<td>' . htmlspecialchars($p['unidad_medida'] ?? 'Unidad') . '</td>';
            echo '<td style="text-align:right;">S/ ' . number_format($p['precio_compra'], 2) . '</td>';
            echo '<td style="text-align:right;">S/ ' . number_format($p['precio_venta'], 2) . '</td>';
            echo '<td style="text-align:center; font-weight:bold;">' . $p['stock'] . '</td>';
            echo '<td text-align:center; ' . $styleAlerta . '>' . $alerta . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }
}
?>