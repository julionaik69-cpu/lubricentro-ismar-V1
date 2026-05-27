<?php
if (class_exists('Cliente')) return;

class Cliente {
    private $conn;
    private $table = "clientes";

    public $id_cliente;
    public $tipo_documento;
    public $numero_documento;
    public $nombre;
    public $telefono;
    public $direccion;
    public $correo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id_cliente DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_cliente = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeDocumento($numero_documento, $excluir_id = null) {
        $doc = trim($numero_documento);
        $query = "SELECT id_cliente FROM " . $this->table . " WHERE numero_documento = :doc";
        if ($excluir_id) {
            $query .= " AND id_cliente != :excluir";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doc', $doc);
        if ($excluir_id) {
            $stmt->bindParam(':excluir', $excluir_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function crear() {
        if ($this->existeDocumento($this->numero_documento)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (tipo_documento, numero_documento, nombre, telefono, direccion, correo) 
                  VALUES (:tipo, :num, :nom, :tel, :dir, :correo)";
        
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $stmt->bindParam(':tipo', $this->tipo_documento);
        $stmt->bindParam(':num', $this->numero_documento);
        $stmt->bindParam(':nom', $this->nombre);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':correo', $this->correo);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function actualizar($id) {
        if ($this->existeDocumento($this->numero_documento, $id)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " 
                  SET tipo_documento = :tipo, numero_documento = :num, nombre = :nom, 
                      telefono = :tel, direccion = :dir, correo = :correo 
                  WHERE id_cliente = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $stmt->bindParam(':tipo', $this->tipo_documento);
        $stmt->bindParam(':num', $this->numero_documento);
        $stmt->bindParam(':nom', $this->nombre);
        $stmt->bindParam(':tel', $this->telefono);
        $stmt->bindParam(':dir', $this->direccion);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Al usar SQLite con llaves foráneas ON DELETE CASCADE, si se borra un cliente se irán sus autos de forma segura.
        $query = "DELETE FROM " . $this->table . " WHERE id_cliente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>