<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-people-fill me-2 text-primary"></i> Configuración de Usuarios y Personal
            </h2>
            <small class="text-muted">Administración de credenciales de accesos, roles de mecánicos y privilegios de cajeros.</small>
        </div>
        <div>
            <a href="index.php?route=nuevo_usuario" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msg = "Operación exitosa.";
                <?php if($_GET['ok'] == '1') echo "msg = '¡Usuario operativo creado correctamente!';"; ?>
                <?php if($_GET['ok'] == '2') echo "msg = '¡Datos del personal actualizados con éxito!';"; ?>
                <?php if($_GET['ok'] == '3') echo "msg = '¡El usuario ha sido removido del sistema!';"; ?>
                Swal.fire({ icon: 'success', title: 'Gestión de Usuarios', text: msg, confirmButtonColor: '#2563EB' });
            });
        </script>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msg = "Error en la operación.";
                <?php if($_GET['error'] == 'self') echo "msg = 'Restricción de seguridad: No puedes eliminarte a ti mismo del sistema.';"; ?>
                Swal.fire({ icon: 'error', title: 'Seguridad', text: msg, confirmButtonColor: '#EF4444' });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaUsuarios" class="table align-middle mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3" style="width: 80px;">ID</th>
                        <th class="py-3">Nombre Completo del Personal</th>
                        <th class="py-3">Usuario (Login)</th>
                        <th class="py-3">Rol / Privilegio</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-center pe-3" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($usuarios) && !empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-secondary">#<?php echo $u['id_usuario']; ?></td>
                            <td class="fw-bold" style="color: #1E3A5F; font-size: 14px;"><?php echo htmlspecialchars($u['nombre']); ?></td>
                            <td class="text-muted fw-medium"><?php echo htmlspecialchars($u['usuario']); ?></td>
                            <td>
                                <?php if(strtoupper($u['rol']) == 'ADMIN' || strtoupper($u['rol']) == 'ADMINISTRADOR'): ?>
                                    <span class="badge bg-danger text-white font-monospace px-2 py-1" style="font-size: 10px;">ADMINISTRADOR</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark font-monospace px-2 py-1" style="font-size: 10px;">MECÁNICO / CAJERO</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($u['estado'] == 1): ?>
                                    <span class="badge bg-light text-success border border-success rounded-pill px-3">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border rounded-pill px-3">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group" role="group">
                                    <a href="index.php?route=editar_usuario&id=<?php echo $u['id_usuario']; ?>" 
                                       class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Editar Ficha" style="font-size: 16px;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <?php if($u['id_usuario'] != $_SESSION['user_id']): ?>
                                        <a href="#" class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Dar de baja" style="font-size: 16px;"
                                           onclick="confirmarBajaUsuario(<?php echo $u['id_usuario']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary border-0 text-muted" disabled title="No puedes eliminar tu propia cuenta" style="font-size: 16px;">
                                            <i class="bi bi-trash3-fill opacity-25"></i>
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

<script>
$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar personal...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

function confirmarBajaUsuario(id, nombre) {
    Swal.fire({
        title: '¿Eliminar credenciales de usuario?',
        text: `Se revocarán de forma definitiva los accesos al sistema para "${nombre}".`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_usuario&id=${id}`;
        }
    });
}
</script>

<?php require_once '../app/views/includes/footer.php'; ?>