<?php
if (class_exists('Categoria')) return;

class Categoria {
    private $conn;
    private $table = "categorias_productos"; // Mapeo exacto a la tabla del Lubricentro

    public $id_categoria;
    public $nombre;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1 ORDER BY id_categoria ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // Devolvemos el PDO Statement tal como lo espera el fetchAll del CategoriaController actual
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_categoria = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeNombre($nombre, $excluir_id = null) {
        $nombre = strtolower(trim($nombre));
        $query = "SELECT id_categoria FROM " . $this->table . " WHERE LOWER(TRIM(nombre)) = :nombre AND estado = 1";
        
        if ($excluir_id) {
            $query .= " AND id_categoria != :excluir";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        if ($excluir_id) {
            $stmt->bindParam(':excluir', $excluir_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function crear() {
        if ($this->existeNombre($this->nombre)) {
            return false; 
        }

        $query = "INSERT INTO " . $this->table . " (nombre, estado) VALUES (:nombre, 1)";
        $stmt = $this->conn->prepare($query);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $stmt->bindParam(':nombre', $this->nombre);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre) {
        if ($this->existeNombre($nombre, $id)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET nombre = :nombre WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $nombre = htmlspecialchars(strip_tags($nombre));
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Borrado lógico para proteger la integridad del Kardex e historial de inventario
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>