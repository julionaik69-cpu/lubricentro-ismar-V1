<?php
if (class_exists('Caja')) return;

class Caja {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerCajaAbierta($usuario_id) {
        $query = "SELECT * FROM cajas 
                  WHERE usuario_id = :uid AND estado = 1 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function abrir($usuario_id, $monto_inicial) {
        $existente = $this->obtenerCajaAbierta($usuario_id);
        if ($existente) return false;

        // MODIFICADO: NOW() para PostgreSQL
        $query = "INSERT INTO cajas (usuario_id, monto_apertura, estado, fecha_apertura) 
                  VALUES (:uid, :monto, 1, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->bindParam(':monto', $monto_inicial);
        return $stmt->execute();
    }

    public function calcularTotales($id_caja) {
        // Recuperar los datos de la caja para auditar por rango de tiempo exacto del turno
        $qCaja = "SELECT fecha_apertura, usuario_id FROM cajas WHERE id = :id";
        $stC = $this->conn->prepare($qCaja);
        $stC->execute([':id' => $id_caja]);
        $cData = $stC->fetch(PDO::FETCH_ASSOC);
        
        $fApertura = $cData['fecha_apertura'] ?? date('Y-m-d H:i:s');
        $uId = $cData['usuario_id'] ?? 0;

        // Total global de ventas del turno
        $sqlTotal = "SELECT COALESCE(SUM(total), 0) as total FROM ventas
                     WHERE usuario_id = :uid AND fecha >= :fap AND estado = 1";
        $stmt = $this->conn->prepare($sqlTotal);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $total_global = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Efectivo
        $sqlEfectivo = "SELECT COALESCE(SUM(total), 0) as total FROM ventas
                        WHERE usuario_id = :uid AND fecha >= :fap AND estado = 1 AND metodo_pago = 'EFECTIVO'";
        $stmt = $this->conn->prepare($sqlEfectivo);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $total_efectivo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Yape / Plin
        $sqlYape = "SELECT COALESCE(SUM(total), 0) as total FROM ventas
                    WHERE usuario_id = :uid AND fecha >= :fap AND estado = 1 AND metodo_pago = 'YAPE'";
        $stmt = $this->conn->prepare($sqlYape);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $total_yape = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tarjeta
        $sqlTarjeta = "SELECT COALESCE(SUM(total), 0) as total FROM ventas
                       WHERE usuario_id = :uid AND fecha >= :fap AND estado = 1 AND metodo_pago = 'TARJETA'";
        $stmt = $this->conn->prepare($sqlTarjeta);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $total_tarjeta = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Gastos vinculados
        $sqlGastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos 
                      WHERE usuario_id = :uid AND fecha >= :fap";
        $stmt = $this->conn->prepare($sqlGastos);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $total_gastos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Cantidad de comprobantes emitidos
        $sqlCantidad = "SELECT COUNT(*) as total FROM ventas 
                        WHERE usuario_id = :uid AND fecha >= :fap AND estado = 1";
        $stmt = $this->conn->prepare($sqlCantidad);
        $stmt->execute([':uid' => $uId, ':fap' => $fApertura]);
        $cantidad_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'venta_total'      => (float)$total_global,
            'venta_efectivo'   => (float)$total_efectivo,
            'venta_yape'       => (float)$total_yape,
            'venta_tarjeta'    => (float)$total_tarjeta,
            'venta_digital'    => (float)($total_yape + $total_tarjeta),
            'gastos'           => (float)$total_gastos,
            'cantidad_tickets' => (int)$cantidad_tickets
        ];
    }

    public function cerrar($id_caja, $total_ventas, $monto_final) {
        // MODIFICADO: NOW() para PostgreSQL
        $query = "UPDATE cajas 
                  SET fecha_cierre = NOW(), 
                      monto_cierre  = :final,
                      monto_apertura = monto_apertura, 
                      estado       = 0 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':final', $monto_final);
        $stmt->bindParam(':id',    $id_caja);
        return $stmt->execute();
    }

    public function getHistorial($fecha_inicio, $fecha_fin) {
        // Adaptación para PostgreSQL (funciona igual que SQLite aquí)
        $sql = "SELECT c.*, u.nombre as cajero,
                    (SELECT COALESCE(SUM(v.total), 0) FROM ventas v WHERE v.usuario_id = c.usuario_id AND v.fecha >= c.fecha_apertura AND v.estado = 1) as ventas_turno,
                    (SELECT COALESCE(SUM(g.monto), 0) FROM gastos g WHERE g.usuario_id = c.usuario_id AND g.fecha >= c.fecha_apertura) as gastos_turno,
                    (SELECT COUNT(*) FROM ventas v WHERE v.usuario_id = c.usuario_id AND v.fecha >= c.fecha_apertura AND v.estado = 1) as num_tickets
                FROM cajas c
                JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE DATE(c.fecha_apertura) BETWEEN :f1 AND :f2
                ORDER BY c.fecha_apertura DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':f1', $fecha_inicio);
        $stmt->bindParam(':f2', $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>