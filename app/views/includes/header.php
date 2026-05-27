<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lubricentro Ismar - ERP</title>
    <link rel="icon" type="image/png" href="/Lubricentro/public/cambio_de_aceite.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <style>
        /* ==========================================================================
           CONFIGURACIÓN GLOBAL DE TIPOGRAFÍA Y COLORES (GUÍA UI/UX)
           ========================================================================== */
        body { 
            font-family: 'Poppins', sans-serif;
            background-color: #F5F7FA; /* Gris claro de fondo general */
            color: #334155; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            margin: 0;
        }

        .wrapper { 
            display: flex; 
            flex: 1; 
            margin-top: 70px; /* Margen para compensar el Navbar fijo */
        }

        /* Estilos base reutilizables para todo el ERP */
        h2, .h2-titulo { font-size: 24px; font-weight: 600; color: #1E3A5F; }
        h5, .h5-subtitulo { font-size: 18px; font-weight: 500; color: #1E3A5F; }
        p, text { font-size: 14px; font-weight: 400; }

        /* Tarjetas Modernas SaaS */
        .card-erp {
            background: #FFFFFF;
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-erp:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        /* Formularios e Inputs cómodos */
        .form-control-erp {
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            color: #334155;
            transition: border-color 0.2s ease-in-out;
        }
        .form-control-erp:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }
        .label-erp {
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* Botones ERP Redondeados Moderadamente */
        .btn-erp-primary { background-color: #2563EB; color: #FFFFFF; font-weight: 600; font-size: 14px; border-radius: 8px; padding: 10px 20px; border: none; transition: background 0.2s; }
        .btn-erp-primary:hover { background-color: #1D4ED8; color: #FFFFFF; }
        
        .btn-erp-secondary { background-color: #64748B; color: #FFFFFF; font-weight: 600; font-size: 14px; border-radius: 8px; padding: 10px 20px; border: none; transition: background 0.2s; }
        .btn-erp-secondary:hover { background-color: #475569; color: #FFFFFF; }

        .btn-erp-success { background-color: #10B981; color: #FFFFFF; font-weight: 600; border-radius: 8px; border: none; }
        .btn-erp-danger { background-color: #EF4444; color: #FFFFFF; font-weight: 600; border-radius: 8px; border: none; }
        .btn-erp-warning { background-color: #F59E0B; color: #FFFFFF; font-weight: 600; border-radius: 8px; border: none; }

        /* Estilización de Tablas DataTables */
        table.dataTable thead {
            background-color: #F1F5F9 !important;
            color: #334155 !important;
            font-size: 13px;
            font-weight: 600;
        }
        table.dataTable tbody tr {
            font-size: 13px;
            border-bottom: 1px solid #E2E8F0;
        }
        table.dataTable tbody tr:hover {
            background-color: #EFF6FF !important; /* Celeste sutil de la guía */
        }

        /* Navbar Superior Fijo Premium */
        .navbar-custom {
            height: 70px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E5E7EB;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            padding: 0 24px;
        }
        .navbar-brand-custom {
            font-size: 18px;
            font-weight: 600;
            color: #1E3A5F !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-profile-section {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
        }
        .profile-avatar {
            width: 38px;
            height: 38px;
            background-color: #EFF6FF;
            color: #2563EB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
            border: 1px solid #E2E8F0;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center p-0">
            
            <?php
            // Obtener el nombre real de la base de datos de manera dinámica para el Navbar
            $navDb = new Database();
            $navConn = $navDb->getConnection();
            $navNombre = "LUBRICENTRO ISMAR";
            if ($navConn) {
                $navStmt = $navConn->query("SELECT razon_social FROM configuracion_empresa LIMIT 1");
                $navCfg = $navStmt->fetch(PDO::FETCH_ASSOC);
                if ($navCfg && !empty($navCfg['razon_social'])) {
                    $navNombre = $navCfg['razon_social'];
                }
            }
            ?>
            
            <a class="navbar-brand-custom" href="index.php?route=dashboard">
                <i class="bi bi-gear-wide-connected text-primary fs-4"></i>
                <span><?php echo htmlspecialchars($navNombre); ?></span>
            </a>
            
            <div class="user-profile-section">
                <span><i class="bi bi-bell text-muted fs-5 me-2 cursor-pointer"></i></span>
                <div class="d-none d-sm-block text-end">
                    <div class="fw-bold text-dark" style="font-size: 13px;"><?php echo htmlspecialchars($_SESSION['user_nombre'] ?? 'Operador'); ?></div>
                    <div class="text-muted" style="font-size: 11px; font-weight: 600;"><?php echo htmlspecialchars($_SESSION['user_rol'] ?? 'Mecánico'); ?></div>
                </div>
                <div class="profile-avatar">
                    <?php 
                        $nombreCompleto = $_SESSION['user_nombre'] ?? 'O';
                        echo strtoupper(substr($nombreCompleto, 0, 1)); 
                    ?>
                </div>
            </div>

        </div>
    </nav>
    
    <div class="wrapper">