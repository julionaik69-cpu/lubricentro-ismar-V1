<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor">
    <div class="mb-3">
        <a href="index.php?route=usuarios" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500;">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-person-plus-fill me-2 text-primary"></i> Registrar Nuevo Colaborador
            </h2>
            <small class="text-muted">Crea credenciales de acceso para nuevos mecánicos o personal de caja.</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card-erp">
                <div class="card-body p-3">
                    <form action="index.php?route=guardar_usuario" method="POST" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="label-erp">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control-erp" placeholder="Ej: Juan Pérez" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="label-erp">Usuario (Login)</label>
                                <input type="text" name="usuario" class="form-control-erp" placeholder="Ej: jperez" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="label-erp">Contraseña</label>
                                <input type="password" name="password" class="form-control-erp" placeholder="••••••••" required>
                            </div>
                            <div class="col-12">
                                <label class="label-erp">Rol / Permisos</label>
                                <select name="rol" class="form-select form-control-erp">
                                    <option value="CAJERO">Mecánico / Cajero (Ventas)</option>
                                    <option value="ADMIN">Administrador (Total)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                            <a href="index.php?route=usuarios" class="btn-erp-secondary text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">Cancelar</a>
                            <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px; background-color: #2563EB;">
                                <i class="bi bi-check-lg"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.label-erp { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px; text-transform: uppercase; }
.form-control-erp { border: 1px solid #CBD5E1; border-radius: 10px; padding: 10px; }
.card-erp { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.btn-erp-secondary { background: #64748B; border-radius: 10px; }
.btn-erp-primary { border-radius: 10px; color: #fff; }
</style>

<?php require_once '../app/views/includes/footer.php'; ?>