<?php 
// Incluimos la estructura global y de navegación adaptada de tu ERP
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-lock-fill me-2 text-primary"></i> Cierre de Caja y Arqueo
            </h2>
            <small class="text-muted">Rendición de cuentas, consolidación de métodos de pago y arqueo de dinero físico al finalizar el turno.</small>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card-erp h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark text-uppercase tracking-wider mb-0" style="font-size: 13px;">
                        <i class="bi bi-pie-chart-fill text-primary me-2"></i>Resumen Financiero del Turno
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1 small">
                        Apertura: <?php echo date("d/m/Y H:i", strtotime($caja['fecha_apertura'])); ?>
                    </span>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <span class="text-muted small fw-medium">💰 Monto Inicial (Sencillo):</span>
                        <span class="fw-bold text-dark">S/ <?php echo number_format($caja['monto_apertura'] ?? $caja['monto_inicial'] ?? 0, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <span class="text-success small fw-medium"><i class="bi bi-cash me-1"></i> + Ventas Efectivo:</span>
                        <span class="fw-bold text-success">S/ <?php echo number_format($totales['venta_efectivo'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <span class="text-info small fw-medium"><i class="bi bi-qr-code me-1"></i> + Ventas Yape / Plin:</span>
                        <span class="fw-bold text-info">S/ <?php echo number_format($totales['venta_yape'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <span class="text-warning small fw-medium"><i class="bi bi-credit-card me-1"></i> + Ventas Tarjeta:</span>
                        <span class="fw-bold style-warning" style="color: #F59E0B;">S/ <?php echo number_format($totales['venta_tarjeta'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <span class="text-danger small fw-medium"><i class="bi bi-dash-circle me-1"></i> − Gastos / Salidas del Turno:</span>
                        <span class="fw-bold text-danger">− S/ <?php echo number_format($total_gastos, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light bg-light px-2 rounded my-1">
                        <span class="text-secondary small fw-bold"><i class="bi bi-receipt me-1"></i> Total Tickets Emitidos:</span>
                        <span class="fw-bold text-dark"><?php echo $totales['cantidad_tickets']; ?> órdenes</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light px-2">
                        <span class="text-muted small fw-medium">📦 Volumen Total Facturado (Bruto):</span>
                        <span class="fw-bold text-dark">S/ <?php echo number_format($totales['venta_total'], 2); ?></span>
                    </div>
                </div>

                <div class="alert alert-success bg-light border-success text-center py-3 mb-0 shadow-sm" style="border-radius: 12px;">
                    <div class="text-uppercase tracking-wider text-muted fw-bold mb-1" style="font-size: 11px;">Efectivo Esperado en Cajón</div>
                    <h3 class="fw-bold mb-0 text-success" style="font-size: 26px;">S/ <?php echo number_format($total_esperado_en_cajon, 2); ?></h3>
                    <small class="text-muted" style="font-size: 11px;">Fórmula de Auditoría: (Monto Inicial + Efectivo − Gastos)</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
            
            <?php if(!empty($lista_gastos)): ?>
                <div class="card-erp py-3 px-3">
                    <h6 class="fw-bold text-danger text-uppercase tracking-wider mb-2" style="font-size: 12px;"><i class="bi bi-dash-circle-fill me-2"></i>Egresos Registrados</h6>
                    <div class="overflow-auto" style="max-height: 120px;">
                        <ul class="list-group list-group-flush small">
                            <?php foreach($lista_gastos as $g): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 bg-transparent border-light">
                                    <span class="text-truncate text-dark" style="max-width: 180px;" title="<?php echo htmlspecialchars($g['descripcion']); ?>">
                                        <?php echo htmlspecialchars($g['descripcion']); ?>
                                    </span>
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="text-danger fw-semibold">− S/ <?php echo number_format($g['monto'], 2); ?></span>
                                        <a href="index.php?route=eliminar_gasto&id=<?php echo $g['id']; ?>" 
                                           onclick="return confirm('¿Retirar este registro de salida?')"
                                           class="btn btn-sm btn-outline-danger border-0 p-0 px-1" style="font-size: 14px;"><i class="bi bi-x-circle-fill"></i></a>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card-erp py-2 px-3 bg-light border">
                <form action="index.php?route=guardar_gasto" method="POST" class="row g-2">
                    <div class="col-4">
                        <input type="number" step="0.01" name="monto" class="form-control-erp form-control-sm py-1 px-2 w-100" placeholder="Monto S/" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="descripcion" class="form-control-erp form-control-sm py-1 px-2 w-100" placeholder="Gasto de último minuto..." required>
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-erp-warning w-100 btn-sm py-1" style="background-color: #F59E0B;"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
            </div>

            <div class="card-erp flex-grow-1" style="border-top: 4px solid #EF4444;">
                <form action="index.php?route=guardar_cierre" method="POST" id="formCierreCaja">
                    <input type="hidden" name="id_caja" value="<?php echo $caja['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="monto_final" class="label-erp text-danger text-uppercase tracking-wider fw-bold" style="font-size: 11px;">
                            <i class="bi bi-cash-coin me-1"></i> Dinero Físico Real Encontrado en Caja
                        </label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px;">
                            <span class="input-group-text bg-light border-end-0 text-muted fw-bold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                            <input type="number" step="0.01" min="0.00" name="monto_final" id="monto_final" class="form-control-erp flex-grow-1 fw-bold text-dark text-end fs-4" style="border-radius: 0 10px 10px 0; padding-right: 15px;" required placeholder="0.00" oninput="calcularDiferenciaArqueo()">
                        </div>
                    </div>

                    <div id="diferencia_box" class="alert d-none py-2 px-3 mb-3" style="border-radius: 10px; font-size: 13.5px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-medium">Balance de Arqueo:</span>
                            <strong id="diferencia_txt">S/ 0.00</strong>
                        </div>
                    </div>

                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <a href="index.php?route=dashboard" class="btn-erp-secondary text-center text-white text-decoration-none d-flex align-items-center justify-content-center w-100" style="height: 44px; border-radius: 8px;">
                                Cancelar
                            </a>
                        </div>
                        <div class="col-8">
                            <button type="button" onclick="procesarValidacionSweet()" class="btn btn-erp-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="background-color: #EF4444; height: 44px;">
                                <i class="bi bi-door-closed-fill"></i> Liquidar y Cerrar Caja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const montoEsperado = <?php echo $total_esperado_en_cajon; ?>;

function calcularDiferenciaArqueo() {
    const ingresado = parseFloat(document.getElementById('monto_final').value) || 0;
    const dif = ingresado - montoEsperado;
    const box = document.getElementById('diferencia_box');
    const txt = document.getElementById('diferencia_txt');
    
    box.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'text-success', 'text-danger');
    
    if (Math.abs(dif) < 0.01) {
        box.classList.add('alert-success');
        txt.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Caja Cuadrada';
    } else if (dif > 0) {
        box.classList.add('alert-warning');
        txt.innerHTML = `<i class="bi bi-plus-circle-fill me-1"></i> Sobrante: S/ ${dif.toFixed(2)}`;
    } else {
        box.classList.add('alert-danger');
        txt.innerHTML = `<i class="bi bi-dash-circle-fill me-1"></i> Faltante: S/ ${Math.abs(dif).toFixed(2)}`;
    }
}

function procesarValidacionSweet() {
    const ingresado = parseFloat(document.getElementById('monto_final').value) || 0;
    
    if (document.getElementById('monto_final').value === "") {
        Swal.fire({ icon: 'error', title: 'Monto requerido', text: 'Por favor, ingrese la cantidad de dinero físico encontrado en el cajón antes de cerrar.', confirmButtonColor: '#2563EB' });
        return;
    }

    const dif = ingresado - montoEsperado;
    let swalTitle = '¿Confirmar cierre de caja?';
    let swalText = 'Al confirmar se dará por concluido el turno y se bloqueará el registro de nuevas ventas.';
    let swalIcon = 'question';
    let confirmColor = '#2563EB';

    if (Math.abs(dif) >= 0.01) {
        swalTitle = '¡Alerta de Descuadre!';
        swalIcon = 'warning';
        confirmColor = '#F59E0B';
        swalText = `Existe un ${dif > 0 ? 'sobrante' : 'faltante'} de S/ ${Math.abs(dif).toFixed(2)} respecto al balance del sistema. ¿Deseas proceder con el cierre auditado?`;
    }

    Swal.fire({
        title: swalTitle,
        text: swalText,
        icon: swalIcon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Sí, cerrar turno',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCierreCaja').submit();
        }
    });
}
</script>

<?php require_once '../app/views/includes/footer.php'; ?>