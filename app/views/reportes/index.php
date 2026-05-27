<?php 
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif;">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-5" style="color: #1E3A5F; font-weight: 700;">
                <i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Auditoría y Reportes Gerenciales
            </h2>
            <small class="text-muted">Balance consolidado de movimientos, ingresos comerciales y cierres de caja chica.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="formReportes" formaction="index.php?route=exportar_reporte_excel" class="btn btn-sm btn-success fw-bold px-3">
                <i class="bi bi-file-earmark-excel-fill"></i> Exportar Excel
            </button>
            <button onclick="imprimirReporte()" class="btn btn-sm btn-primary fw-bold px-3">
                <i class="bi bi-printer-fill"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- ========== FILTROS AVANZADOS ========== -->
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="index.php?route=reportes" method="POST" class="row g-3" id="formReportes">
                <div class="col-12 col-md-3">
                    <label class="form-label text-dark fw-semibold small">Fecha Inicio:</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $fecha_inicio ?? date('Y-m-d'); ?>" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-dark fw-semibold small">Fecha Fin:</label>
                    <input type="date" class="form-control" name="fecha_fin" value="<?php echo $fecha_fin ?? date('Y-m-d'); ?>" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label text-dark fw-semibold small">Método Pago:</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        <option value="EFECTIVO" <?php echo ($metodo_pago ?? '') == 'EFECTIVO' ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="YAPE" <?php echo ($metodo_pago ?? '') == 'YAPE' ? 'selected' : ''; ?>>Yape / Plin</option>
                        <option value="TARJETA" <?php echo ($metodo_pago ?? '') == 'TARJETA' ? 'selected' : ''; ?>>Tarjeta</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label text-dark fw-semibold small">&nbsp;</label>
                    <button type="submit" class="btn btn-primary fw-bold w-100">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label text-dark fw-semibold small">&nbsp;</label>
                    <button type="button" class="btn btn-secondary fw-bold w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-eraser-fill me-1"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== KPI CARDS ========== -->
    <div class="row g-4 mb-4" id="reporteImprimible">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #2563EB;">
                <div class="text-muted small fw-bold text-uppercase">Total Ventas</div>
                <div class="fs-3 fw-bold text-dark mt-1">S/ <?php echo number_format($total_ventas, 2); ?></div>
                <div class="small text-success mt-1">Ingresos brutos del período</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #EF4444;">
                <div class="text-muted small fw-bold text-uppercase">Total Gastos</div>
                <div class="fs-3 fw-bold text-danger mt-1">S/ <?php echo number_format($total_gastos, 2); ?></div>
                <div class="small text-muted mt-1">Salidas de caja</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #10B981; background-color: #F0FDF4;">
                <div class="text-muted small fw-bold text-uppercase">Utilidad Neta</div>
                <div class="fs-3 fw-bold mt-1 <?php echo ($total_ventas - $total_gastos) >= 0 ? 'text-success' : 'text-danger'; ?>">
                    S/ <?php echo number_format($total_ventas - $total_gastos, 2); ?>
                </div>
                <div class="small text-muted mt-1">Rentabilidad real</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #8B5CF6;">
                <div class="text-muted small fw-bold text-uppercase">Transacciones</div>
                <div class="fs-3 fw-bold text-dark mt-1"><?php echo count($ventas_detalladas); ?></div>
                <div class="small text-muted mt-1">Órdenes procesadas</div>
            </div>
        </div>
    </div>

    <!-- ========== TURNOS DE CAJA ========== -->
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
        <div class="card-header bg-light py-3 fw-bold text-uppercase border-0" style="color: #1E3A5F;">
            <i class="bi bi-safe2-fill text-primary me-1"></i> Control de Turnos y Aperturas de Caja
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr><th>ID</th><th>Cajero</th><th>Apertura</th><th>Monto Inicial</th><th>Cierre</th><th>Monto Final</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($turnos_caja)): ?>
                        <tr><td colspan="7" class="text-center py-4">No hay turnos registrados</td></tr>
                    <?php else: foreach($turnos_caja as $tc): ?>
                        <tr>
                            <td>#<?php echo $tc['id']; ?></td>
                            <td><?php echo htmlspecialchars($tc['cajero_nombre'] ?? 'Cajero'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($tc['fecha_apertura'])); ?></td>
                            <td>S/ <?php echo number_format($tc['monto_apertura'] ?? 0, 2); ?></td>
                            <td><?php echo !empty($tc['fecha_cierre']) ? date('d/m/Y H:i', strtotime($tc['fecha_cierre'])) : '---'; ?></td>
                            <td class="fw-bold">S/ <?php echo number_format($tc['monto_final'] ?? 0, 2); ?></td>
                            <td><?php echo empty($tc['fecha_cierre']) ? '<span class="badge bg-warning">ABIERTO</span>' : '<span class="badge bg-success">CERRADO</span>'; ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========== VENTAS DETALLADAS ========== -->
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
        <div class="card-header bg-light py-3 fw-bold text-uppercase border-0" style="color: #1E3A5F;">
            <i class="bi bi-cart-check-fill text-success me-1"></i> Órdenes de Servicio
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light">
                    <tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Placa</th><th>Método</th><th class="text-end">Total</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($ventas_detalladas)): ?>
                        <tr><td colspan="7" class="text-center py-4">No hay ventas registradas</td></tr>
                    <?php else: foreach($ventas_detalladas as $v): ?>
                        <tr>
                            <td>#<?php echo $v['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></td>
                            <td><?php echo htmlspecialchars($v['cliente_nombre'] ?? 'Público General'); ?></td>
                            <td><?php echo strtoupper($v['placa'] ?? 'S/P'); ?></td>
                            <td><?php echo $v['metodo_pago'] ?? 'EFECTIVO'; ?></td>
                            <td class="text-end fw-bold">S/ <?php echo number_format($v['total'], 2); ?></td>
                            <td><?php echo (($v['estado'] ?? 1) == 0) ? '<span class="badge bg-danger">ANULADO</span>' : '<span class="badge bg-success">EMITIDO</span>'; ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold"><td colspan="5" class="text-end">TOTAL:</td><td class="text-end">S/ <?php echo number_format($total_ventas ?? 0, 2); ?></td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ========== PRODUCTOS Y SERVICIOS ========== -->


    <!-- ========== GASTOS ========== -->
    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
        <div class="card-header bg-light py-3 fw-bold text-uppercase border-0" style="color: #1E3A5F;">
            <i class="bi bi-receipt-cutoff text-danger me-1"></i> Detalle de Gastos
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light"><tr><th>Fecha</th><th>Concepto</th><th class="text-end">Monto</th></tr></thead>
                <tbody>
                    <?php if(empty($gastos_detallados)): ?><tr><td colspan="3" class="text-center">No hay gastos registrados</td></tr>
                    <?php else: foreach($gastos_detallados as $g): ?>
                        <tr><td><?php echo date('d/m/Y H:i', strtotime($g['fecha'])); ?></td><td><?php echo htmlspecialchars($g['descripcion']); ?></td><td class="text-end text-danger">- S/ <?php echo number_format($g['monto'], 2); ?></td></tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot class="table-light"><tr class="fw-bold"><td colspan="2" class="text-end">TOTAL GASTOS:</td><td class="text-end text-danger">- S/ <?php echo number_format($total_gastos ?? 0, 2); ?></td></tr></tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function exportarReporteExcel() {
    const form = document.getElementById('formReportes');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = 'index.php?route=exportar_reporte_excel&' + params.toString();
}

function imprimirReporte() {
    // Obtener las fechas
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]')?.value || '<?php echo $fecha_inicio; ?>';
    const fechaFin = document.querySelector('input[name="fecha_fin"]')?.value || '<?php echo $fecha_fin; ?>';
    
    // Abrir ventana con la misma URL pero con parámetro print=1
    const url = `index.php?route=reportes&print=1&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&metodo_pago=${document.querySelector('select[name="metodo_pago"]')?.value || ''}`;
    window.open(url, '_blank');
}

function limpiarFiltros() {
    window.location.href = 'index.php?route=reportes';
}
</script>

<style media="print">
    .btn, form, .sidebar, .header, .no-print { display: none !important; }
    body { background: white; padding: 0; margin: 0; }
    .card { border: 1px solid #ccc; box-shadow: none; }
</style>

<?php 
require_once '../app/views/includes/footer.php'; 
?>