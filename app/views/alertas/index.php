<?php 
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC;">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="mb-0 text-uppercase fs-5" style="color: #1E3A5F;">
                <i class="bi bi-whatsapp text-success me-2"></i> Fidelización y Alertas Preventivas
            </h2>
            <small class="text-muted">Control de mantenimiento por tiempo transcurrido</small>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="bi bi-sliders me-1 text-primary"></i> Plazo de Alerta</h6>
                    <form action="index.php?route=alertas" method="POST" class="d-flex gap-2">
                        <input type="number" class="form-control text-center" name="dias_alerta_input" value="<?php echo $limite_dias; ?>" min="1" style="max-width: 80px;">
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </form>
                    <small class="text-muted">Días para cambio de aceite</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="bi bi-pencil-fill text-success me-1"></i> Mensaje WhatsApp</h6>
                    <textarea class="form-control font-monospace" id="txtPlantilla" rows="2" style="font-size: 12px;"><?php echo $mensaje_predeterminado; ?></textarea>
                    <small class="text-muted">Tags: [CLIENTE], [PLACA]</small>
                </div>
            </div>
        </div>

        <?php if (!empty($filtro)): ?>
            <div class="alert alert-info alert-sm mb-3" style="border-radius: 8px;">
                <i class="bi bi-funnel-fill me-2"></i> 
                Mostrando: 
                <?php 
                switch($filtro) {
                    case 'pendientes': echo '<strong>📋 Pendientes</strong> (menos de ' . $limite_dias . ' días)'; break;
                    case 'vencidos': echo '<strong>🔴 Vencidos</strong> (más de ' . $limite_dias . ' días)'; break;
                    case 'criticos': echo '<strong>🚨 Críticos</strong> (más de 45 días)'; break;
                    default: echo '<strong>Todos los vehículos</strong>';
                }
                ?>
                <a href="index.php?route=alertas" class="float-end text-decoration-none">✖ Limpiar filtro</a>
            </div>
        <?php endif; ?>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="bi bi-funnel-fill me-1 text-primary"></i> Filtrar Alertas</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="index.php?route=alertas" class="btn btn-sm btn-outline-primary <?php echo !isset($_GET['filtro']) || $_GET['filtro'] == '' ? 'active' : ''; ?>">
                            📋 Todos
                        </a>
                        <a href="index.php?route=alertas&filtro=pendientes" class="btn btn-sm btn-outline-warning <?php echo ($_GET['filtro'] ?? '') == 'pendientes' ? 'active' : ''; ?>">
                            ⏳ Pendientes (&lt;30 días)
                        </a>
                        <a href="index.php?route=alertas&filtro=vencidos" class="btn btn-sm btn-outline-danger <?php echo ($_GET['filtro'] ?? '') == 'vencidos' ? 'active' : ''; ?>">
                            🔴 Vencidos (&gt;30 días)
                        </a>
                        <a href="index.php?route=alertas&filtro=criticos" class="btn btn-sm btn-outline-dark <?php echo ($_GET['filtro'] ?? '') == 'criticos' ? 'active' : ''; ?>">
                            🚨 Críticos (&gt;45 días)
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-alertas" id="tablaAlertas">
                    <thead>
                        <tr>
                            <th>🚗 Placa</th>
                            <th>👤 Cliente / Teléfono</th>
                            <th>📅 Último Servicio</th>
                            <th>⏱️ Días</th>
                            <th>🟢 Estado</th>
                            <th>⚡ Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lista_general)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                    No hay vehículos registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lista_general as $a): ?>
                                <tr>
                                    <td>
                                        <?php if ($a['placa'] != 'S/P' && $a['placa'] != '---'): ?>
                                            <span class="badge-placa">🚗 <?php echo strtoupper($a['placa']); ?></span>
                                        <?php else: ?>
                                            <span class="badge-sin-placa">❌ Sin placa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="cliente-nombre">
                                            <i class="bi bi-person-circle text-primary"></i> 
                                            <?php echo htmlspecialchars($a['cliente_nombre']); ?>
                                        </div>
                                        <div class="cliente-telefono">
                                            <i class="bi bi-whatsapp text-success"></i> 
                                            <?php echo $a['cliente_telefono'] ?? '---'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fecha-servicio">
                                            <i class="bi bi-calendar3 text-muted"></i> 
                                            <?php echo date('d/m/Y', strtotime($a['fecha'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="dias-numero"><?php echo $a['dias_transcurridos']; ?></span>
                                        <span class="dias-texto">días</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($a['semaforo'] == 'success'): ?>
                                            <span class="semaforo-success">
                                                <i class="bi bi-check-circle-fill me-1"></i> <?php echo $a['estado_texto']; ?>
                                            </span>
                                        <?php elseif ($a['semaforo'] == 'warning'): ?>
                                            <span class="semaforo-warning">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $a['estado_texto']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="semaforo-danger">
                                                <i class="bi bi-x-circle-fill me-1"></i> <?php echo $a['estado_texto']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn-whatsapp" onclick="enviarWhatsApp('<?php echo $a['cliente_telefono']; ?>', '<?php echo htmlspecialchars($a['cliente_nombre']); ?>', '<?php echo $a['placa']; ?>', <?php echo $a['id_venta']; ?>)">
                                                <i class="bi bi-whatsapp"></i> Notificar
                                            </button>
                                            <a href="index.php?route=descartar_alerta&id=<?php echo $a['id_venta']; ?>" class="btn-descartar" onclick="return confirm('¿Descartar esta alerta?')">
                                                <i class="bi bi-x-circle"></i> Descartar
                                            </a>
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
</div>
<style>
/* Mejoras para la tabla de alertas */
.table-alertas {
    font-size: 13px;
    background: white;
}

.table-alertas thead th {
    background: #1E3A5F !important;
    color: white !important;
    font-weight: 600;
    padding: 12px 10px;
    border-bottom: none;
}

.table-alertas tbody tr {
    border-bottom: 1px solid #E2E8F0;
    transition: all 0.2s;
}

.table-alertas tbody tr:hover {
    background-color: #F8FAFC;
}

.table-alertas td {
    padding: 12px 10px;
    vertical-align: middle;
    color: #1E293B;
}

.badge-placa {
    background-color: #2563EB;
    color: white;
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 13px;
    display: inline-block;
    min-width: 100px;
    text-align: center;
}

.badge-sin-placa {
    background-color: #94A3B8;
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-block;
}

.cliente-nombre {
    font-weight: 600;
    color: #1E3A5F;
}

.cliente-telefono {
    font-size: 11px;
    color: #64748B;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
}

.cliente-telefono i {
    font-size: 11px;
}

.fecha-servicio {
    font-size: 12px;
    color: #475569;
}

.dias-numero {
    font-weight: 700;
    font-size: 18px;
    color: #1E293B;
}

.dias-texto {
    font-size: 10px;
    color: #64748B;
    display: block;
}

.semaforo-success {
    background-color: #D1FAE5;
    color: #065F46;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.semaforo-warning {
    background-color: #FEF3C7;
    color: #92400E;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.semaforo-danger {
    background-color: #FEE2E2;
    color: #991B1B;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.btn-whatsapp {
    background-color: #25D366;
    border: none;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-whatsapp:hover {
    background-color: #128C7E;
    color: white;
}

.btn-descartar {
    background-color: transparent;
    border: 1px solid #CBD5E1;
    color: #64748B;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    transition: all 0.2s;
}

.btn-descartar:hover {
    background-color: #F1F5F9;
    border-color: #94A3B8;
}

/* Colores de fondo para filas (alternado) */
.table-alertas tbody tr:nth-child(even) {
    background-color: #F8FAFC;
}
</style>
<script>
function enviarWhatsApp(telefono, cliente, placa, idVenta) {
    let texto = document.getElementById("txtPlantilla").value;
    texto = texto.replace("[CLIENTE]", cliente).replace("[PLACA]", placa);
    let url = "https://api.whatsapp.com/send?phone=51" + telefono + "&text=" + encodeURIComponent(texto);
    window.open(url, '_blank');
    setTimeout(() => { window.location.href = "index.php?route=marcar_notificado&id=" + idVenta; }, 2000);
}
$(document).ready(function() {
    // DataTables desactivado para evitar warnings
    // Solo mostramos el número de registros
    let totalRegistros = $('#tablaAlertas tbody tr').length;
    if (totalRegistros > 0) {
        $('.card-body').prepend('<div class="alert alert-info small mb-3">📊 Total de vehículos: <strong>' + totalRegistros + '</strong></div>');
    }
});
</script>

<?php require_once '../app/views/includes/footer.php'; ?>