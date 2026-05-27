<?php
if (class_exists('Servicio')) return;

class Servicio {
    private $conn;
    private $table = "servicios";

    public $id_servicio;
    public $nombre;
    public $descripcion;
    public $precio;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1 ORDER BY id_servicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_servicio = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeNombre($nombre, $excluir_id = null) {
        $nom = strtolower(trim($nombre));
        $query = "SELECT id_servicio FROM " . $this->table . " WHERE LOWER(TRIM(nombre)) = :nom AND estado = 1";
        if ($excluir_id) {
            $query .= " AND id_servicio != :excluir";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
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

        $query = "INSERT INTO " . $this->table . " (nombre, descripcion, precio, estado) VALUES (:nom, :desc, :precio, 1)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(':nom', $this->nombre);
        $stmt->bindParam(':desc', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);

        return $stmt->execute();
    }

    public function actualizar($id) {
        if ($this->existeNombre($this->nombre, $id)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET nombre = :nom, descripcion = :desc, precio = :precio WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(':nom', $this->nombre);
        $stmt->bindParam(':desc', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Hacemos un borrado lógico cambiando el estado a 0 para no romper el historial de servicios antiguos
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>