<?php 
// Incluimos los componentes de estructura global adaptados
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif;">
    
    <div class="mb-3">
        <a href="index.php?route=vehiculos<?php echo $id_cliente_preseleccionado ? '&id_cliente='.$id_cliente_preseleccionado : ''; ?>" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al listado de vehículos
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-5" style="color: #1E3A5F; font-weight: 700; letter-spacing: 0.5px;">
                <i class="bi bi-plus-circle-fill me-2 text-primary"></i> Vincular Nuevo Vehículo
            </h2>
            <small class="text-muted">Asocia las especificaciones técnicas de la unidad al cliente para controlar el ciclo de sus próximos mantenimientos preventivos.</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
                <div class="card-body p-4">
                    
                    <form action="index.php?route=guardar_vehiculo" method="POST" autocomplete="off">
                        
                        <div class="row g-3">
                            
                            <div class="col-12">
                                <label for="id_cliente" class="form-label small fw-bold text-secondary">Propietario / Cliente Vinculado</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-person-bounding-box"></i></span>
                                    <select class="form-select border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="id_cliente" name="id_cliente" required>
                                        <option value="" disabled <?php echo !$id_cliente_preseleccionado ? 'selected' : ''; ?>>-- Seleccione el dueño del auto --</option>
                                        <?php foreach($clientes as $cli): ?>
                                            <option value="<?php echo $cli['id_cliente']; ?>" <?php echo ($id_cliente_preseleccionado == $cli['id_cliente']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cli['nombre'] . ' (' . $cli['tipo_documento'] . ': ' . $cli['numero_documento'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="placa" class="form-label small fw-bold text-secondary">Placa / Matrícula</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control border-start-0 text-uppercase font-monospace tracking-wider fw-bold text-center" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 15px; color: #334155; letter-spacing: 1px;" id="placa" name="placa" placeholder="ABC-123" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="tipo_vehiculo" class="form-label small fw-bold text-secondary">Tipo de Vehículo</label>
                                <select class="form-select" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="tipo_vehiculo" name="tipo_vehiculo" required>
                                    <option value="Auto" selected>Automóvil / Sedán</option>
                                    <option value="Camioneta">Camioneta / SUV</option>
                                    <option value="Furgón">Furgón / Van</option>
                                    <option value="Camión">Camión / Pesado</option>
                                    <option value="Moto">Motocicleta / Lineal</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="anio" class="form-label small fw-bold text-secondary">Año de Fabricación</label>
                                <input type="number" class="form-control" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="anio" name="anio" min="1950" max="<?php echo date('Y')+1; ?>" placeholder="Ej: 2022">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="marca" class="form-label small fw-bold text-secondary">Marca</label>
                                <input type="text" class="form-control" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="marca" name="marca" placeholder="Ej: Toyota" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="modelo" class="form-label small fw-bold text-secondary">Modelo</label>
                                <input type="text" class="form-control" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="modelo" name="modelo" placeholder="Ej: Hilux" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="kilometraje" class="form-label small fw-bold text-secondary">Odómetro Inicial (Km)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-speedometer2"></i></span>
                                    <input type="number" class="form-control border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="kilometraje" name="kilometraje" min="0" step="any" placeholder="Ej: 45000" required>
                                </div>
                                <div class="form-text text-muted" style="font-size: 11px;">Kilometraje con el que ingresa por primera vez.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary">Ciclo de Alerta (Días)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-hourglass-split"></i></span>
                                    <input type="number" class="form-control border-start-0 text-center fw-bold text-primary" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px;" name="dias_alerta" value="30" min="1" required>
                                </div>
                                <div class="form-text text-muted" style="font-size: 11px;">Plazo estimado para el próximo cambio de aceite.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="color" class="form-label small fw-bold text-secondary">Color de la Carrocería</label>
                                <input type="text" class="form-control" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="color" name="color" placeholder="Ej: Gris Metálico">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color: #E2E8F0 !important;">
                            <a href="index.php?route=vehiculos<?php echo $id_cliente_preseleccionado ? '&id_cliente='.$id_cliente_preseleccionado : ''; ?>" class="btn btn-outline-secondary me-2 fw-bold d-flex align-items-center justify-content-center" style="height: 40px; min-width: 120px; border-radius: 8px; font-size: 14px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2" style="height: 40px; border-radius: 8px; font-size: 14px; background-color: #2563EB; border-color: #2563EB;">
                                <i class="bi bi-disk-fill"></i> Guardar Vehículo
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const placaInput = document.getElementById("placa");
    if (placaInput) {
        placaInput.addEventListener("input", function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
        });
    }
});
</script>

<style>
.hover-primary:hover { color: #2563EB !important; }
.form-control:focus, .form-select:focus {
    border-color: #2563EB !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
}
.input-group:focus-within .input-group-text {
    border-color: #2563EB !important;
    background-color: #EFF6FF !important;
}
</style>

<?php 
require_once '../app/views/includes/footer.php'; 
?>