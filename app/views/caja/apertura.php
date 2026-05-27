<?php 
// Incluimos la estructura global y de navegación adaptada de tu ERP
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-cash-stack me-2 text-primary"></i> Control de Caja Diaria
            </h2>
            <small class="text-muted">Apertura del punto de venta y control de flujo de efectivo para el turno actual.</small>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="card-erp" style="border-top: 4px solid #10B981;">
                <div class="card-body p-2 text-center">
                    
                    <div class="profile-avatar mx-auto mb-3 fs-3" style="background-color: #E6F4EA; color: #10B981; border: none; width: 55px; height: 55px;">
                        <i class="bi bi-sunrise-fill"></i>
                    </div>

                    <h5 class="fw-bold mb-1" style="color: #1E3A5F;">Apertura de Turno</h5>
                    <p class="text-muted small px-3 mb-4">
                        Hola, <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['user_nombre'] ?? 'Operador'); ?></strong>. Antes de procesar órdenes de servicio o registrar boletas, es obligatorio inicializar el saldo del cajón.
                    </p>
                    
                    <form action="index.php?route=guardar_apertura" method="POST" autocomplete="off">
                        
                        <div class="mb-4 text-start">
                            <label class="label-erp text-uppercase tracking-wider" style="font-size: 11px; font-weight: 700;">Monto Inicial de Apertura (Sencillo / Cambio)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px;">
                                <span class="input-group-text bg-light border-end-0 text-muted fw-bold" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1; font-size: 18px;">S/</span>
                                <input type="number" step="0.01" min="0.00" name="monto_inicial" class="form-control-erp flex-grow-1 fw-bold text-dark" style="border-radius: 0 10px 10px 0; font-size: 22px; padding-left: 10px;" value="0.00" required autofocus onfocus="this.select();">
                            </div>
                            <div class="form-text text-muted mt-2" style="font-size: 12px; lh-sm: 1.3;">
                                <i class="bi bi-info-circle me-1 text-primary"></i> Realice el conteo del dinero físico que se le ha asignado para dar cambio inicial.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-erp-success w-100 btn-lg py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #10B981; font-size: 15px; border-radius: 10px; transition: background 0.2s;">
                            <i class="bi bi-unlock-fill fs-5"></i> Abrir Caja y Habilitar Ventas
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php 
// Incluimos el footer global de tu aplicación
require_once '../app/views/includes/footer.php'; 
?>