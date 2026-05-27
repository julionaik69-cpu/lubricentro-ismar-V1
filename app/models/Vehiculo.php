<?php
if (class_exists('Vehiculo')) return;

class Vehiculo {
    private $conn;
    private $table = "vehiculos";

    public $id_vehicle;
    public $id_cliente;
    public $placa;
    public $marca;
    public $modelo;
    public $anio;
    public $color;
    public $kilometraje;
    public $tipo_vehiculo;
    public $dias_alerta;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        // Hacemos un JOIN para saber a qué cliente le pertenece cada auto en las listas generales
        $query = "SELECT v.*, c.nombre as nombre_cliente, c.numero_documento 
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                  ORDER BY v.id_vehicle DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT v.*, c.nombre as nombre_cliente 
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                  WHERE v.id_vehicle = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarPorCliente($id_cliente) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_cliente = :id_cli ORDER BY placa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cli', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existePlaca($placa, $excluir_id = null) {
        $placa_clean = strtoupper(trim($placa));
        $query = "SELECT id_vehicle FROM " . $this->table . " WHERE UPPER(TRIM(placa)) = :placa";
        if ($excluir_id) {
            $query .= " AND id_vehicle != :excluir";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':placa', $placa_clean);
        if ($excluir_id) {
            $stmt->bindParam(':excluir', $excluir_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function crear() {
        // Forzar placa en mayúsculas (Estándar de Perú: ABC-123)
        $this->placa = strtoupper(trim($this->placa));

        if ($this->existePlaca($this->placa)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (id_cliente, placa, marca, modelo, anio, color, kilometraje, tipo_vehiculo) 
                  VALUES (:id_cli, :placa, :marca, :modelo, :anio, :color, :km, :tipo)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_cli', $this->id_cliente);
        $stmt->bindParam(':placa', $this->placa);
        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':modelo', $this->modelo);
        $stmt->bindParam(':anio', $this->anio);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':km', $this->kilometraje);
        $stmt->bindParam(':tipo', $this->tipo_vehiculo);

        return $stmt->execute();
    }

    public function actualizar($id) {
    $this->placa = strtoupper(trim($this->placa));

    if ($this->existePlaca($this->placa, $id)) {
        return false;
    }

    $query = "UPDATE " . $this->table . " 
              SET id_cliente = :id_cli, placa = :placa, marca = :marca, modelo = :modelo, 
                  anio = :anio, color = :color, kilometraje = :km, tipo_vehiculo = :tipo,
                  dias_alerta = :dias_alerta
              WHERE id_vehicle = :id";
    
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':id_cli', $this->id_cliente);
    $stmt->bindParam(':placa', $this->placa);
    $stmt->bindParam(':marca', $this->marca);
    $stmt->bindParam(':modelo', $this->modelo);
    $stmt->bindParam(':anio', $this->anio);
    $stmt->bindParam(':color', $this->color);
    $stmt->bindParam(':km', $this->kilometraje);
    $stmt->bindParam(':tipo', $this->tipo_vehiculo);
    $stmt->bindParam(':dias_alerta', $this->dias_alerta);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}

    // Método especial: Cuando se registre un servicio, actualizaremos directo el odómetro del carro
    public function actualizarKilometraje($id, $nuevo_km) {
        $query = "UPDATE " . $this->table . " SET kilometraje = :km WHERE id_vehicle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':km', $nuevo_km);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_vehicle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>