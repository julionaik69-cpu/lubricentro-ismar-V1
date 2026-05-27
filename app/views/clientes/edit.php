<?php 
// Incluimos los componentes de estructura global adaptados
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif;">
    
    <div class="mb-3">
        <a href="index.php?route=clientes" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al listado de clientes
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-5" style="color: #1E3A5F; font-weight: 700; letter-spacing: 0.5px;">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Editar Ficha de Cliente
            </h2>
            <small class="text-muted">Modifica los datos generales de contacto o facturación del usuario. El número de documento debe ser único.</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
                <div class="card-body p-4">
                    
                    <form action="index.php?route=actualizar_cliente" method="POST" autocomplete="off">
                        
                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">

                        <div class="row g-3">
                            
                            <div class="col-12 col-md-4">
                                <label for="tipo_documento" class="form-label small fw-bold text-secondary">Tipo de Documento</label>
                                <select class="form-select" style="border-radius: 8px; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="tipo_documento" name="tipo_documento" required>
                                    <option value="DNI" <?php echo ($cliente['tipo_documento'] == 'DNI') ? 'selected' : ''; ?>>DNI (Persona Natural)</option>
                                    <option value="RUC" <?php echo ($cliente['tipo_documento'] == 'RUC') ? 'selected' : ''; ?>>RUC (Empresa / Jurídico)</option>
                                    <option value="Pasaporte" <?php echo ($cliente['tipo_documento'] == 'Pasaporte') ? 'selected' : ''; ?>>Pasaporte / Extranjería</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="numero_documento" class="form-label small fw-bold text-secondary">Número de Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-card-text"></i></span>
                                    <input type="text" class="form-control border-start-0 fw-bold" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155; letter-spacing: 0.5px;" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($cliente['numero_documento']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="nombre" class="form-label small fw-bold text-secondary">Nombre Completo / Razón Social</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="telefono" class="form-label small fw-bold text-secondary">Teléfono / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" class="form-control border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="correo" class="form-label small fw-bold text-secondary">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="correo" name="correo" value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="direccion" class="form-label small fw-bold text-secondary">Dirección Fiscal / Domicilio</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control border-start-0" style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; height: 42px; font-size: 14px; color: #334155;" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color: #E2E8F0 !important;">
                            <a href="index.php?route=clientes" class="btn btn-outline-secondary me-2 fw-bold d-flex align-items-center justify-content-center" style="height: 40px; min-width: 120px; border-radius: 8px; font-size: 14px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2" style="height: 40px; border-radius: 8px; font-size: 14px; background-color: #2563EB; border-color: #2563EB;">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar Cliente
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
    const tipoDoc = document.getElementById("tipo_documento");
    const numDoc = document.getElementById("numero_documento");

    function adaptarValidacion() {
        if (tipoDoc.value === "DNI") {
            numDoc.maxLength = 8;
        } else if (tipoDoc.value === "RUC") {
            numDoc.maxLength = 11;
        } else {
            numDoc.maxLength = 15;
        }
    }

    if (tipoDoc && numDoc) {
        tipoDoc.addEventListener("change", adaptarValidacion);
        adaptarValidacion();

        numDoc.addEventListener("input", function() {
            if(tipoDoc.value === "DNI" || tipoDoc.value === "RUC") {
                this.value = this.value.replace(/[^0-9]/g, '');
            }
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
    transition: all 0.2s ease-in-out;
}
</style>

<?php 
// Incluimos el footer global de tu aplicación
require_once '../app/views/includes/footer.php'; 
?>