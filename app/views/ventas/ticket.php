<?php
if(isset($_GET['print']) && $_GET['print'] == 1) {
    echo "<script>window.print();</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?php echo str_pad($venta['id'], 5, "0", STR_PAD_LEFT); ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Courier New', Courier, monospace; background:#e0e0e0; display:flex; justify-content:center; align-items:flex-start; min-height:100vh; padding:20px; }
        .ticket { width:320px; background:#fff; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,.3); }
        .header { text-align:center; border-bottom:2px dashed #000; padding-bottom:10px; margin-bottom:10px; }
        .header h3 { font-size:16px; letter-spacing:1px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size:11px; line-height:1.4; color:#000; }
        .info { font-size:11px; margin-bottom:10px; line-height:1.6; }
        table { width:100%; font-size:11px; border-collapse:collapse; margin: 10px 0; }
        th { border-bottom:1px solid #000; padding:4px 0; }
        td { padding:4px 0; }
        .price { text-align:right; }
        .separator { border-top:1px dashed #000; margin:6px 0; }
        .totales-box { width: 100%; margin-top: 5px; font-size: 11px; }
        .total-row td { font-weight:bold; font-size:13px; border-top:2px solid #000; padding-top:8px; }
        .footer { text-align:center; border-top:2px dashed #000; padding-top:10px; margin-top:10px; font-size:10px; line-height: 1.4; }
        .acciones { text-align:center; margin-top:15px; display:flex; gap:8px; justify-content:center; }
        .btn-accion { padding: 6px 12px; font-weight: bold; border-radius: 4px; border: 1px solid #ccc; cursor: pointer; }
        @media print { body { background:white; padding:0; } .acciones { display:none !important; } }
    </style>
</head>
<body onload="window.print()">
<div class="ticket">
    <div class="header">
        <h3><?php echo strtoupper(htmlspecialchars($empresa['razon_social'])); ?></h3>
        <p>
            RUC: <?php echo htmlspecialchars($empresa['ruc']); ?><br>
            <?php echo htmlspecialchars($empresa['direccion']); ?><br>
            Tel: <?php echo htmlspecialchars($empresa['telefono']); ?>
        </p>
    </div>

    <div class="info">
        <span><strong>Comprobante:</strong> TICKET #<?php echo str_pad($venta['id'], 5, "0", STR_PAD_LEFT); ?></span><br>
        <span><strong>Fecha/Hora:</strong> <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></span><br>
        <span><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Público General'); ?></span><br>
        
        <?php if(!empty($venta['placa'])): ?>
            <span><strong>Placa Vehículo:</strong> <span style="font-size: 12px; font-weight: bold;"><?php echo strtoupper(htmlspecialchars($venta['placa'])); ?></span></span><br>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Producto/Servicio</th>
                <th style="text-align:center; width: 40px;">Cant</th>
                <th class="price" style="width: 70px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subtotal_calculado = 0;
            foreach($detalles as $d): 
                $item_total = $d['subtotal'] ?? ($d['cantidad'] * ($d['precio'] ?? $d['precio_unitario'] ?? 0));
                $subtotal_calculado += $item_total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($d['nombre'] ?? $d['nombre_producto'] ?? 'Item'); ?></td>
                <td style="text-align:center;"><?php echo $d['cantidad']; ?></td>
                <td class="price">S/ <?php echo number_format($item_total, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="separator"></div>
    <table class="totales-box">
        <?php 
        $op_gravada = $subtotal_calculado / 1.18;
        $igv = $subtotal_calculado - $op_gravada;
        ?>
        <tr>
            <td colspan="2" style="text-align: right; color: #555;">Op. Gravada:</td>
            <td class="price" style="color: #555;">S/ <?php echo number_format($op_gravada, 2); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; color: #555;">I.G.V. (18%):</td>
            <td class="price" style="color: #555;">S/ <?php echo number_format($igv, 2); ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="2">TOTAL A PAGAR:</td>
            <td class="price">S/ <?php echo number_format($venta['total'] ?? $subtotal_calculado, 2); ?></td>
        </tr>
    </table>

    <div class="footer">
        Gracias por confiar en el mantenimiento de su vehículo.<br>
        <strong>¡Revise su nivel de fluido regularmente!</strong><br>
        Visítenos pronto para su próximo cambio de aceite.
    </div>
</div>

<div class="acciones">
    <button class="btn-accion" style="background: #10B981; color: white;" onclick="window.print()">🖨️ Imprimir</button>
    <button class="btn-accion" href="index.php?route=dashboard" style="background: #EF4444; color: white;" onclick="window.close()">✕ Cerrar</button>
</div>
</body>
</html>
