<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Empresa / SUNAT</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        h2 { color: #1e3a5f; margin-top: 0; border-bottom: 2px solid #2563eb; padding-bottom: 10px; font-size: 18px; text-transform: uppercase; font-weight: 600; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 13px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="file"] {
            width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #334155; display: block;
        }
        input:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .row { display: flex; gap: 15px; margin-bottom: 10px; }
        .row .form-group { flex: 1; }
        button { background: #2563eb; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: background 0.2s; }
        button:hover { background: #1d4ed8; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        .nav-back { display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: 500; }
        .nav-back:hover { text-decoration: underline; }
        .cert-info { background: #e0f2fe; padding: 15px; border-radius: 8px; color: #0369a1; font-size: 13px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-factura { background: #e0f2fe; color: #0369a1; }
        .badge-boleta { background: #ffedd5; color: #9a3412; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php?route=dashboard" class="nav-back">← Volver al Dashboard</a>

    <?php if (isset($_GET['ok']) && $_GET['ok'] == '1'): ?>
        <div class="alert alert-success">✅ Configuración guardada correctamente en la Base de Datos.</div>
    <?php endif; ?>
    <?php if (isset($_GET['ok']) && $_GET['ok'] == '2'): ?>
        <div class="alert alert-success">✅ Correlativos de comprobantes actualizados con éxito.</div>
    <?php endif; ?>
    <?php if (isset($_GET['ok']) && $_GET['ok'] == '3'): ?>
        <div class="alert alert-success">✅ Certificado digital electrónico subido correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">❌ Hubo un inconveniente al procesar la solicitud en el servidor.</div>
    <?php endif; ?>

    <div class="card">
        <h2>🏢 Panel de Control - Lubricentro Ismar</h2>
        <form method="POST" action="index.php?route=guardar_empresa" style="margin-top: 15px;">
            
            <input type="hidden" name="id" value="1">

            <div class="row">
                <div class="form-group">
                    <label>Número de RUC</label>
                    <input type="text" name="ruc" value="<?php echo htmlspecialchars($empresa['ruc'] ?? '20123456789'); ?>" maxlength="11" required>
                </div>
                <div class="form-group">
                    <label>Razón Social / Nombre Comercial</label>
                    <input type="text" name="razon_social" value="<?php echo htmlspecialchars($empresa['razon_social'] ?? 'LUBRICENTRO ISMAR S.A.C.'); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Dirección Fiscal / Taller Técnico</label>
                <input type="text" name="direccion" value="<?php echo htmlspecialchars($empresa['direccion'] ?? 'Av. Mariscal Cáceres 123'); ?>" required>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <label>Teléfono de Contacto (Aparece en Ticket)</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($empresa['telefono'] ?? '987654321'); ?>">
                </div>
                <div class="form-group">
                    <label>Correo Electrónico Corporativo</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($empresa['email'] ?? 'info@ismar.com'); ?>">
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
            <h3 style="font-size: 15px; color: #1e3a5f; margin-bottom: 15px;">🔐 Credenciales de Acceso SOL (SUNAT)</h3>
            
            <div class="row">
                <div class="form-group">
                    <label>Usuario SOL</label>
                    <input type="text" name="sol_usuario" value="<?php echo htmlspecialchars($empresa['usuario_sol'] ?? ''); ?>" placeholder="MODOSOL1">
                </div>
                <div class="form-group">
                    <label>Clave SOL</label>
                    <input type="password" name="sol_clave" value="<?php echo htmlspecialchars($empresa['clave_sol'] ?? ''); ?>" placeholder="••••••••">
                </div>
            </div>
            
            <button type="submit" style="margin-top: 10px;">💾 Guardar Configuración General</button>
        </form>
    </div>

    <div class="card">
        <h2>📜 Certificado Digital Electrónico (.pem)</h2>
        <?php if (!empty($empresa['certificado'])): ?>
            <div class="cert-info">
                ✅ Certificado activo en servidor: <code><?php echo basename($empresa['certificado']); ?></code>
            </div>
        <?php else: ?>
            <p style="color:#94a3b8; font-size: 13px;">No hay ningún archivo de certificado digital .pem cargado en el sistema.</p>
        <?php endif; ?>
        <form method="POST" action="index.php?route=subir_certificado" enctype="multipart/form-data" style="margin-top:15px;">
            <div class="form-group">
                <input type="file" name="certificado" accept=".pem" required>
            </div>
            <button type="submit" class="btn-success">📤 Subir Llave de Certificado</button>
        </form>
    </div>

    <div class="card">
        <h2>🧾 Numeración y Correlativos de Comprobantes</h2>
        <form method="POST" action="index.php?route=guardar_correlativos">
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Serie</th>
                        <th>Correlativo Actual</th>
                        <th>Nuevo Correlativo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($series)): ?>
                        <?php foreach ($series as $s): ?>
                        <tr>
                            <td>
                                <?php if (($s['tipo_comprobante'] ?? '') == '01'): ?>
                                    <span class="badge badge-factura">FACTURA ELECTRONICA</span>
                                <?php else: ?>
                                    <span class="badge badge-boleta">BOLETA DE VENTA</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($s['serie']); ?></strong></td>
                            <td><?php echo $s['correlativo']; ?></td>
                            <td>
                                <input type="number" name="series[<?php echo htmlspecialchars($s['serie']); ?>]" value="<?php echo $s['correlativo']; ?>" min="1" style="width:120px;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8;">No se han inicializado las series de comprobantes de facturación.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="submit" style="margin-top:15px;">📝 Actualizar Numeración</button>
        </form>
    </div>

</div>
</body>
</html>