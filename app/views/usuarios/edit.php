<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor">
    
    <div class="mb-3">
        <a href="index.php?route=usuarios" class="text-decoration-none text-muted small hover-primary" style="font-weight: 500; transition: color 0.2s;">
            <i class="bi bi-arrow-left"></i> Volver al listado de personal
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
        <div>
            <h2 class="mb-0 text-uppercase tracking-wider fs-4" style="color: #1E3A5F; font-weight: 600;">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Editar Perfil de Usuario
            </h2>
            <small class="text-muted">Actualiza los privilegios, credenciales de acceso o el estado operativo del colaborador en el taller.</small>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-10 col-lg-7">
            <div class="card-erp">
                <div class="card-body p-2">
                    
                    <form action="index.php?route=actualizar_usuario" method="POST" autocomplete="off">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nombre" class="label-erp">Nombre Completo del Colaborador</label>
                                <input type="text" id="nombre" name="nombre" class="form-control-erp w-100" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label for="usuario" class="label-erp">Usuario de Acceso (Login)</label>
                                <input type="text" id="usuario" name="usuario" class="form-control-erp w-100" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required style="text-transform: lowercase;">
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label for="password" class="label-erp">Nueva Contraseña de Seguridad</label>
                                <input type="password" id="password" name="password" class="form-control-erp w-100" placeholder="••••••••">
                                <div class="form-text text-muted" style="font-size: 11px;">Dejar completamente en blanco para conservar la contraseña actual.</div>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label for="rol" class="label-erp">Rol / Nivel de Privilegios</label>
                                <select id="rol" name="rol" class="form-select form-control-erp" required>
                                    <option value="ADMIN" <?php echo ($usuario['rol'] == 'ADMIN' || $usuario['rol'] == 'Administrador') ? 'selected' : ''; ?>>ADMINISTRADOR (Acceso Total)</option>
                                    <option value="CAJERO" <?php echo $usuario['rol'] == 'CAJERO' ? 'selected' : ''; ?>>MECÁNICO / CAJERO (Operaciones Limitadas)</option>
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label for="estado" class="label-erp">Estado de la Cuenta</label>
                                <select id="estado" name="estado" class="form-select form-control-erp" required>
                                    <option value="1" <?php echo $usuario['estado'] == 1 ? 'selected' : ''; ?>>Permitir Acceso (Activo)</option>
                                    <option value="0" <?php echo $usuario['estado'] == 0 ? 'selected' : ''; ?>>Bloquear Turno (Inactivo)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                            <a href="index.php?route=usuarios" class="btn-erp-secondary text-center text-white text-decoration-none me-2 d-flex align-items-center justify-content-center" style="height: 42px; min-width: 120px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-erp-primary shadow-sm d-flex align-items-center gap-2" style="height: 42px; background-color: #2563EB;">
                                <i class="bi bi-disk-fill"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-primary:hover { color: #2563EB !important; }
</style>
<?php require_once '../app/views/includes/footer.php'; ?>