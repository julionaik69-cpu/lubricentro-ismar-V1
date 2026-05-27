<?php 
// Incluimos el header global de tu aplicación
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-people-fill me-2 text-primary"></i> Gestión de Clientes
            </h2>
            <small class="text-muted">Administración del padrón de clientes, flotas comerciales y vinculación vehicular.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?route=exportar_clientes" class="btn btn-erp-secondary px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel-fill text-success"></i> Exportar Excel
            </a>
            <a href="index.php?route=nuevo_cliente" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msgText = "Operación exitosa.";
                <?php if($_GET['ok'] == 1): ?> msgText = "¡Cliente registrado correctamente en la base de datos!"; <?php endif; ?>
                <?php if($_GET['ok'] == 2): ?> msgText = "¡Los datos del cliente han sido actualizados con éxito!"; <?php endif; ?>
                <?php if($_GET['ok'] == 3): ?> msgText = "¡El cliente ha sido dado de baja del sistema!"; <?php endif; ?>
                
                Swal.fire({
                    icon: 'success',
                    title: 'Procesado',
                    text: msgText,
                    confirmButtonColor: '#2563EB'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaClientes" class="table align-middle mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3">ID</th>
                        <th class="py-3">Tipo Doc. / Número</th>
                        <th class="py-3">Nombre / Razón Social</th>
                        <th class="py-3">Contacto Directo</th>
                        <th class="py-3">Dirección Residencial</th>
                        <th class="py-3 text-center">Parque Vehicular</th>
                        <th class="py-3 text-center pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($clientes)): ?>
                        <?php foreach($clientes as $c): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">#<?php echo $c['id_cliente']; ?></td>
                                <td>
                                    <span class="badge <?php echo ($c['tipo_documento'] == 'RUC') ? 'bg-primary' : 'bg-dark'; ?> text-uppercase mb-1" style="font-size: 10px; font-weight: 600; letter-spacing: 0.5px;">
                                        <?php echo htmlspecialchars($c['tipo_documento']); ?>
                                    </span><br>
                                    <span class="fw-semibold text-dark" style="font-size: 13.5px;"><?php echo htmlspecialchars($c['numero_documento']); ?></span>
                                </td>
                                <td class="fw-bold" style="color: #1E3A5F; font-size: 14px;"><?php echo htmlspecialchars($c['nombre']); ?></td>
                                <td>
                                    <div style="font-size: 13px; color: #475569;"><i class="bi bi-telephone text-muted me-2"></i><?php echo htmlspecialchars($c['telefono'] ?? '-'); ?></div>
                                    <div class="text-muted" style="font-size: 12px;"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($c['correo'] ?? '-'); ?></div>
                                </td>
                                <td class="text-muted" style="font-size: 13px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($c['direccion'] ?? '-'); ?>
                                    <br>
                                    <small class="text-muted" style="font-size: 11px;">Ayacucho, Perú</small>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?route=vehiculos&id_cliente=<?php echo $c['id_cliente']; ?>" class="btn btn-sm px-3 rounded-pill fw-medium btn-outline-primary" style="font-size: 12px; transition: all 0.2s;">
                                        <i class="bi bi-car-front-fill me-1"></i> Ver Autos
                                    </a>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=editar_cliente&id=<?php echo $c['id_cliente']; ?>" class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Modificar Ficha" style="font-size: 16px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Dar de Baja" style="font-size: 16px;" onclick="confirmarEliminacion(<?php echo $c['id_cliente']; ?>, '<?php echo htmlspecialchars($c['nombre']); ?>')">
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
    $('#tablaClientes').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "desc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            // Estilización limpia de las cajas de paginación y filtros nativos de DataTables
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar cliente...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

// Implementación nativa de SweetAlert2 en reemplazo del confirm aburrido de JS
function confirmarEliminacion(id, nombre) {
    Swal.fire({
        title: '¿Está completamente seguro?',
        text: `Se dará de baja al cliente "${nombre}". Esta acción restringirá sus futuras vinculaciones de órdenes.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444', // Rojo Error de tu guía
        cancelButtonColor: '#64748B',  // Secundario de tu guía
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_cliente&id=${id}`;
        }
    });
}
</script>