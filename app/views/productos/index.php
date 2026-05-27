<?php 
// Incluimos los componentes de estructura global adaptados de la guía
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-droplet-half me-2 text-primary"></i> Inventario de Aceites y Filtros
            </h2>
            <small class="text-muted">Control general de insumos, marcas, empaques, volúmenes y niveles de stock crítico.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?route=exportar_productos" class="btn btn-erp-secondary px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill text-success"></i> Exportar Excel
            </a>
            <a href="index.php?route=nuevo_producto" class="btn btn-erp-primary px-3 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <?php if(isset($_GET['ok'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let msgText = "Operación exitosa.";
                <?php if($_GET['ok'] == '1'): ?> msgText = "¡Producto registrado e incorporado al inventario con éxito!"; <?php endif; ?>
                <?php if($_GET['ok'] == '2'): ?> msgText = "¡Ficha técnica y niveles de existencias actualizados correctamente!"; <?php endif; ?>
                <?php if($_GET['ok'] == '3'): ?> msgText = "¡El insumo ha sido removido del catálogo correctamente!"; <?php endif; ?>
                
                Swal.fire({
                    icon: 'success',
                    title: 'Gestión de Almacén',
                    text: msgText,
                    confirmButtonColor: '#2563EB'
                });
            });
        </script>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let errText = "Hubo un inconveniente al procesar la solicitud.";
                <?php if($_GET['error'] == 'notfound'): ?> errText = "El insumo que buscas no existe o ya fue borrado del almacén."; <?php endif; ?>
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Almacén',
                    text: errText,
                    confirmButtonColor: '#EF4444'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="table-responsive">
            <table id="tablaProductos" class="table align-middle mb-0" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3" style="width: 70px;">ID</th>
                        <th class="py-3" style="width: 130px;">Código SKU</th>
                        <th class="py-3">Descripción del Insumo</th>
                        <th class="py-3">Marca</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Presentación</th>
                        <th class="py-3 text-center" style="width: 110px;">Stock</th>
                        <th class="py-3 text-end" style="width: 130px;">P. Venta</th>
                        <th class="py-3 text-center pe-3" style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($productos)): ?>
                        <?php foreach ($productos as $prod): ?>
                            <?php 
                            // Lógica automatizada para detectar stock crítico
                            $esCritico = ($prod['stock'] <= $prod['stock_minimo']); 
                            ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">#<?php echo $prod['id_producto']; ?></td>
                                <td>
                                    <small class="text-uppercase font-monospace text-muted fw-bold"><?php echo htmlspecialchars($prod['codigo'] ?? 'SKU-GEN'); ?></small>
                                </td>
                                <td class="fw-bold" style="color: #1E3A5F; font-size: 14px;">
                                    <?php echo htmlspecialchars($prod['nombre']); ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-weight: 600; font-size: 11px;"><?php echo htmlspecialchars($prod['marca']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-uppercase" style="font-size: 10px; font-weight: 600; letter-spacing: 0.3px;">
                                        <?php echo htmlspecialchars($prod['nombre_categoria'] ?? 'Sin Cat.'); ?>
                                    </span>
                                </td>
                                <td class="small text-muted fw-medium"><?php echo htmlspecialchars($prod['unidad_medida'] ?? 'Unidad'); ?></td>
                                
                                <td class="text-center fw-bold fs-6">
                                    <?php if($esCritico): ?>
                                        <span class="text-danger animate-pulse d-inline-flex align-items-center gap-1" title="¡Alerta! Stock por debajo del límite permitido (Mínimo: <?php echo $prod['stock_minimo']; ?>)">
                                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 13px; color: #EF4444;"></i>
                                            <?php echo $prod['stock']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <?php echo $prod['stock']; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end fw-bold text-dark">
                                    S/ <?php echo number_format($prod['precio_venta'], 2); ?>
                                </td>
                                
                                <td class="text-center pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=editar_producto&id=<?php echo $prod['id_producto']; ?>" 
                                        class="btn btn-sm btn-outline-secondary border-0 text-primary" title="Editar Ficha Técnica" style="font-size: 16px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" 
                                        class="btn btn-sm btn-outline-secondary border-0 text-danger" title="Dar de baja de inventario" style="font-size: 16px;"
                                        onclick="confirmarBajaProducto(<?php echo $prod['id_producto']; ?>, '<?php echo htmlspecialchars($prod['nombre']); ?>')">
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
    $('#tablaProductos').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "drawCallback": function() {
            // Estilización limpia de cajas de búsqueda y filtros nativos
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').addClass('form-control-erp d-inline-block w-auto py-1 px-2 small ms-2');
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Buscar insumo...');
            $('.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate').addClass('text-muted small mt-3');
        }
    });
});

// Función interactiva de confirmación con SweetAlert2
function confirmarBajaProducto(id, nombre) {
    Swal.fire({
        title: '¿Dar de baja del almacén?',
        text: `Se archivará el producto "${nombre}". Esta acción no alterará los cierres de caja ni el historial de ventas anteriores.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444', // Rojo Error manual
        cancelButtonColor: '#64748B',  // Slate Grey manual
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?route=eliminar_producto&id=${id}`;
        }
    });
}
</script>

<?php require_once '../app/views/includes/footer.php'; ?>