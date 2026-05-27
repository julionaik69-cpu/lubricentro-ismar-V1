<?php 
// Incluimos los componentes de estructura global adaptados
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-car-front-fill me-2 text-primary"></i> Control de Vehículos
            </h2>
            <?php if (!empty($clienteEspecifico)): ?>
                <small class="text-muted">
                    Mostrando las flotas y unidades vinculadas al cliente: 
                    <strong class="text-primary fw-semibold"><?php echo htmlspecialchars($clienteEspecifico['nombre']); ?></strong> 
                    (<?php echo htmlspecialchars($clienteEspecifico['tipo_documento'] . ': ' . $clienteEspecifico['numero_documento']); ?>)
                </small>
            <?php else: ?>
                <small class="text-muted">Historial general de unidades, odómetros e historial de mantenimiento preventivo.</small>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <?php if (!empty($clienteEspecifico)): ?>
                <a href="index.php?route=clientes" class="btn btn-erp-secondary px-3 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-circle-fill"></i> Volver a Clientes
                </a>
                <a href="index.php?route=nuevo_vehiculo&id_cliente=<?php echo $clienteEspecifico['id_cliente']; ?>" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2" style="background-color: #10B981;">
                    <i class="bi bi-plus-circle-fill"></i> Registrar Auto a este Cliente
                </a>
            <?php else: ?>
                <a href="index.php?route=nuevo_vehiculo" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Registrar Vehículo
                </a>
            <?php endif; ?>
            <a href="index.php?route=exportar_vehiculos_excel" class="btn btn-success fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 14px; background-color: #10B981; border-color: #10B981; color: white; text-decoration: none; padding: 8px 16px;">
                <i class="bi bi-file-earmark-excel-fill"></i> Descargar Reporte de Autos
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msgTxt = "Operación realizada.";
                <?php if($_GET['ok'] == 1): ?> msgTxt = "¡Vehículo registrado e incorporado correctamente a la flota!"; <?php endif; ?>
                <?php if($_GET['ok'] == 2): ?> msgTxt = "¡Ficha técnica y kilometraje actualizados correctamente!"; <?php endif; ?>
                <?php if($_GET['ok'] == 3): ?> msgTxt = "¡La unidad ha sido eliminada del sistema con éxito!"; <?php endif; ?>
                
                Swal.fire({
                    icon: 'success',
                    title: 'ERP Ismar',
                    text: msgTxt,
                    confirmButtonColor: '#2563EB'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaVehiculos" class="table align-middle mb-0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3">Placa / Matrícula</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Marca y Modelo</th>
                        <th class="py-3">Año / Color</th>
                        <th class="py-3">Odómetro Actual</th>
                        <?php if (empty($clienteEspecifico)): ?>
                            <th class="py-3">Propietario / Cliente</th>
                        <?php endif; ?>
                        <th class="py-3 text-center pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($vehiculos)): ?>
                        <?php foreach($vehiculos as $v): ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-white text-dark border border-dark px-3 py-2 text-uppercase font-monospace tracking-wider shadow-sm" style="min-width: 110px; font-size: 13.5px; border-radius: 6px;">
                                        <?php echo htmlspecialchars($v['placa']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1 text-uppercase" style="font-size: 11px; font-weight: 600;">
                                        <i class="bi bi-truck me-1"></i><?php echo htmlspecialchars($v['tipo_vehiculo'] ?? 'Auto'); ?>
                                    </span>
                                </td>
                                <td class="fw-bold" style="color: #1E3A5F; font-size: 14px;">
                                    <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>
                                </td>
                                <td class="text-muted" style="font-size: 13px;">
                                    <div><small class="fw-bold text-secondary">Año:</small> <?php echo htmlspecialchars($v['anio'] ?? '-'); ?></div>
                                    <div><small class="fw-bold text-secondary">Color:</small> <?php echo htmlspecialchars($v['color'] ?? '-'); ?></div>
                                </td>
                                <td class="fw-bold" style="color: #2563EB; font-size: 14px;">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    <?php echo number_format($v['kilometraje'], 0, '.', ','); ?> 
                                    <span class="text-muted font-monospace" style="font-size: 11px;">KM</span>
                                </td>
                                <?php if (empty($clienteEspecifico)): ?>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size: 13px;"><?php echo htmlspecialchars($v['nombre_cliente']); ?></div>
                                        <div class="text-muted small" style="font-size: 11px;"><i class="bi bi-card-text me-1"></i><?php echo htmlspecialchars($v['numero_documento']); ?></div>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=nueva_venta&id_vehiculo=<?php echo $v['id_vehicle']; ?>" class="btn btn-sm btn-outline-secondary border-0 text-success" title="Registrar Servicio de Cambio de Aceite" style="font-size: 15px;">
                                            <i class="bi bi-cart-plus-fill"></i>
                                        </a>
                                        <a href="index.php?route=editar_vehiculo&id=<?php echo $v['id_vehicle']; ?>" class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Editar Propiedades Técnicas" style="font-size: 15px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Dar de baja de flota" style="font-size: 15px;" onclick="confirmarBajaVehiculo(<?php echo $v['id_vehicle']; ?>, '<?php echo htmlspecialchars($v['placa']); ?>')">
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
    $('#tablaVehiculos').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            // Renders de los componentes nativos de la librería para que adopten la guía UI
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar por placa o marca...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

// Implementación de Ventana Modal de SweetAlert2
function confirmarBajaVehiculo(id, placa) {
    Swal.fire({
        title: '¿Retirar vehículo de la flota?',
        text: `Se eliminará la unidad con placa "${placa}". Esta acción archivará de forma permanente su historial acumulado de cambios de aceite.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444', // Rojo Error manual
        cancelButtonColor: '#64748B',  // Slate Grey manual
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_vehiculo&id=${id}`;
        }
    });
}
</script>