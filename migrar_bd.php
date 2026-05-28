<?php
$db = new PDO('sqlite:db_lubricentro.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Agregar columna placa
    $db->exec("ALTER TABLE ventas ADD COLUMN placa TEXT DEFAULT 'S/P'");
    echo "✅ Columna placa agregada\n";
} catch (PDOException $e) {
    echo "⚠️ " . $e->getMessage() . "\n";
}

try {
    // Crear tabla series_comprobantes
    $db->exec("CREATE TABLE IF NOT EXISTS series_comprobantes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo_comprobante TEXT NOT NULL,
        serie TEXT NOT NULL,
        correlativo INTEGER DEFAULT 1
    )");
    echo "✅ Tabla series_comprobantes creada\n";
    
    // Insertar series
    $db->exec("INSERT OR IGNORE INTO series_comprobantes (tipo_comprobante, serie, correlativo) VALUES 
        ('01', 'F001', 1),
        ('03', 'B001', 1)");
    echo "✅ Series insertadas\n";
} catch (PDOException $e) {
    echo "⚠️ " . $e->getMessage() . "\n";
}

try {
    // Verificar/agregar nombre_producto
    $result = $db->query("PRAGMA table_info(detalle_ventas)");
    $hasNombre = false;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($row['name'] == 'nombre_producto') $hasNombre = true;
    }
    if (!$hasNombre) {
        $db->exec("ALTER TABLE detalle_ventas ADD COLUMN nombre_producto TEXT DEFAULT 'Producto'");
        echo "✅ Columna nombre_producto agregada\n";
    } else {
        echo "✅ Columna nombre_producto ya existe\n";
    }
} catch (PDOException $e) {
    echo "⚠️ " . $e->getMessage() . "\n";
}

echo "\n🎉 Migración completada!\n";