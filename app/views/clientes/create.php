<?php 
// Incluimos el header global de tu aplicación
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="mb-3">
        <a href="index.php?route=clientes" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al listado de clientes
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-person-plus-fill me-2 text-primary"></i> Registrar Nuevo Cliente
            </h2>
            <small class="text-muted">Ingresa los datos del cliente para la emisión de comprobantes electrónicos y control de flotas.</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card-erp">
                <div class="card-body p-2">
                    
                    <form action="index.php?route=guardar_cliente" method="POST" autocomplete="off">
                        
                        <div class="row g-4">
                            <div class="col-12 col-md-4">
                                <label for="tipo_documento" class="label-erp">Tipo de Documento</label>
                                <select class="form-select form-control-erp" id="tipo_documento" name="tipo_documento" required>
                                    <option value="DNI" selected>DNI (Persona Natural)</option>
                                    <option value="RUC">RUC (Empresa / Jurídico)</option>
                                    <option value="Pasaporte">Pasaporte / Extranjería</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="numero_documento" class="label-erp">Número de Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-card-text"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="numero_documento" name="numero_documento" placeholder="Ej: 71234567" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="nombre" class="label-erp">Nombre Completo / Razón Social (Facturación)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="nombre" name="nombre" placeholder="Ej: Juan Pérez o Transportes Ismar S.A.C." required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="telefono" class="label-erp">Teléfono / WhatsApp de Contacto</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-whatsapp text-success"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="telefono" name="telefono" placeholder="Ej: 987654321">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="correo" class="label-erp">Correo Electrónico (Envío de CPE)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="correo" name="correo" placeholder="Ej: cliente@correo.com">
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Aquí se enviarán de forma automática las boletas o facturas XML de la SUNAT.</small>
                            </div>

                            <div class="col-12">
                                <label for="direccion" class="label-erp">Dirección Fiscal / Domicilio</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control-erp flex-grow-1" style="border-radius: 0 10px 10px 0;" id="direccion" name="direccion" placeholder="Ej: Av. Mariscal Cáceres 123, Ayacucho">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                            <a href="index.php?route=clientes" class="btn-erp-secondary text-center text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px;">
                                <i class="bi bi-disk-fill"></i> Guardar Cliente
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
            numDoc.placeholder = "Ej: 71234567";
        } else if (tipoDoc.value === "RUC") {
            numDoc.maxLength = 11;
            numDoc.placeholder = "Ej: 20123456789";
        } else {
            numDoc.maxLength = 15;
            numDoc.placeholder = "Número de documento de extranjería";
        }
    }

    tipoDoc.addEventListener("change", function() {
        numDoc.value = ""; // Limpiar campo al cambiar de tipo para evitar cruces
        adaptarValidacion();
    });
    
    adaptarValidacion(); // Inicializar al cargar el documento

    // Bloquear caracteres no numéricos estrictamente para documentos nacionales peruanos
    numDoc.addEventListener("input", function() {
        if(tipoDoc.value === "DNI" || tipoDoc.value === "RUC") {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });
});
</script>

<style>
.hover-primary:hover {
    color: #2563EB !important;
}
</style>

<?php 
// Incluimos el footer global de tu aplicación
require_once '../app/views/includes/footer.php'; 
?>