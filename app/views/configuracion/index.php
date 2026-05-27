<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor">
    <h2 class="fs-4 mb-4 fw-bold"><i class="bi bi-gear-fill text-primary"></i> Configuración de Alertas</h2>
    
    <div class="card-erp" style="max-width: 400px;">
        <form action="index.php?route=guardar_configuracion" method="POST">
            <div class="mb-3">
                <label class="label-erp">Días para próxima alerta:</label>
                <input type="number" name="dias" class="form-control-erp" value="<?php echo $dias_actuales; ?>" required>
                <small class="text-muted">El sistema alertará a clientes tras X días del último servicio.</small>
            </div>
            <button type="submit" class="btn btn-erp-primary w-100">Guardar Configuración</button>
        </form>
    </div>
</div>

<?php require_once '../app/views/includes/footer.php'; ?>