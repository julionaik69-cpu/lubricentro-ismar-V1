<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor">

    <?php if(isset($_GET['cerrada']) && $_GET['cerrada']=='1'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2 text-success"></i> ✅ Caja cerrada exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['msg']) && $_GET['msg']=='caja_abierta'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="bi bi-unlock-fill me-2 text-success"></i> ✅ Caja aperturada. ¡Buenas ventas!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Operativo
            </h2>
            <small class="text-muted">Resumen analítico de ventas, stock crítico e historial de mantenimiento vehicular.</small>
        </div>
        <div class="badge bg-white text-dark border p-2 rounded-pill shadow-sm text-muted">
            <i class="bi bi-calendar-event me-1 text-primary"></i> <?php echo date("d/m/Y H:i"); ?>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card-erp d-flex align-items-center" style="border-left: 4px solid #2563EB;">
                <div class="flex-grow-1">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Ventas Hoy</div>
                    <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 22px;">S/ <?php echo number_format($ventas_hoy ?? 0, 2); ?></h3>
                </div>
                <div class="profile-avatar fs-4" style="background-color: #EFF6FF; color: #2563EB; border: none; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card-erp d-flex align-items-center" style="border-left: 4px solid #10B981;">
                <div class="flex-grow-1">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Insumos en Catálogo</div>
                    <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 22px;"><?php echo $total_productos ?? 0; ?></h3>
                </div>
                <div class="profile-avatar fs-4" style="background-color: #E6F4EA; color: #10B981; border: none; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card-erp d-flex align-items-center" style="border-left: 4px solid #EF4444;">
                <div class="flex-grow-1">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Stock Crítico</div>
                    <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 22px;"><?php echo $stock_bajo ?? 0; ?></h3>
                    <?php if(($stock_bajo ?? 0) > 0): ?>
                        <a href="index.php?route=productos" class="text-danger small d-block mt-1"><u>Revisar Almacén</u></a>
                    <?php endif; ?>
                </div>
                <div class="profile-avatar fs-4" style="background-color: #FCE8E6; color: #EF4444; border: none; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card-erp d-flex align-items-center" style="border-left: 4px solid #F59E0B;">
                <div class="flex-grow-1">
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Tickets Hoy</div>
                    <h3 class="mb-0 mt-1 fw-bold text-dark" style="font-size: 22px;"><?php echo $tickets_hoy ?? 0; ?></h3>
                </div>
                <div class="profile-avatar fs-4" style="background-color: #FEF3C7; color: #F59E0B; border: none; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-erp mb-4">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="fw-bold me-2 text-dark" style="font-size: 14px;">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Acceso Directo:
            </span>
            <a href="index.php?route=nueva_venta" class="btn btn-success px-3 py-2" style="border-radius:10px;">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta / Orden
            </a>
            <a href="index.php?route=historial_ventas" class="btn text-white px-3 py-2" style="background-color: #1E3A5F; border-radius:10px;">
                <i class="bi bi-receipt me-1"></i> Ver Historial
            </a>
            
            <?php if(isset($_SESSION['user_rol']) && in_array(strtoupper($_SESSION['user_rol']), ['ADMIN', 'ADMINISTRADOR'])): ?>
                <a href="index.php?route=reportes" class="btn btn-primary px-3 py-2" style="border-radius:10px;">
                    <i class="bi bi-bar-chart me-1"></i> Ver Reportes
                </a>
                <a href="index.php?route=config_empresa" class="btn btn-outline-secondary btn-sm px-3 py-2" style="border-radius:10px; border-color: #CBD5E1; color: #475569;">
                    <i class="bi bi-gear me-1"></i> Config SUNAT
                </a>
            <?php endif; ?>

            <button type="button" class="btn btn-danger px-3 py-2 ms-sm-auto" style="border-radius:10px;" data-bs-toggle="modal" data-bs-target="#modalGasto">
                <i class="bi bi-dash-circle me-1"></i> Registrar Gasto rápido
            </button>
        </div>
    </div>

    <div class="card-erp p-4 mb-4">
        <h5 class="text-uppercase text-dark fs-6 fw-bold mb-3" style="color: #1E3A5F !important;">
            <i class="bi bi-calendar-check text-primary me-2"></i> Tablero Semáforo: Control de Aceite por Tiempo
        </h5>
        <div class="row g-3 text-center">
            <div class="col-4">
                <div class="p-3 border rounded-3 bg-light">
                    <h2 class="text-danger fw-bold mb-1"><?php echo $total_vencidos ?? 0; ?></h2>
                    <span class="badge bg-danger mb-2">CRÍTICO</span>
                    <p class="text-muted small mb-0">Clientes con más de 30 días sin cambio.</p>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 border rounded-3 bg-light">
                    <h2 class="text-warning fw-bold mb-1"><?php echo $proximos_a_vencer ?? 0; ?></h2>
                    <span class="badge bg-warning text-dark mb-2">ADVERTENCIA</span>
                    <p class="text-muted small mb-0">Clientes en rango (15 a 30 días).</p>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 border rounded-3 bg-light">
                    <h2 class="text-success fw-bold mb-1"><?php echo $al_dia ?? 0; ?></h2>
                    <span class="badge bg-success mb-2">AL DÍA</span>
                    <p class="text-muted small mb-0">Mantenimientos recientes (<15 días).</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card-erp h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark text-uppercase tracking-wider mb-0" style="font-size: 13px;"><i class="bi bi-graph-up text-primary me-2"></i>Ventas de los últimos 7 días</h6>
                    <span class="badge bg-light text-dark border py-1 px-2 fw-semibold">S/ <?php echo number_format(array_sum(array_column($datos_grafico ?? [], 'total')), 2); ?> Total</span>
                </div>
                <div>
                    <?php if(!empty($datos_grafico)): ?>
                        <canvas id="graficoVentas" height="120"></canvas>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bar-chart fs-1 opacity-25"></i>
                            <p class="small mt-2 mb-0">Sin transacciones registradas esta semana.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card-erp h-100 p-0 overflow-hidden">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark text-uppercase tracking-wider mb-0" style="font-size: 13px;"><i class="bi bi-clock-history text-primary me-2"></i>Últimas Operaciones</h6>
                    <span class="badge bg-primary rounded-pill">Monitoreo</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small" style="font-size: 13px;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-3 py-2">Ticket</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2">Método</th>
                                <th class="text-end pe-3 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($ventas_recientes)): ?>
                                <?php foreach($ventas_recientes as $vr): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">
                                            <a href="index.php?route=ver_ticket&id=<?php echo $vr['id']; ?>" target="_blank" class="text-decoration-none text-primary">
                                                #<?php echo $vr['id']; ?>
                                            </a>
                                        </td>
                                        <td class="text-dark fw-medium"><?php echo htmlspecialchars($vr['cliente_nombre']); ?></td>
                                        <td><span class="badge bg-light text-secondary border px-2 py-1"><?php echo $vr['metodo_pago']; ?></span></td>
                                        <td class="text-end pe-3 fw-bold text-dark">S/ <?php echo number_format($vr['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        No se han registrado operaciones de caja en el turno de hoy.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-erp { background: #fff; border-radius: 16px; border: none; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
</style>


<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 360px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-danger text-white py-2 px-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h6 class="modal-title fw-bold text-uppercase" style="font-size: 13px;"><i class="bi bi-dash-circle me-2"></i>Registrar Salida de Efectivo</h6>
                <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?route=guardar_gasto" method="POST" autocomplete="off">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="label-erp">Monto de Salida (S/)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;">S/</span>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control-erp w-auto flex-grow-1" style="border-radius: 0 10px 10px 0;" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="label-erp">Justificación / Descripción</label>
                        <textarea name="descripcion" class="form-control-erp w-100" required rows="3" placeholder="Ej: Pago de almuerzo personal, flete de lubricantes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-end">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold"><i class="bi bi-dash-circle me-1"></i> Registrar Retiro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if(!empty($datos_grafico)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('graficoVentas');
    
    if (ctx) {
        // 1. Recorremos los datos puros que vienen de la base de datos
        const datosPHP = <?php echo json_encode($datos_grafico ?? []); ?>;
        
        // 2. LOGICA PARA DETALLAR Y ASIGNAR LOS ÚLTIMOS 7 DÍAS CONSECUTIVOS
        // Esto genera los 7 días anteriores de forma automática para que el gráfico no se vea vacío
        const etiquetas = [];
        const montos = [];
        
        for (let i = 6; i >= 0; i--) {
            const fechaBucle = new Date();
            fechaBucle.setDate(fechaBucle.getDate() - i);
            
            // Formato para comparar con la BD (YYYY-MM-DD)
            const formatoBD = fechaBucle.toISOString().split('T')[0];
            // Formato visual para mostrar abajo de la barra (DD/MM)
            const formatoVisual = fechaBucle.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit' });
            
            // Buscamos si hay una venta registrada en esta fecha específica
            const registroVenta = datosPHP.find(row => row.fecha === formatoBD);
            
            etiquetas.push(formatoVisual);
            // Si hay venta ponemos el total, si no, se queda en 0 de forma limpia
            montos.push(registroVenta ? parseFloat(registroVenta.total) : 0);
        }

        // 3. Renderizamos el gráfico con etiquetas de valores detallados
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Recaudado Diario',
                    data: montos,
                    backgroundColor: '#2563EB', // Azul corporativo
                    hoverBackgroundColor: '#1D4ED8', // Azul más oscuro al pasar el mouse
                    borderRadius: 8,
                    borderWidth: 0,
                    barPercentage: 0.6 // Hace las barras un poco más finas y elegantes
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    // DETALLE EXTRA: Configurar ventanas emergentes (Tooltips) flotantes descriptivos
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Total Vendido: S/ ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', family: 'Poppins' } }
                    },
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' }, // Líneas horizontales muy sutiles de guía
                        ticks: {
                            font: { family: 'Poppins' },
                            callback: function(value) { return 'S/ ' + value; }
                        }
                    }
                }
            }
        });
    }
});
</script>
<?php endif; ?>

<?php require_once '../app/views/includes/footer.php'; ?>