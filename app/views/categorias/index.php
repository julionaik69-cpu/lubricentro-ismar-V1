<?php 
// Incluimos los componentes de estructura global adaptados de la guía
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-tags-fill me-2 text-primary"></i> Gestión de Categorías
            </h2>
            <small class="text-muted">Organiza el almacén clasificando los insumos del lubricentro (Aceites de Motor, Filtros, Aditivos).</small>
        </div>
        <div>
            <a href="index.php?route=nueva_categoria" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Nueva Categoría
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msgText = "Operación exitosa.";
                <?php if($_GET['ok'] == '1'): ?> msgText = "¡Categoría creada y registrada correctamente en el almacén!"; <?php endif; ?>
                <?php if($_GET['ok'] == '2'): ?> msgText = "¡El nombre de la categoría ha sido actualizado con éxito!"; <?php endif; ?>
                <?php if($_GET['ok'] == '3'): ?> msgText = "¡La categoría seleccionada ha sido eliminada correctamente!"; <?php endif; ?>
                
                Swal.fire({
                    icon: 'success',
                    title: 'Control de Inventario',
                    text: msgText,
                    confirmButtonColor: '#2563EB'
                });
            });
        </script>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let errText = "Ocurrió un error inesperado.";
                <?php if($_GET['error'] == 'hasproducts'): ?> errText = "No se puede eliminar la categoría. Existen aceites o filtros vinculados activamente a ella."; <?php endif; ?>
                <?php if($_GET['error'] == 'notfound'): ?> errText = "La categoría seleccionada no existe o ya fue removida."; <?php endif; ?>
                
                Swal.fire({
                    icon: 'error',
                    title: 'Restricción de Almacén',
                    text: errText,
                    confirmButtonColor: '#EF4444' // Rojo Error del manual
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaCategorias" class="table align-middle mb-0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3" style="width: 100px;">ID</th>
                        <th class="py-3">Clasificación / Nombre de Categoría</th>
                        <th class="py-3 text-center pe-3" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">#<?php echo $cat['id_categoria']; ?></td>
                                <td class="fw-bold text-uppercase" style="color: #1E3A5F; font-size: 13.5px;">
                                    <i class="bi bi-bookmark-star-fill text-muted opacity-50 me-2"></i>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=editar_categoria&id=<?php echo $cat['id_categoria']; ?>" 
                                           class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Editar Nombre" style="font-size: 16px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" 
                                           class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Eliminar del Catálogo" style="font-size: 16px;"
                                           onclick="confirmarBajaCategoria(<?php echo $cat['id_categoria']; ?>, '<?php echo htmlspecialchars($cat['nombre']); ?>')">
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
    $('#tablaCategorias').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            // Estilización limpia de inputs y selectores nativos de DataTables
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar categoría...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

// Función interactiva SweetAlert2 para reemplazo del confirm estándar
function confirmarBajaCategoria(id, nombre) {
    Swal.fire({
        title: '¿Deseas eliminar la categoría?',
        text: `Esta acción removerá "${nombre}" del catálogo. El sistema validará que no existan aceites ni filtros vinculados antes de proceder.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444', // Rojo Error del manual
        cancelButtonColor: '#64748B',  // Slate Grey del manual
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_categoria&id=${id}`;
        }
    });
}
</script>