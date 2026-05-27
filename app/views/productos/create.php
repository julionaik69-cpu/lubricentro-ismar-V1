<?php 
// Incluimos los componentes de estructura global adaptados de la guía
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="mb-3">
        <a href="index.php?route=productos" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al inventario de productos
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-plus-circle-fill me-2 text-primary"></i> Registrar Nuevo Insumo
            </h2>
            <small class="text-muted">Incorpora aceites, lubricantes, aditivos o filtros especificando sus controles técnicos de stock mínimo.</small>
        </div>
    </div>

    <?php if(isset($_GET['error']) && $_GET['error'] == 'duplicate'): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Código o Nombre Duplicado',
                    text: 'Ya existe un producto registrado con esa misma descripción o código SKU en el sistema del lubricentro.',
                    confirmButtonColor: '#EF4444'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card-erp">
        <div class="card-body p-2">
            
            <form action="index.php?route=guardar_producto" method="POST" autocomplete="off">
                <div class="row g-4">
                    
                    <div class="col-12 col-md-6">
                        <h5 class="mb-3 pb-2 border-bottom border-light" style="color: #1E3A5F; font-weight: 600;">
                            <i class="bi bi-info-circle-fill me-2 text-primary" style="font-size: 15px;"></i>Identificación del Producto
                        </h5>
                        
                        <div class="mb-3">
                            <label class="label-erp">Nombre / Descripción Comercial del Insumo</label>
                            <input type="text" class="form-control-erp w-100" name="nombre" placeholder="Ej: Aceite Shell Helix Ultra 10W-40" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label class="label-erp">Marca del Fabricante</label>
                            <input type="text" class="form-control-erp w-100" name="marca" placeholder="Ej: Shell, Castrol, Mobil, Bosch, Mobil" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Categoría de Almacén</label>
                                <select class="form-select form-control-erp" name="id_categoria" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>">
                                            <?php echo htmlspecialchars($cat['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Presentación de Venta</label>
                                <select class="form-select form-control-erp" name="pageLength" required>
                                    <option value="Galón" selected>Galón</option>
                                    <option value="Litro">Litro</option>
                                    <option value="Cuarto">Cuarto de Galón</option>
                                    <option value="Unidad">Unidad (Filtros/Bujías)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="label-erp">Código de Barras / SKU Interno (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-qr-code-scan"></i></span>
                                <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="codigo" placeholder="Dejar vacío para autogenerar codificación correlativa">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h5 class="mb-3 pb-2 border-bottom border-light" style="color: #1E3A5F; font-weight: 600;">
                            <i class="bi bi-sliders2-vertical me-2 text-primary" style="font-size: 15px;"></i>Costos y Gestión de Almacén
                        </h5>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Precio de Compra (Costo Neto)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-semibold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                                    <input type="number" step="0.01" min="0.00" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="precio_compra" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Precio de Venta al Público</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-semibold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                                    <input type="number" step="0.01" min="0.00" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="precio_venta" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Stock Inicial de Inventario</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-box-seam"></i></span>
                                    <input type="number" min="0" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="stock" value="0" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.3;">Unidades físicas cargadas en estanterías de forma inmediata.</small>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Límite de Alerta Mínima</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-danger" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-bell-fill"></i></span>
                                    <input type="number" min="1" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="stock_minimo" value="5" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.3;">El indicador del Dashboard se tornará rojo si las existencias bajan de este número.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                    <a href="index.php?route=productos" class="btn-erp-secondary text-center text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px; background-color: #2563EB;">
                        <i class="bi bi-disk-fill"></i> Guardar Producto
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
.hover-primary:hover {
    color: #2563EB !important;
}
</style>
<?php 
// Incluimos el footer global de tu aplicación
require_once '../app/views/includes/footer.php'; 
?>