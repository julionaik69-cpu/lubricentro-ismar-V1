<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Auditoría de Caja Chica - Turno #<?php echo $caja['id']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <style>
        @media print {
            body { background-color: #ffffff !important; font-size: 13px; }
            .btn-print-wrapper, .sidebar, .header-navbar { display: none !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
            @page { size: A4 portrait; margin: 1.5cm; }
        }
        body { background-color: #F8FAFC; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .documento-a4 { max-width: 850px; margin: 30px auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .logo-seccion { border-left: 4px solid #2563EB; padding-left: 15px; }
        .tabla-contable thead th { background-color: #1E293B !important; color: #ffffff !important; font-weight: 600; text-transform: uppercase; font-size: 12px; }
        .badge-auditoria { padding: 10px 15px; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block; }
    </style>
</head>
<body>

<div class="container text-end my-3 btn-print-wrapper" style="max-width: 850px;">
    <button onclick="window.print();" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-printer-fill me-2"></i> Imprimir Documento A4</button>
    <a href="index.php?route=dashboard" class="btn btn-outline-secondary fw-bold px-3 shadow-sm">Volver al Panel</a>
</div>

<div class="documento-a4">
    <div class="row align-items-center mb-4">
        <div class="col-8 logo-seccion">
            <h2 class="mb-0 fw-bold tracking-tight" style="color: #0F172A; font-size: 26px;">LUBRICENTRO ISMAR</h2>
            <p class="text-muted small mb-0">Sistema Integral ERP Gerencial - Mantenimiento Automotriz de Confianza</p>
        </div>
        <div class="col-4 text-end">
            <span class="badge bg-primary px-3 py-2 fw-bold" style="font-size: 12px; border-radius: 6px;">TURNO DE CAJA #<?php echo $caja['id']; ?></span>
            <div class="text-muted small mt-1">Generado en el Servidor</div>
        </div>
    </div>

    <div class="alert alert-light border p-3 mb-4 bg-light row g-3 mx-0" style="border-radius: 8px;">
        <div class="col-12 col-md-4"><strong>Cajero Responsable:</strong><br><span class="text-dark"><?php echo htmlspecialchars($caja['cajero_nombre']); ?></span></div>
        <div class="col-12 col-md-4"><strong>Fecha Apertura:</strong><br><span class="text-dark"><?php echo date('d/m/Y H:i', strtotime($caja['fecha_apertura'])); ?></span></div>
        <div class="col-12 col-md-4"><strong>Fecha Cierre Turno:</strong><br><span class="text-dark"><?php echo date('d/m/Y H:i', strtotime($caja['fecha_cierre'])); ?></span></div>
    </div>

    <h5 class="fw-bold mb-3" style="color: #1E3A5F;"><i class="bi bi-cash-coin me-1 text-primary"></i> 1. Arqueo y Conciliación Monetaria</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle tabla-contable">
            <thead>
                <tr>
                    <th>Concepto Operativo</th>
                    <th class="text-end" style="width: 200px;">Monto Corriente</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>(+) Saldo Inicial en Caja Chica (Base de cambio)</td><td class="text-end fw-medium">S/ <?php echo number_format($monto_apertura, 2); ?></td></tr>
                <tr><td>(+) Recaudación por Ventas en Efectivo</td><td class="text-end fw-medium">S/ <?php echo number_format($venta_efectivo, 2); ?></td></tr>
                <tr><td>(+) Recaudación por Ventas en Tarjeta / Yape / Plin</td><td class="text-end fw-medium text-secondary">S/ <?php echo number_format($venta_tarjeta, 2); ?></td></tr>
                <tr><td>(-) Egresos Rápidos de Caja Chica</td><td class="text-end fw-medium text-danger">- S/ <?php echo number_format($total_gastos, 2); ?></td></tr>
                <tr class="table-active fw-bold text-dark" style="border-top: 2px solid #000 !important;">
                    <td>(=) SALDO TOTAL TEÓRICO EXPECTANTE EN EFECTIVO:</td>
                    <td class="text-end text-primary" style="font-size: 16px;">S/ <?php echo number_format($saldo_esperado, 2); ?></td>
                </tr>
                <tr class="fw-bold table-light">
                    <td>(•) CAPITAL FÍSICO CONTADO POR EL CAJERO (Declarado):</td>
                    <td class="text-end text-success" style="font-size: 16px;">S/ <?php echo number_format($saldo_real, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-4 text-center">
        <?php if (round($diferencia, 2) == 0): ?>
            <div class="badge-auditoria bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-shield-check fs-5 me-1"></i> DICTAMEN: TURNO TOTALMENTE CUADRADO SIN OBSERVACIONES
            </div>
        <?php elseif ($diferencia > 0): ?>
            <div class="badge-auditoria bg-warning-subtle text-warning border border-warning-subtle">
                <i class="bi bi-exclamation-octagon fs-5 me-1"></i> DICTAMEN: ALERTA DE SOBRANTE EN CAJA CHICA (+ S/ <?php echo number_format($diferencia, 2); ?>)
            </div>
        <?php else: ?>
            <div class="badge-auditoria bg-danger-subtle text-danger border border-danger-subtle">
                <i class="bi bi-x-circle fs-5 me-1"></i> DICTAMEN: DECLARACIÓN DE FALTANTE CRÍTICO (- S/ <?php echo number_format(abs($diferencia), 2); ?>)
            </div>
        <?php endif; ?>
    </div>

    <h5 class="fw-bold mb-3" style="color: #1E3A5F;"><i class="bi bi-receipt-cutoff me-1 text-danger"></i> 2. Auditoría Detallada de Gastos y Egresos</h5>
    <div class="table-responsive mb-5">
        <table class="table table-striped table-bordered align-middle text-center" style="font-size: 13px;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 80px;">Ítem</th>
                    <th class="text-start">Descripción del Gasto / Concepto</th>
                    <th style="width: 150px;">Monto Retirado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lista_gastos)): ?>
                    <tr><td colspan="3" class="text-muted py-3 italic">No se registraron egresos o compras menores en este turno de caja.</td></tr>
                <?php else: $i = 1; foreach ($lista_gastos as $g): ?>
                    <tr>
                        <td>#<?php echo $i++; ?></td>
                        <td class="text-start"><?php echo htmlspecialchars($g['descripcion']); ?></td>
                        <td class="text-end text-danger fw-bold">- S/ <?php echo number_format($g['monto'], 2); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="row text-center mt-5 pt-5">
        <div class="col-6">
            <div style="border-top: 1px solid #CBD5E1; max-width: 250px; margin: 0 auto; padding-top: 8px;" class="text-muted small">
                Firma del Cajero Turno<br>
                <strong><?php echo htmlspecialchars($caja['cajero_nombre']); ?></strong>
            </div>
        </div>
        <div class="col-6">
            <div style="border-top: 1px solid #CBD5E1; max-width: 250px; margin: 0 auto; padding-top: 8px;" class="text-muted small">
                Auditoría / Control Gerencial<br>
                <strong>Administración Ismar</strong>
            </div>
        </div>
    </div>
</div>

<script>
    // Se abre el diálogo de impresión automáticamente al renderizar en Brave
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>