<?php 
// Incluimos los componentes de estructura global adaptados de la guía
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="mb-3">
        <a href="index.php?route=categorias" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al panel de categorías
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Editar Categoría
            </h2>
            <small class="text-muted">Corrige o actualiza la nomenclatura técnica de la familia de insumos seleccionada.</small>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card-erp">
                <div class="card-body p-2">
                    
                    <form action="index.php?route=actualizar_categoria" method="POST" autocomplete="off">
                        <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                        
                        <div class="mb-4">
                            <label for="nombre" class="label-erp">Nombre de la Categoría / Familia de Insumos</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-tag-fill"></i></span>
                                <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="nombre" name="nombre" value="<?php echo htmlspecialchars($categoria['nombre']); ?>" required autofocus>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 12px;">Al guardar, la actualización se reflejará inmediatamente en todas las fichas de aceites y filtros asociadas.</small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                            <a href="index.php?route=categorias" class="btn-erp-secondary text-center text-white text-decoration-none d-flex align-items-center justify-content-center" style="height: 40px; min-width: 110px; border-radius: 8px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 40px; background-color: #2563EB;">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar Cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>
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