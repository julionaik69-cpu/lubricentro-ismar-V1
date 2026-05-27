<?php 
// Incluimos los componentes de estructura global adaptados de tu aplicación
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="mb-3">
        <a href="index.php?route=servicios" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al catálogo de servicios
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-plus-circle-fill me-2 text-primary"></i> Registrar Nuevo Servicio
            </h2>
            <small class="text-muted">Agrega un tipo de mano de obra o mantenimiento preventivo estableciendo su tarifa estándar para el punto de venta.</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card-erp">
                <div class="card-body p-2">
                    
                    <form action="index.php?route=guardar_servicio" method="POST" autocomplete="off">
                        
                        <div class="row g-4">
                            <div class="col-12 col-md-8">
                                <label for="nombre" class="label-erp">Nombre del Servicio / Tarea Mecánica</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-gear"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="nombre" name="nombre" placeholder="Ej: Cambio de Aceite de Motor + Filtros" required autofocus>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="precio" class="label-erp">Precio Base (S/)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-semibold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">S/</span>
                                    <input type="number" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="precio" name="precio" min="0" step="0.01" placeholder="Ej: 25.00" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="label-erp">Descripción Comercial / ¿Qué incluye el servicio?</label>
                                <textarea class="form-control-erp w-100" id="descripcion" name="descripcion" rows="4" placeholder="Ej: Incluye la mano de obra para el vaciado de fluidos usados, instalación del nuevo filtro de aceite y revisión de niveles complementaria de cortesía."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                            <a href="index.php?route=servicios" class="btn-erp-secondary text-center text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px; background-color: #2563EB;">
                                <i class="bi bi-disk-fill text-white"></i> Guardar Servicio
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