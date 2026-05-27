<?php
if (class_exists('Dashboard')) return;

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getVentasHoy() {
        // Adaptado a las funciones de fecha nativas de SQLite
        $q = "SELECT COALESCE(SUM(total), 0) as total FROM ventas 
              WHERE date(fecha) = date('now', 'localtime') AND estado = 1";
        $res = $this->conn->query($q)->fetch(PDO::FETCH_ASSOC);
        return $res ? (float)$res['total'] : 0.00;
    }

    public function getTicketsHoy() {
        $q = "SELECT COUNT(*) as total FROM ventas 
              WHERE date(fecha) = date('now', 'localtime') AND estado = 1";
        $res = $this->conn->query($q)->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['total'] : 0;
    }

    public function getTotalProductos() {
        $q = "SELECT COUNT(*) as total FROM productos WHERE estado = 1";
        $res = $this->conn->query($q)->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['total'] : 0;
    }

    public function getStockBajo() {
        // AUTOMATIZACIÓN: Cuenta los aceites/filtros cuyo stock cayó por debajo o igual al stock mínimo configurado
        $q = "SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo AND estado = 1";
        $res = $this->conn->query($q)->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['total'] : 0;
    }

    public function getVentasSemana() {
        // Cambiado el INTERVAL de Postgres por el modificador '-6 days' nativo de SQLite
        $q = "SELECT date(fecha) as dia, SUM(total) as total 
              FROM ventas 
              WHERE estado = 1 
              AND date(fecha) >= date('now', '-6 days', 'localtime')
              GROUP BY date(fecha) 
              ORDER BY date(fecha) ASC";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentasMes() {
        $q = "SELECT COALESCE(SUM(total), 0) as total 
              FROM ventas 
              WHERE estado = 1 
              AND date(fecha) >= date('now', '-30 days', 'localtime')";
        $res = $this->conn->query($q)->fetch(PDO::FETCH_ASSOC);
        return $res ? (float)$res['total'] : 0.00;
    }

    public function getVentasRecientes($limite = 5) {
        // Recupera el historial de las últimas transacciones procesadas en el lubricentro
        $q = "SELECT v.*, u.nombre as vendedor 
              FROM ventas v 
              JOIN usuarios u ON v.usuario_id = u.id_usuario
              WHERE v.estado = 1
              ORDER BY v.fecha DESC LIMIT :lim";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDatosGraficoSemanales() {
        try {
            // Forzamos a SQLite a interpretar las fechas usando 'localtime' en el rango y en el formateo
            $q = "SELECT date(fecha) as fecha, SUM(total) as total 
                  FROM ventas 
                  WHERE date(fecha) >= date('now', '-7 days', 'localtime') 
                  GROUP BY date(fecha) 
                  ORDER BY date(fecha) ASC";
                  
            $stmt = $this->conn->prepare($q);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

}
?>