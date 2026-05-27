<?php

class ReporteController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { 
            header("Location: index.php?route=login"); 
            exit; 
        }
    }

    public function index() {
        // Obtener filtros

        $fecha_inicio = $_POST['fecha_inicio'] ?? $_GET['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin    = $_POST['fecha_fin'] ?? $_GET['fecha_fin'] ?? date('Y-m-d');
        $metodo_pago  = $_POST['metodo_pago'] ?? $_GET['metodo_pago'] ?? '';
        
        // Inicializar variables
        $total_ventas = 0;
        $total_gastos = 0;
        $ventas_detalladas = [];
        $gastos_detallados = [];
        $turnos_caja = [];
        $top_productos = [];
        $top_servicios = [];

        try {
            // ============================================================
            // 1. VENTAS DETALLADAS CON FILTRO DE FECHAS
            // ============================================================
            $sql_ventas = "
                SELECT 
                    v.id,
                    v.fecha,
                    v.total,
                    v.metodo_pago,
                    v.estado,
                    v.cliente_nombre
                FROM ventas v
                WHERE DATE(v.fecha) BETWEEN :inicio AND :fin
            ";
            
            if (!empty($metodo_pago)) {
                $sql_ventas .= " AND v.metodo_pago = :metodo";
            }
            $sql_ventas .= " ORDER BY v.id DESC";
            
            $stmt = $this->db->prepare($sql_ventas);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            if (!empty($metodo_pago)) {
                $stmt->bindParam(':metodo', $metodo_pago);
            }
            $stmt->execute();
            $ventas_detalladas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total de ventas (SOLO las emitidas)
            $total_ventas = 0;
            foreach ($ventas_detalladas as $v) {
                if (($v['estado'] ?? 1) == 1) {
                    $total_ventas += floatval($v['total'] ?? 0);
                }
            }
            
            // ============================================================
            // 2. GASTOS CON FILTRO DE FECHAS
            // ============================================================
            $sql_gastos = "
                SELECT 
                    g.id,
                    g.descripcion,
                    g.monto,
                    g.fecha
                FROM gastos g
                WHERE DATE(g.fecha) BETWEEN :inicio AND :fin
                ORDER BY g.fecha DESC
            ";
            
            $stmt = $this->db->prepare($sql_gastos);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->execute();
            $gastos_detallados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total de gastos
            $total_gastos = 0;
            foreach ($gastos_detallados as $g) {
                $total_gastos += floatval($g['monto'] ?? 0);
            }
            
            // ============================================================
            // 3. TURNOS DE CAJA (CON FILTRO DE FECHA)
            // ============================================================
            $sql_cajas = "
                SELECT 
                    c.id,
                    c.usuario_id,
                    c.monto_apertura,
                    c.monto_cierre,
                    c.fecha_apertura,
                    c.fecha_cierre,
                    c.estado,
                    u.nombre as cajero_nombre
                FROM cajas c
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE DATE(c.fecha_apertura) BETWEEN :inicio AND :fin
                ORDER BY c.id DESC
            ";

            $stmt = $this->db->prepare($sql_cajas);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->execute();
            $turnos_caja = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // ============================================================
            // 4. PRODUCTOS MÁS VENDIDOS (con filtro de fechas)
            // ============================================================
            $sql_productos = "
                SELECT 
                    dv.nombre_producto as nombre,
                    SUM(dv.cantidad) as total_cantidad,
                    SUM(dv.subtotal) as total_venta
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE DATE(v.fecha) BETWEEN :inicio AND :fin
                    AND (v.estado = 1 OR v.estado IS NULL)
                    AND dv.tipo_item = 'PRODUCTO'
                GROUP BY dv.nombre_producto
                ORDER BY total_venta DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($sql_productos);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->execute();
            $top_productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // ============================================================
            // 5. SERVICIOS MÁS REALIZADOS (con filtro de fechas)
            // ============================================================
            $sql_servicios = "
                SELECT 
                    dv.nombre_producto as nombre,
                    SUM(dv.cantidad) as total_cantidad,
                    SUM(dv.subtotal) as total_venta
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE DATE(v.fecha) BETWEEN :inicio AND :fin
                    AND (v.estado = 1 OR v.estado IS NULL)
                    AND dv.tipo_item = 'SERVICIO'
                GROUP BY dv.nombre_producto
                ORDER BY total_venta DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($sql_servicios);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->execute();
            $top_servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en ReporteController: " . $e->getMessage());
        }
        
        // Utilidad neta
        $utilidad_neta = $total_ventas - $total_gastos;
        
        $is_print = isset($_GET['print']) && $_GET['print'] == 1;

        // Si es para imprimir, usar una vista especial
        if ($is_print) {
            $this->imprimirVista($fecha_inicio, $fecha_fin, $metodo_pago, $total_ventas, $total_gastos, $ventas_detalladas, $gastos_detallados, $turnos_caja, $top_productos, $top_servicios);
            exit;
        }

        // Pasar variables a la vista
        require_once '../app/views/reportes/index.php';
    }
    
    // ============================================================
    // EXPORTAR A EXCEL (con los mismos filtros)
    // ============================================================
    public function exportar_reporte_excel() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $metodo_pago = $_GET['metodo_pago'] ?? '';
        
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=reporte_{$fecha_inicio}_a_{$fecha_fin}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo chr(0xEF) . chr(0xBB) . chr(0xBF);
        
        // Calcular ventas del período
        $sql = "SELECT id, fecha, total, metodo_pago, estado, cliente_nombre 
                FROM ventas 
                WHERE DATE(fecha) BETWEEN :inicio AND :fin";
        
        if(!empty($metodo_pago)) {
            $sql .= " AND metodo_pago = :metodo";
        }
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':inicio', $fecha_inicio);
        $stmt->bindParam(':fin', $fecha_fin);
        if(!empty($metodo_pago)) {
            $stmt->bindParam(':metodo', $metodo_pago);
        }
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_excel = 0;
        foreach($ventas as $v) {
            if(($v['estado'] ?? 1) == 1) {
                $total_excel += floatval($v['total'] ?? 0);
            }
        }
        
        echo "<table border='1'>";
        echo "<tr style='background-color: #1E3A5F; color: white;'>";
        echo "<th>ID</th><th>Fecha</th><th>Cliente</th><th>Método Pago</th><th>Total</th><th>Estado</th>";
        echo "</table>";
        
        foreach($ventas as $v) {
            echo "<tr>";
            echo "<td>{$v['id']}</td>";
            echo "<td>{$v['fecha']}</td>";
            echo "<td>" . htmlspecialchars($v['cliente_nombre'] ?? 'General') . "</td>";
            echo "<td>{$v['metodo_pago']}</td>";
            echo "<td>" . number_format($v['total'], 2) . "</td>";
            echo "<td>" . (($v['estado'] ?? 1) == 1 ? 'EMITIDO' : 'ANULADO') . "</td>";
            echo "</tr>";
        }
        
        echo "<tr style='background-color: #E2E8F0; font-weight: bold;'>";
        echo "<td colspan='4' style='text-align: right;'>TOTAL:</td>";
        echo "<td>" . number_format($total_excel, 2) . "</td>";
        echo "<td></td>";
        echo "</tr>";
        echo "</table>";
        exit;
    }

    private function imprimirVista($fecha_inicio, $fecha_fin, $metodo_pago, $total_ventas, $total_gastos, $ventas_detalladas, $gastos_detallados, $turnos_caja, $top_productos, $top_servicios) {
        $utilidad = $total_ventas - $total_gastos;
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reporte Lubricentro Ismar</title>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; padding: 20px; }
                .report-wrapper { max-width: 1200px; margin: 0 auto; }
                .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #1E3A5F; }
                .header h1 { color: #1E3A5F; font-size: 26px; text-transform: uppercase; margin-bottom: 5px; }
                .header h2 { color: #2563EB; font-size: 16px; margin-bottom: 8px; }
                .periodo { background: linear-gradient(135deg, #1E3A5F, #2563EB); color: white; padding: 10px; text-align: center; border-radius: 10px; margin-bottom: 25px; }
                .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
                .kpi-card { background: #F8FAFC; border-radius: 12px; padding: 15px; text-align: center; border-left: 4px solid #2563EB; }
                .kpi-card .label { font-size: 11px; text-transform: uppercase; color: #64748B; }
                .kpi-card .value { font-size: 24px; font-weight: bold; margin-top: 8px; }
                .kpi-card.total .value { color: #2563EB; }
                .kpi-card.gastos .value { color: #EF4444; }
                .kpi-card.utilidad .value { color: #10B981; }
                .section { margin-bottom: 25px; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; }
                .section-title { background: #1E3A5F; color: white; padding: 12px 18px; font-size: 13px; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #F1F5F9; padding: 12px; text-align: left; font-size: 11px; font-weight: 600; }
                td { padding: 10px 12px; border-bottom: 1px solid #E2E8F0; font-size: 12px; }
                .text-end { text-align: right; }
                .badge-success { background: #10B981; color: white; padding: 3px 10px; border-radius: 20px; font-size: 10px; display: inline-block; }
                .badge-danger { background: #EF4444; color: white; padding: 3px 10px; border-radius: 20px; font-size: 10px; display: inline-block; }
                .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #E2E8F0; font-size: 9px; color: #94A3B8; }
                @media print { body { padding: 0; } .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="report-wrapper">
                <div class="header">
                    <h1>🏭 LUBRICENTRO ISMAR</h1>
                    <h2>REPORTE DE GESTIÓN Y AUDITORÍA</h2>
                    <p>RUC: 20601234567 | Av. Principal 123 - Lima | Teléfono: (01) 234-5678</p>
                </div>
                
                <div class="periodo">
                    📅 PERÍODO DE ANÁLISIS: <?php echo $fecha_inicio; ?> hasta <?php echo $fecha_fin; ?>
                </div>
                
                <div class="kpi-grid">
                    <div class="kpi-card total">
                        <div class="label">💰 TOTAL VENTAS</div>
                        <div class="value">S/ <?php echo number_format($total_ventas, 2); ?></div>
                    </div>
                    <div class="kpi-card gastos">
                        <div class="label">📉 TOTAL GASTOS</div>
                        <div class="value">S/ <?php echo number_format($total_gastos, 2); ?></div>
                    </div>
                    <div class="kpi-card utilidad">
                        <div class="label">📈 UTILIDAD NETA</div>
                        <div class="value">S/ <?php echo number_format($utilidad, 2); ?></div>
                    </div>
                    <div class="kpi-card">
                        <div class="label">🔄 TRANSACCIONES</div>
                        <div class="value"><?php echo count($ventas_detalladas); ?></div>
                    </div>
                </div>
                
                <!-- TURNOS -->
                <div class="section">
                    <div class="section-title">📋 CONTROL DE TURNOS Y APERTURAS DE CAJA</div>
                    <table>
                        <thead><tr><th>ID</th><th>Cajero</th><th>Apertura</th><th>Monto Inicial</th><th>Cierre</th><th>Monto Final</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php foreach($turnos_caja as $tc): ?>
                            <tr>
                                <td>#<?php echo $tc['id']; ?></td>
                                <td><?php echo htmlspecialchars($tc['cajero_nombre'] ?? 'Cajero'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($tc['fecha_apertura'])); ?></td>
                                <td>S/ <?php echo number_format($tc['monto_apertura'] ?? 0, 2); ?></td>
                                <td><?php echo !empty($tc['fecha_cierre']) ? date('d/m/Y H:i', strtotime($tc['fecha_cierre'])) : '---'; ?></td>
                                <td>S/ <?php echo number_format($tc['monto_cierre'] ?? 0, 2); ?></td>
                                <td><?php echo empty($tc['fecha_cierre']) ? '<span class="badge-success">ABIERTO</span>' : '<span class="badge-success">CERRADO</span>'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- VENTAS -->
                <div class="section">
                    <div class="section-title">🧾 ÓRDENES DE SERVICIO FACTURADAS</div>
                    <table>
                        <thead><tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Método</th><th class="text-end">Total</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php foreach($ventas_detalladas as $v): ?>
                            <tr>
                                <td>#<?php echo $v['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($v['cliente_nombre'] ?? 'General'); ?></td>
                                <td><?php echo $v['metodo_pago'] ?? 'EFECTIVO'; ?></td>
                                <td class="text-end">S/ <?php echo number_format($v['total'], 2); ?></td>
                                <td><?php echo (($v['estado'] ?? 1) == 1) ? '<span class="badge-success">EMITIDO</span>' : '<span class="badge-danger">ANULADO</span>'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr class="total-row"><td colspan="4" class="text-end"><strong>TOTAL</strong></td><td class="text-end"><strong>S/ <?php echo number_format($total_ventas, 2); ?></strong></td><td></td></tr></tfoot>
                    </table>
                </div>
                
                <!-- GASTOS -->
                <div class="section">
                    <div class="section-title">💸 REGISTRO DE GASTOS</div>
                    <table>
                        <thead><tr><th>Fecha</th><th>Concepto</th><th class="text-end">Monto</th></tr></thead>
                        <tbody>
                            <?php foreach($gastos_detallados as $g): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($g['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($g['descripcion']); ?></td>
                                <td class="text-end text-danger">- S/ <?php echo number_format($g['monto'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr><td colspan="2" class="text-end"><strong>TOTAL GASTOS</strong></td><td class="text-end text-danger"><strong>- S/ <?php echo number_format($total_gastos, 2); ?></strong></td></tr></tfoot>
                    </table>
                </div>
                
                <div class="footer">
                    <p>Reporte generado el <?php echo date('d/m/Y H:i:s'); ?> - Sistema de Gestión Lubricentro Ismar</p>
                </div>
            </div>
            <script>window.print();</script>
        </body>
        </html>
        <?php
    }

}
?>