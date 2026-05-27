<?php require_once '../app/views/includes/header.php'; ?>
<?php require_once '../app/views/includes/sidebar.php'; ?>

<div class="content-principal-contenedor pt-4" style="min-height: 100vh; background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif;">
    <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: #E2E8F0 !important;">
        <div>
            <a href="index.php?route=productos" class="btn btn-sm btn-outline-secondary fw-bold px-3" style="border-radius: 6px;"><i class="bi bi-arrow-left"></i> Volver al Catálogo</a>
            <h2 class="mt-3 text-uppercase tracking-wider fs-5 mb-1" style="color: #1E3A5F; font-weight: 700;">
                <i class="bi bi-gear-wide-connected text-primary me-1"></i> Ficha Técnica y Especificaciones
            </h2>
            <small class="text-muted">Asigna los grados de viscosidad, normativas SAE y composiciones químicas por marca de lubricante.</small>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-2" style="font-size: 13px; border-radius: 6px;">
                Cód. Maestro: #<?php echo $id_producto; ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
                <div class="card-header bg-light py-3 fw-bold border-0 text-uppercase" style="color: #1E3A5F; font-size: 13px; border-radius: 12px 12px 0 0;">
                    <i class="bi bi-plus-circle-fill text-primary me-1"></i> Añadir Atributo Mecánico
                </div>
                <div class="card-body p-4">
                    <form action="index.php?route=guardar_variante" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $id_producto; ?>">

                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1">Viscosidad / Grado SAE:</label>
                            <input type="text" name="viscosidad" class="form-control fw-medium" placeholder="Ej: 10W-40, 20W-50, 5W-30" required style="height: 40px; border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1">Tipo de Aceite / Base:</label>
                            <input type="text" name="tipo_aceite" class="form-control fw-medium" placeholder="Ej: Sintético, Semisintético, Mineral" required style="height: 40px; border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1">Cantidad de Envases (Stock):</label>
                            <input type="number" name="stock" class="form-control fw-bold text-center" value="1" min="1" required style="height: 40px; border-radius: 8px;">
                        </div>

                        <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm mt-2" style="height: 42px; border-radius: 8px; background-color: #2563EB;">
                            <i class="bi bi-file-earmark-plus"></i> Registrar en Almacén
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border: 1px solid #E2E8F0 !important;">
                <div class="card-body p-0">
                    <div class="p-3 bg-light border-bottom fw-bold text-uppercase" style="color: #475569; font-size: 13px; border-radius: 12px 12px 0 0;">
                        <i class="bi bi-list-check text-success me-1"></i> Desglose Técnico en Existencia
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                            <thead class="table-light">
                                <tr style="color: #475569; font-weight: 600;">
                                    <th class="ps-4" style="width: 80px;">ID</th>
                                    <th>Grado Viscosidad</th>
                                    <th>Composición / Base</th>
                                    <th class="text-center" style="width: 150px;">Stock Disponible</th>
                                    <th class="text-center pe-4" style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($variantes)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-info-circle-fill text-secondary fs-3 d-block mb-2"></i>
                                            Este insumo no tiene especificaciones registradas aún. Configura su viscosidad a la izquierda.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($variantes as $v): ?>
                                    <tr>
                                        <td class="ps-4 text-secondary font-monospace">#<?php echo $v['id']; ?></td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-2.5 py-1.5 font-monospace" style="font-size: 13px;">
                                                <?php echo htmlspecialchars($v['talla']); ?>
                                            </span>
                                        </td>
                                        <td class="fw-medium text-dark"><?php echo htmlspecialchars($v['color']); ?></td>
                                        
                                        <td class="text-center">
                                            <form action="index.php?route=actualizar_stock_manual" method="POST" class="d-flex justify-content-center align-items-center gap-1">
                                                <input type="hidden" name="id_variante" value="<?php echo $v['id']; ?>">
                                                <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                                                
                                                <input type="number" name="nuevo_stock" value="<?php echo $v['stock']; ?>" 
                                                       class="form-control form-control-sm fw-bold text-center" style="width: 75px; height: 32px; border-radius: 6px;">
                                                
                                                <button type="submit" class="btn btn-sm btn-success p-1 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 6px; background-color: #10B981; border-color: #10B981;" title="Actualizar Unidades">
                                                    <i class="bi bi-check-lg text-white" style="font-size: 14px;"></i>
                                                </button>
                                            </form>
                                        </td>
                                        
                                        <td class="text-center pe-4">
                                            <a href="index.php?route=eliminar_variante&id_var=<?php echo $v['id']; ?>&id_prod=<?php echo $id_producto; ?>" 
                                               class="btn btn-sm btn-outline-danger border-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 6px;"
                                               onclick="return confirm('¿Remover esta especificación técnica del almacén? Se recalculará el stock.');">
                                                <i class="bi bi-trash3-fill" style="font-size: 15px;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/includes/footer.php'; ?>