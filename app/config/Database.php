<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        // 1. Detectar si existe la variable de entorno de producción (Railway / Supabase)
        $dbUrl = getenv('DATABASE_URL');

        try {
            if ($dbUrl) {
                $dbopts = parse_url($dbUrl);
                $host = $dbopts["host"];
                $port = $dbopts["port"];
                $user = $dbopts["user"];
                $pass = $dbopts["pass"];
                $dbname = ltrim($dbopts["path"], '/');

                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                $this->conn = new PDO($dsn, $user, $pass);
            } else {
                // ==========================================
                // ENTORNO LOCAL: SQLite (RUTA CORREGIDA)
                // ==========================================
                // Usamos dirname(__DIR__, 2) para subir desde app/config/ hasta la raíz (Lubricentro/)
                // ENTORNO LOCAL: SQLite
                $rutaSQLite = __DIR__ . '/../../data/db_lubricentro.sqlite'; 
                
                $dsn = "sqlite:" . $rutaSQLite;
                $this->conn = new PDO($dsn);
                
                // Habilitar el soporte de Llaves Foráneas en SQLite
                $this->conn->exec("PRAGMA foreign_keys = ON;");
                
                // El script de creación mantiene la estructura. 
                // Como ya tienes tablas, 'IF NOT EXISTS' no borrará tus datos actuales.
                $this->conn->exec("
                    CREATE TABLE IF NOT EXISTS usuarios (id_usuario INTEGER PRIMARY KEY AUTOINCREMENT, usuario TEXT UNIQUE, password TEXT, nombre TEXT, rol TEXT, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS categorias_productos (id_categoria INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS productos (id_producto INTEGER PRIMARY KEY AUTOINCREMENT, id_categoria INTEGER, codigo TEXT UNIQUE, nombre TEXT, marca TEXT, stock INTEGER, stock_minimo INTEGER, precio_compra REAL, precio_venta REAL, unidad_medida TEXT, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS servicios (id_servicio INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, descripcion TEXT, precio REAL, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS clientes (id_cliente INTEGER PRIMARY KEY AUTOINCREMENT, tipo_documento TEXT, numero_documento TEXT UNIQUE, nombre TEXT, telefono TEXT, correo TEXT, direccion TEXT, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS vehiculos (id_vehicle INTEGER PRIMARY KEY AUTOINCREMENT, id_cliente INTEGER, placa TEXT UNIQUE, tipo_vehiculo TEXT, marca TEXT, modelo TEXT, anio INTEGER, color TEXT, kilometraje REAL, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS cajas (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, monto_apertura REAL, monto_cierre REAL, fecha_apertura TEXT, fecha_cierre TEXT, estado INTEGER);
                    CREATE TABLE IF NOT EXISTS ventas (id INTEGER PRIMARY KEY AUTOINCREMENT, usuario_id INTEGER, tipo_comprobante TEXT, cliente_tipo_doc TEXT, cliente_num_doc TEXT, cliente_nombre TEXT, total REAL, metodo_pago TEXT, fecha TEXT, estado INTEGER, estado_sunat TEXT);
                    CREATE TABLE IF NOT EXISTS detalle_ventas (id INTEGER PRIMARY KEY AUTOINCREMENT, venta_id INTEGER, item_id INTEGER, tipo_item TEXT, nombre TEXT, precio REAL, cantidad INTEGER, subtotal REAL);
                    CREATE TABLE IF NOT EXISTS servicios_realizados (id_realizado INTEGER PRIMARY KEY AUTOINCREMENT, id_venta INTEGER, id_vehiculo INTEGER, id_servicio INTEGER, observaciones TEXT, kilometraje_actual REAL, proximo_cambio REAL, fecha_registro TEXT);
                    CREATE TABLE IF NOT EXISTS configuracion_empresa (id INTEGER PRIMARY KEY AUTOINCREMENT, ruc TEXT, razon_social TEXT, direccion TEXT, telefono TEXT, email TEXT);
                    INSERT OR IGNORE INTO usuarios (id_usuario, usuario, password, nombre, rol, estado) VALUES (1, 'admin', 'admin', 'Nilson Mayanga', 'ADMIN', 1);
                    CREATE TABLE IF NOT EXISTS gastos (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        monto REAL NOT NULL,
                        descripcion TEXT NOT NULL,
                        fecha TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
                        usuario_id INTEGER,
                        FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
                    );
                ");
            }

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            die("Error de conexión a la Base de Datos: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>