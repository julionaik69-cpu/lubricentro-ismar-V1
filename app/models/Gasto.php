<?php
if (class_exists('Gasto')) return;

class Gasto {
    private $conn;
    private $table_name = "gastos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Calcular el monto acumulado en egresos del turno operativo
    public function getTotalGastos($id_caja) {
        $qCaja = "SELECT fecha_apertura, usuario_id FROM cajas WHERE id = :id LIMIT 1";
        $stmtC = $this->conn->prepare($qCaja);
        $stmtC->execute([':id' => $id_caja]);
        $caja = $stmtC->fetch(PDO::FETCH_ASSOC);

        if (!$caja) return 0.00;

        $fechaApertura = $caja['fecha_apertura'];
        $usuarioId = $caja['usuario_id'];

        $query = "SELECT COALESCE(SUM(monto), 0) as total FROM " . $this->table_name . " 
                  WHERE usuario_id = :uid AND fecha >= :fap";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $usuarioId);
        $stmt->bindParam(':fap', $fechaApertura);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float)$row['total'];
    }

    // LISTAR DETALLES: Método obligatorio para la vista de Cierre de Caja
    public function listarPorCaja($id_caja) {
        // 1. Obtener datos de la caja para saber cuándo se abrió y quién la abrió
        $qCaja = "SELECT fecha_apertura, usuario_id FROM cajas WHERE id = :id LIMIT 1";
        $stmtC = $this->conn->prepare($qCaja);
        $stmtC->execute([':id' => $id_caja]);
        $caja = $stmtC->fetch(PDO::FETCH_ASSOC);

        if (!$caja) return [];

        // 2. Traer los gastos registrados por ese usuario desde la apertura del turno
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = :uid AND fecha >= :fap 
                  ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $caja['usuario_id']);
        $stmt->bindParam(':fap', $caja['fecha_apertura']);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($monto, $descripcion, $usuario_id) {
        $query = "INSERT INTO " . $this->table_name . " (monto, descripcion, usuario_id, fecha) 
                  VALUES (:monto, :desc, :uid, datetime('now', 'localtime'))";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':uid', $usuario_id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>