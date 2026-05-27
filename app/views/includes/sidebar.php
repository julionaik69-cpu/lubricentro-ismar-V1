<?php
// Asegurar la consulta de la configuración de la empresa para pintar el título si es necesario
$sidebarDb = new Database();
$sidebarConn = $sidebarDb->getConnection();
$nombreEmpresa = "LUBRICENTRO ISMAR";

if ($sidebarConn) {
    $sidebarStmt = $sidebarConn->query("SELECT razon_social FROM configuracion_empresa LIMIT 1");
    $sidebarCfg = $sidebarStmt->fetch(PDO::FETCH_ASSOC);
    if ($sidebarCfg && !empty($sidebarCfg['razon_social'])) {
        $nombreEmpresa = $sidebarCfg['razon_social'];
    }
}
?>

<div class="sidebar-erp">
    
    <div class="sidebar-brand-section">
        <i class="bi bi-speedometer text-info fs-5"></i>
        <span class="text-white fw-bold tracking-wider" style="font-size: 13px; text-transform: uppercase;">Menú Principal</span>
    </div>

    <?php 
    $routeActual = $_GET['route'] ?? '';
    
    // Función de control activo bajo la nueva paleta SaaS de la guía
    $activeClass = function($route) use ($routeActual) { 
        return ($routeActual == $route) ? 'sidebar-link-active' : 'sidebar-link-normal'; 
    }; 
    ?>
    
    <ul class="sidebar-menu-list">
        <li class="menu-item">
            <a href="index.php?route=dashboard" class="<?php echo $activeClass('dashboard'); ?>">
                <i class="bi bi-grid-1x2-fill menu-icon"></i> <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-category">Operaciones</li>
        <li class="menu-item">
            <a href="index.php?route=clientes" class="<?php echo $activeClass('clientes'); ?>">
                <i class="bi bi-people-fill menu-icon"></i> <span>Clientes</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=vehiculos" class="<?php echo $activeClass('vehiculos'); ?>">
                <i class="bi bi-car-front-fill menu-icon"></i> <span>Vehículos</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=servicios" class="<?php echo $activeClass('servicios'); ?>">
                <i class="bi bi-tools menu-icon"></i> <span>Servicios (Mano Obra)</span>
            </a>
        </li>

        <li class="menu-category">Inventario</li>
        <li class="menu-item">
            <a href="index.php?route=categorias" class="<?php echo $activeClass('categorias'); ?>">
                <i class="bi bi-tags-fill menu-icon"></i> <span>Categorías</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=productos" class="<?php echo $activeClass('productos'); ?>">
                <i class="bi bi-droplet-lock menu-icon"></i> <span>Aceites y Filtros</span>
            </a>
        </li>

        <li class="menu-category">Ventas y Facturación</li>
        <li class="menu-item">
            <a href="index.php?route=nueva_venta" class="<?php echo $activeClass('nueva_venta'); ?>">
                <i class="bi bi-cart-plus-fill menu-icon" style="color: #60A5FA;"></i> <span class="fw-semibold">Nueva Venta</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=historial_ventas" class="<?php echo $activeClass('historial_ventas'); ?>">
                <i class="bi bi-file-earmark-bar-graph-fill menu-icon"></i> <span>Historial / SUNAT</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=alertas" class="<?php echo $activeClass('alertas'); ?>">
                <i class="bi bi-bell-fill menu-icon" style="color: #F59E0B;"></i> <span>Alertas de Cambio</span>
            </a>
        </li>

        <li class="menu-category">Caja Diaria</li>
        <li class="menu-item">
            <a href="index.php?route=caja_apertura" class="<?php echo $activeClass('caja_apertura'); ?>">
                <i class="bi bi-unlock-fill menu-icon" style="color: #10B981;"></i> <span>Apertura Caja</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=caja_cierre" class="<?php echo $activeClass('caja_cierre'); ?>">
                <i class="bi bi-lock-fill menu-icon" style="color: #EF4444;"></i> <span>Cerrar Caja</span>
            </a>
        </li>

        <?php if(isset($_SESSION['user_rol']) && in_array(strtoupper($_SESSION['user_rol']), ['ADMIN', 'ADMINISTRADOR'])): ?>
            <li class="menu-category">Administración</li>
            <li class="menu-item">
                <a href="index.php?route=reportes" class="<?php echo $activeClass('reportes'); ?>">
                    <i class="bi bi-pie-chart-fill menu-icon"></i> <span>Reportes Gerenciales</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="index.php?route=usuarios" class="<?php echo $activeClass('usuarios'); ?>">
                    <i class="bi bi-person-gear menu-icon"></i> <span>Config. Usuarios</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="menu-category">Mantenimiento</li>
        <li class="menu-item">
            <a href="index.php?route=backup" class="sidebar-link-normal text-info-custom">
                <i class="bi bi-database-fill-down menu-icon"></i> <span>Respaldar Sistema</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?route=logout" class="sidebar-link-normal text-danger-custom">
                <i class="bi bi-power menu-icon"></i> <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer-version">
        <span>ERP Ismar v3.0</span>
        <br>
        <small class="text-muted">© 2026</small>
    </div>
</div>

<style>
    .sidebar-erp {
        width: 260px;
        min-width: 260px;
        background-color: #1E293B; /* Color exacto del manual */
        min-height: calc(100vh - 70px);
        position: fixed;
        top: 70px;
        left: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #2D3748;
        padding: 15px 0;
    }

    .sidebar-brand-section {
        padding: 0 20px 15px 20px;
        border-bottom: 1px solid #2D3748;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        overflow-y: auto;
        flex: 1;
    }

    /* Categorías / Separadores de menú */
    .menu-category {
        padding: 12px 20px 6px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748B;
        letter-spacing: 1px;
    }

    /* Enlaces del Sidebar */
    .sidebar-link-normal, .sidebar-link-active {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .sidebar-link-normal {
        color: #CBD5E1;
    }

    .sidebar-link-normal:hover {
        background-color: #334155; /* Hover exacto */
        color: #FFFFFF;
    }

    /* Estado Activo Inteligente */
    .sidebar-link-active {
        background-color: #2563EB; /* Azul acero de la guía */
        color: #FFFFFF !important;
        font-weight: 600;
    }

    .sidebar-link-active .menu-icon {
        color: #60A5FA !important; /* Icono activo celeste */
    }

    .menu-icon {
        font-size: 16px;
        transition: color 0.2s;
    }

    /* Clases de apoyo para acciones críticas */
    .text-danger-custom { color: #FCA5A5 !important; }
    .text-danger-custom:hover { background-color: rgba(239, 68, 68, 0.15) !important; color: #EF4444 !important; }

    .text-info-custom { color: #93C5FD !important; }
    .text-info-custom:hover { background-color: rgba(37, 99, 235, 0.12) !important; color: #38BDF8 !important; }

    .sidebar-footer-version {
        padding: 15px 20px 0 20px;
        border-top: 1px solid #2D3748;
        text-align: center;
        font-size: 11px;
        color: #64748B;
    }
    
    .tracking-wider { letter-spacing: 0.05rem; }

    /* Ajuste responsivo para el contenedor general del contenido principal */
    .content-principal-contenedor {
        margin-left: 260px;
        width: calc(100% - 260px);
        padding: 24px;
        transition: all 0.2s ease;
    }
</style>