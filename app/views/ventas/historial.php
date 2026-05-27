<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif;">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-clock-history me-2 text-primary"></i> Historial de Ventas y CPE
            </h2>
            <small class="text-muted">Registro centralizado de órdenes de servicio facturadas, comprobantes electrónicos y estados SUNAT.</small>
        </div>
        <div class="text-end">
            <a href="index.php?route=nueva_venta" class="btn btn-primary fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm mb-2" style="background-color: #2563EB; border-color: #2563EB; border-radius: 8px; text-decoration: none; color: white;">
                <i class="bi bi-cart-plus-fill"></i> Nueva Operación
            </a><br>
            <a href="index.php?route=exportar_ventas_excel" class="btn btn-success fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 14px; background-color: #10B981; border-color: #10B981; text-decoration: none; color: white; padding: 6px 12px;">
                <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
        <div class="card-body p-3.5">
            <form action="index.php?route=historial_ventas" method="GET" class="row align-items-end g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label text-dark fw-semibold small mb-1"><i class="bi bi-calendar-event text-primary me-1"></i> Desde el Día:</label>
                    <input type="date" class="form-control fw-medium" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required style="height: 40px; border-radius: 8px;">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-dark fw-semibold small mb-1"><i class="bi bi-calendar-check text-success me-1"></i> Hasta el Día:</label>
                    <input type="date" class="form-control fw-medium" name="fecha_fin" value="<?php echo $fecha_fin; ?>" required style="height: 40px; border-radius: 8px;">
                </div>
                <div class="col-12 col-md-4">
                    <input type="hidden" name="route" value="historial_ventas">
                    <button type="submit" class="btn btn-dark fw-bold w-100 shadow-sm" style="height: 40px; border-radius: 8px;">
                        <i class="bi bi-search me-1"></i> Filtrar Historial
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-white p-3" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
        <div class="table-responsive">
            <table id="tablaHistorialVentas" class="table align-middle mb-0" style="width: 100%; font-size: 14px;">
                <thead class="table-light" style="border-bottom: 2px solid #E2E8F0;">
                    <tr style="color: #475569; font-weight: 600;">
                        <th class="py-3 ps-3" style="width: 70px;">ID</th>
                        <th class="py-3">Fecha / Hora</th>
                        <th class="py-3">Cliente / Razón Social</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Método Pago</th>
                        <th class="py-3 text-end">Monto Total</th>
                        <th class="py-3 text-center">Estado de Orden</th>
                        <th class="py-3 text-center pe-3" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($ventas)): ?>
                        <?php foreach($ventas as $v): ?>
                            <tr class="<?php echo ($v['estado'] == 0) ? 'table-danger opacity-75 text-decoration-line-through' : ''; ?>">
                                <td class="ps-3 fw-bold text-secondary">#<?php echo $v['id']; ?></td>
                                <td class="text-muted" style="font-size: 13px;">
                                    <?php echo date("d/m/Y H:i", strtotime($v['fecha'])); ?>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #1E3A5F; font-size: 13.5px;">
                                        <?php echo htmlspecialchars($v['cliente_nombre'] ?? 'Público General'); ?>
                                    </div>
                                    <small class="text-muted font-monospace" style="font-size: 11px;">Doc: <?php echo htmlspecialchars($v['cliente_num_doc'] ?? '-'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-medium px-2 py-1" style="font-size: 11px;">
                                        <?php echo ($v['tipo_comprobante'] == '01') ? 'FAV / FACTURA' : 'BOL / BOLETA'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $coloresPago = ['EFECTIVO'=>'success','YAPE'=>'info','TARJETA'=>'warning','PLIN'=>'info'];
                                    $metodo_clean = strtoupper(trim($v['metodo_pago']));
                                    $cPago = $coloresPago[$metodo_clean] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-light text-<?php echo $cPago; ?> border border-<?php echo $cPago; ?>" style="font-size: 11px; font-weight: 600;">
                                        <?php echo $metodo_clean; ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark fs-6">
                                    S/ <?php echo number_format($v['total'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <?php if(($v['estado'] ?? 1) == 0): ?>
                                        <span class="badge bg-danger px-3 py-1 fw-bold">ANULADO</span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white px-3 py-1 fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i>EMITIDO
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=ver_ticket&id=<?php echo $v['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Imprimir Ticket A4 / Térmico" style="font-size: 16px;">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                        
                                        <?php if($v['estado'] == 1): ?>
                                            <a href="#" class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Anular Orden y Devolver Stock" style="font-size: 16px;" onclick="confirmarAnulacionVenta(<?php echo $v['id']; ?>, '<?php echo htmlspecialchars($v['cliente_nombre'] ?? 'Público General'); ?>', '<?php echo number_format($v['total'], 2); ?>')">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary border-0 text-muted" disabled style="font-size: 16px;">
                                                <i class="bi bi-lock-fill opacity-25"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#tablaHistorialVentas').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "desc" ]],
        "pageLength": 10,
        "columnDefs": [
            { "orderData": [0], "targets": [0] }
        ],
        "drawCallback": function() {
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar por cliente...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

function confirmarAnulacionVenta(id, cliente, monto) {
    Swal.fire({
        title: '¿Anular este comprobante?',
        text: `Se dará de baja la orden #${id} del cliente "${cliente}" por S/ ${monto}. Esta acción reincorporará los aceites y filtros de vuelta al stock del almacén de forma automática.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Sí, proceder a anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=anular_venta&id=${id}`;
        }
    });
}
</script>

<style>
.hover-primary:hover { color: #2563EB !important; }
</style>
<?php require_once '../app/views/includes/footer.php'; ?>