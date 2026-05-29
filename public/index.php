<?php
// 1. Inicialización limpia y controlada de la sesión global (Evita duplicados)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Lima');

// 2. Carga obligatoria de componentes base de datos y arquitectura
require_once '../app/config/Database.php';
require_once '../app/models/Dashboard.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/CategoriaController.php';
require_once '../app/controllers/ProductoController.php';
require_once '../app/controllers/VentaController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/CajaController.php';
require_once '../app/controllers/UsuarioController.php';
require_once '../app/controllers/GastoController.php';
require_once '../app/controllers/ReporteController.php';
require_once '../app/controllers/BackupController.php';
require_once '../app/controllers/ConfigEmpresaController.php';
require_once '../app/controllers/SunatController.php'; 

// --- CONTROLADORES EXCLUSIVOS PARA EL LUBRICENTRO ---
require_once '../app/controllers/ClienteController.php';
require_once '../app/controllers/VehiculoController.php';
require_once '../app/controllers/ServicioController.php';
require_once '../app/controllers/AlertaController.php';

// 3. Establecer conexión con el motor SQLite del taller
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("<div style='font-family:sans-serif;padding:40px;color:red;'><h2>❌ Error de conexión a la base de datos</h2><p>Verifica que las credenciales de la base de datos en la nube sean correctas.</p></div>");}

// Optimización explícita para alto rendimiento en transacciones de lubricentro
//$db->exec("PRAGMA journal_mode=WAL;");
//$db->exec("PRAGMA foreign_keys=ON;");

// 4. Captura e inicialización limpia de la ruta solicitada por Brave
$route = isset($_GET['route']) ? trim($_GET['route']) : 'login';

// 5. 🔥 EL FILTRO INTELIGENTE CORREGIDO (Ya cuenta con la variable $route inicializada)
if (isset($_SESSION['user_id'])) {
    // Si el usuario ya está logueado e intenta ir a la pantalla de acceso, empújalo al Dashboard
    if ($route == 'login' || $route == 'procesar_login') {
        header("Location: index.php?route=dashboard");
        exit();
    }
} else {
    // Si NO ha iniciado sesión y la ruta no es de acceso, forzarlo al login de inmediato
    if ($route != 'login' && $route != 'procesar_login') {
        header("Location: index.php?route=login");
        exit();
    }
}

// 6. ENRUTADOR PRINCIPAL DE LA APLICACIÓN (Mapeo Simétrico MVC)
switch ($route) {

    // --- AUTENTICACIÓN ---
    case 'login':
    case 'procesar_login': // Mapeamos ambas variantes para que no se pierdan en el AuthController
        $auth = new AuthController($db); 
        $auth->login(); 
        break;
        
    case 'logout':
        $auth = new AuthController($db); 
        $auth->logout(); 
        break;

    // --- DASHBOARD GENERAL ---
    case 'dashboard':
        $c = new DashboardController($db); 
        $c->index(); 
        break;

    // --- CATEGORÍAS DE ACEITES Y REPUESTOS ---
    case 'categorias':
        $c = new CategoriaController($db); $c->index(); break;
    case 'nueva_categoria':
        $c = new CategoriaController($db); $c->create(); break;
    case 'guardar_categoria':
        $c = new CategoriaController($db); $c->store(); break;
    case 'editar_categoria':
        $c = new CategoriaController($db); $c->edit(); break;
    case 'actualizar_categoria':
        $c = new CategoriaController($db); $c->update(); break;
    case 'eliminar_categoria':
        $c = new CategoriaController($db); $c->delete(); break;

    // --- INVENTARIO DE PRODUCTOS ---
    case 'productos':
        $c = new ProductoController($db); $c->index(); break;
    case 'nuevo_producto':
        $c = new ProductoController($db); $c->create(); break;
    case 'guardar_producto':
        $c = new ProductoController($db); $c->store(); break;
    case 'editar_producto':
        $c = new ProductoController($db); $c->edit(); break;
    case 'actualizar_producto':
        $c = new ProductoController($db); $c->update(); break;
    case 'eliminar_producto':
        $c = new ProductoController($db); $c->delete(); break;
    case 'exportar_productos':
    case 'exportar_productos_excel': // Soporte para ambas variaciones de ruta de descarga
        $controller = new ProductoController($db);
        $controller->exportarExcel(); 
        break;
    case 'actualizar_stock_manual':
        $c = new ProductoController($db); $c->actualizar_stock_manual(); break;

    // --- GESTIÓN DE CLIENTES ---
    case 'clientes':
        $c = new ClienteController($db); $c->index(); break;
    case 'nuevo_cliente':
        $c = new ClienteController($db); $c->create(); break;
    case 'guardar_cliente':
        $c = new ClienteController($db); $c->store(); break;
    case 'editar_cliente':
        $c = new ClienteController($db); $c->edit(); break;
    case 'actualizar_cliente':
        $c = new ClienteController($db); $c->update(); break;
    case 'eliminar_cliente':
        $c = new ClienteController($db); $c->delete(); break;
    case 'exportar_clientes':
        $c = new ClienteController($db); $c->exportarExcel(); break;

    // --- HISTORIAL Y VINCULACIÓN DE VEHÍCULOS ---
    case 'vehiculos':
        $c = new VehiculoController($db); $c->index(); break;
    case 'nuevo_vehiculo':
        $c = new VehiculoController($db); $c->create(); break;
    case 'guardar_vehiculo':
        $c = new VehiculoController($db); $c->store(); break;
    case 'editar_vehiculo':
        $c = new VehiculoController($db); $c->edit(); break;
    case 'actualizar_vehiculo':
        $c = new VehiculoController($db); $c->update(); break;
    case 'eliminar_vehiculo':
        $c = new VehiculoController($db); $c->delete(); break;
    case 'buscar_vehiculos_cliente':
        $c = new VehiculoController($db); $c->buscarPorCliente(); break;

    // --- SERVICIOS Y TALLER (MANO DE OBRA) ---
    case 'servicios':
        $c = new ServicioController($db); $c->index(); break;
    case 'nuevo_servicio':
        $c = new ServicioController($db); $c->create(); break;
    case 'guardar_servicio':
        $c = new ServicioController($db); $c->store(); break;
    case 'editar_servicio':
        $c = new ServicioController($db); $c->edit(); break;
    case 'actualizar_servicio':
        $c = new ServicioController($db); $c->update(); break;
    case 'eliminar_servicio':
        $c = new ServicioController($db); $c->delete(); break;

    // --- ALERTAS DE MANTENIMIENTO PREVENTIVO ---
    case 'alertas':
        $c = new AlertaController($db); $c->index(); break;

    // --- CAJA OPERATIVA Y TRANSACCIONAL ---
    case 'caja_apertura':
        $c = new CajaController($db); $c->apertura(); break;
    case 'guardar_apertura':
        $c = new CajaController($db); $c->guardar_apertura(); break;
    case 'caja_cierre':
        $c = new CajaController($db); $c->cierre(); break;
    case 'guardar_cierre':
        $c = new CajaController($db); $c->guardar_cierre(); break;

    // --- GASTOS DIARIOS DE CAJA ---
    case 'guardar_gasto':
        $c = new GastoController($db); $c->registrar(); break;
    case 'eliminar_gasto':
        $c = new GastoController($db); $c->eliminar(); break;

    // --- FLUJO DE CAJA CHICA / NUEVA VENTA ---
    case 'nueva_venta':
        $c = new VentaController($db); $db_mode = $c->index(); break;
    case 'agregar_carrito':
        $c = new VentaController($db); $c->agregar(); break;
    case 'quitar_carrito':
        $c = new VentaController($db); $c->quitar(); break;
    case 'limpiar_carrito':
        $c = new VentaController($db); $c->limpiar(); break;
    case 'finalizar_venta':
        $c = new VentaController($db); $c->finalizar(); break;
    case 'historial_ventas':
        $c = new VentaController($db); $c->historial(); break;
    case 'exportar_historial':
    case 'exportar_ventas_excel': // Unificación total para llamadas desde el historial
        $controller = new VentaController($db);
        if (method_exists($controller, 'exportarExcel')) {
            $controller->exportarExcel();
        } else if (method_exists($controller, 'exportar_excel')) {
            $controller->exportar_excel();
        } else {
            die("ERROR: El método de exportación no está definido dentro de VentaController.php");
        }
        break;
    case 'ver_ticket':
        $c = new VentaController($db); $c->ver_ticket(); break;
    case 'anular_venta':
        $c = new VentaController($db);
        $c->anular();
        break;
    case 'consulta_api':
        $c = new VentaController($db); $c->consulta_api(); break;

    // --- CONTROL DE USUARIOS DEL SISTEMA ---
    case 'usuarios':
        $c = new UsuarioController($db); $c->index(); break;
    case 'nuevo_usuario':
        $c = new UsuarioController($db); $c->create(); break;
    case 'guardar_usuario':
        $c = new UsuarioController($db); $c->store(); break;
    case 'editar_usuario':
        $c = new UsuarioController($db); $c->edit(); break;
    case 'actualizar_usuario':
        $c = new UsuarioController($db); $c->update(); break;
    case 'eliminar_usuario':
        $c = new UsuarioController($db); $c->delete(); break;

    // --- REPORTES AUDITABLES ---
    case 'reportes':
        $c = new ReporteController($db); $c->index(); break;
    case 'imprimir_cierre':
        $c = new CajaController($db); $c->imprimir_cierre_pdf(); break;
    case 'exportar_reporte_excel':
        $controller = new ReporteController($db);
        $controller->exportar_reporte_excel();  // ← este método existe ahora
        break;
    case 'limpiar_filtros_reportes':
        header("Location: index.php?route=reportes");
        break;

    // --- BACKUP MANUAL INTEGRADO ---
    case 'backup':
        $c = new BackupController(); $c->descargar(); break;

    // --- FACTURACIÓN ELECTRÓNICA SUNAT ---
    case 'config_empresa':
        $c = new ConfigEmpresaController($db); $c->index(); break;
    case 'guardar_empresa':
        $c = new ConfigEmpresaController($db); $c->guardar(); break;
    case 'guardar_correlativos':
        $c = new ConfigEmpresaController($db); $c->guardarCorrelativos(); break;
    case 'subir_certificado':
        $c = new ConfigEmpresaController($db); $c->subirCertificado(); break;
    case 'enviar_sunat':
        $c = new SunatController($db); $c->enviar(); break;
    case 'reenviar_sunat':
        $c = new SunatController($db); $c->reenviar(); break;

    // --- BOTÓN DE REPARACIÓN DE CAJAS (MANTENIMIENTO DE EMERGENCIA) ---
    case 'fix_caja':
        if (!isset($_SESSION['user_rol']) || !in_array(strtoupper($_SESSION['user_rol']), ['ADMIN', 'ADMINISTRADOR'])) {
            header("Location: index.php?route=login"); exit;
        }
        $db->exec("UPDATE cajas SET fecha_cierre = NOW (), monto_final = 0, estado = 0 WHERE estado = 1");
        session_destroy();
        echo "<script>alert('🛠️ Todas las cajas abiertas fueron cerradas de forma exitosa. Inicia sesión limpiamente.'); window.location.href='index.php?route=login';</script>";
        break;
    // --- PON ESTO DENTRO DE LOS CASE DE VEHÍCULOS EN public/index.php ---
    case 'exportar_vehiculos_excel':
        $c = new VehiculoController($db);
        $c->exportarExcel();
        break;
    case 'historial_cierres':
        $c = new CajaController($db); $c->historial_cierres(); break;
    case 'imprimir_cierre':
        $c = new CajaController($db); $c->imprimir_cierre_pdf(); break;

    case 'alertas':
        $c = new AlertaController($db); $c->index(); break;
    case 'descartar_alerta':
        $c = new AlertaController($db); $c->descartar(); break;
    case 'marcar_notificado':
        $c = new AlertaController($db); $c->marcar_notificado(); break;

    case 'variantes_producto':
        $c = new ProductoController($db);
        $c->variantes(); 
        break;
    case 'guardar_variante':
        $c = new ProductoController($db); $c->guardar_variante(); break;
    case 'eliminar_variante':
        $c = new ProductoController($db); $c->eliminar_variante(); break;
    // --- RUTA POR DEFECTO REBOTADOR ---
    default:
        header("Location: index.php?route=login"); 
        break;
}
?>