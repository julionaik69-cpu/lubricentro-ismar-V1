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
                <i class="bi bi-pencil-square me-2 text-primary"></i> Editar Ficha de Insumo
            </h2>
            <small class="text-muted">Modifica las propiedades técnicas, precios o límites de alerta de stock para el producto seleccionado.</small>
        </div>
    </div>

    <div class="card-erp">
        <div class="card-body p-2">
            
            <form action="index.php?route=actualizar_producto" method="POST" autocomplete="off">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                <div class="row g-4">
                    
                    <div class="col-12 col-md-6">
                        <h5 class="mb-3 pb-2 border-bottom border-light" style="color: #1E3A5F; font-weight: 600;">
                            <i class="bi bi-info-circle-fill me-2 text-primary" style="font-size: 15px;"></i>Identificación del Producto
                        </h5>
                        
                        <div class="mb-3">
                            <label class="label-erp">Nombre / Descripción Comercial del Insumo</label>
                            <input type="text" class="form-control-erp w-100" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="label-erp">Marca del Fabricante</label>
                            <input type="text" class="form-control-erp w-100" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Categoría de Almacén</label>
                                <select class="form-select form-control-erp" name="id_categoria" required>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Presentación de Venta</label>
                                <select class="form-select form-control-erp" name="unidad_medida" required>
                                    <option value="Galón" <?php echo ($producto['unidad_medida'] == 'Galón') ? 'selected' : ''; ?>>Galón</option>
                                    <option value="Litro" <?php echo ($producto['unidad_medida'] == 'Litro') ? 'selected' : ''; ?>>Litro</option>
                                    <option value="Cuarto" <?php echo ($producto['unidad_medida'] == 'Cuarto') ? 'selected' : ''; ?>>Cuarto de Galón</option>
                                    <option value="Unidad" <?php echo ($producto['unidad_medida'] == 'Unidad') ? 'selected' : ''; ?>>Unidad (Filtros/Bujías)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="label-erp">Código de Barras / SKU Interno</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-qr-code-scan"></i></span>
                                <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="codigo" value="<?php echo htmlspecialchars($producto['codigo'] ?? ''); ?>" placeholder="Código de barras interno">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h5 class="mb-3 pb-2 border-bottom border-light" style="color: #1E3A5F; font-weight: 600;">
                            <i class="bi bi-sliders2-vertical me-2 text-primary" style="font-size: 15px;"></i>Costos y Alertas de Almacén
                        </h5>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Precio de Compra (Costo Neto)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-semibold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                                    <input type="number" step="0.01" min="0.00" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="precio_compra" value="<?php echo $producto['precio_compra']; ?>" required>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Precio de Venta al Público</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-semibold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                                    <input type="number" step="0.01" min="0.00" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="precio_venta" value="<?php echo $producto['precio_venta']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-6 mb-3">
                                <label class="label-erp">Límite de Alerta Mínima</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-danger" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-bell-fill"></i></span>
                                    <input type="number" min="1" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>" required>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.3;">El sistema activará la alerta visual en el panel general si las existencias caen por debajo de esta cifra.</small>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="label-erp">Stock Actual (Solo Lectura)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-lock-fill"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1 bg-light text-muted fw-bold" style="border-radius: 0 10px 10px 0;" value="<?php echo $producto['stock']; ?>" disabled>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.3;">Para actualizar las existencias de este insumo, utilice el panel modular de compras o inventario diario.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                    <a href="index.php?route=productos" class="btn-erp-secondary text-center text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px; background-color: #2563EB;">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar Ficha
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