<?php 
// Incluimos el header global y el sidebar de tu aplicación
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-wrench-adjustable me-2 text-primary"></i> Catálogo de Servicios
            </h2>
            <small class="text-muted">Tarifas estándar asignadas a las operaciones técnicas, lavados, mantenimientos y cambios de fluido.</small>
        </div>
        <div>
            <a href="index.php?route=nuevo_servicio" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Registrar Servicio
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msgText = "Operación procesada con éxito.";
                <?php if($_GET['ok'] == 1): ?> msgText = "¡Servicio técnico registrado correctamente en el catálogo global!"; <?php endif; ?>
                <?php if($_GET['ok'] == 2): ?> msgText = "¡Tarifa base y especificaciones actualizadas con éxito!"; <?php endif; ?>
                <?php if($_GET['ok'] == 3): ?> msgText = "¡El servicio ha sido removido del catálogo de operaciones!"; <?php endif; ?>
                
                Swal.fire({
                    icon: 'success',
                    title: 'Catálogo de Servicios',
                    text: msgText,
                    confirmButtonColor: '#2563EB'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaServicios" class="table align-middle mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3" style="width: 80px;">ID</th>
                        <th class="py-3">Servicio / Mantenimiento Técnico</th>
                        <th class="py-3">Descripción de Tareas Incluidas</th>
                        <th class="py-3" style="width: 180px;">Precio Base (S/)</th>
                        <th class="py-3 text-center pe-3" style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($servicios)): ?>
                        <?php foreach($servicios as $s): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">#<?php echo $s['id_servicio']; ?></td>
                                <td class="fw-bold" style="color: #1E3A5F; font-size: 14px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="profile-avatar bg-light text-primary border-0 fs-6" style="width: 32px; height: 32px;">
                                            <i class="bi bi-gear-fill" style="font-size: 14px;"></i>
                                        </div>
                                        <span><?php echo htmlspecialchars($s['nombre']); ?></span>
                                    </div>
                                </td>
                                <td class="text-muted text-truncate-2" style="font-size: 13px; max-width: 350px;" title="<?php echo htmlspecialchars($s['descripcion']); ?>">
                                    <?php echo htmlspecialchars($s['descripcion'] ?: 'Sin especificaciones añadidas por el mecánico.'); ?>
                                </td>
                                <td class="fw-bold text-dark fs-6">
                                    S/ <?php echo number_format($s['precio'], 2, '.', ','); ?>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=editar_servicio&id=<?php echo $s['id_servicio']; ?>" class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Editar Tarifa Base" style="font-size: 16px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Dar de baja del catálogo" style="font-size: 16px;" onclick="confirmarBajaServicio(<?php echo $s['id_servicio']; ?>, '<?php echo htmlspecialchars($s['nombre']); ?>')">
                                            <i class="bi bi-trash3-fill"></i>
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

<script>
$(document).ready(function() {
    $('#tablaServicios').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            // Ajustamos las cajas nativas de búsqueda para que hereden el diseño ERP Claro
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar servicio...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

// Confirmación asíncrona mediante SweetAlert2 para anular un servicio del catálogo
function confirmarBajaServicio(id, nombre) {
    Swal.fire({
        title: '¿Retirar del catálogo?',
        text: `Se dará de baja el servicio "${nombre}". Esta acción no alterará los montos ni registros históricos de las ventas previas.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444', // Rojo Error del manual
        cancelButtonColor: '#64748B',  // Gris Secundario del manual
        confirmButtonText: 'Sí, retirar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_servicio&id=${id}`;
        }
    });
}
</script>

<style>
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: normal;
}
</style>