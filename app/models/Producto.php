<?php
if (class_exists('Producto')) return;

class Producto {
    private $conn;
    private $table = "productos";

    // Mapeo exacto de las columnas de tu BD de lubricentro
    public $id_producto;
    public $id_categoria;
    public $codigo;
    public $nombre;
    public $marca;
    public $stock;
    public $stock_minimo;
    public $precio_compra;
    public $precio_venta;
    public $unidad_medida;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        // Ajustamos los campos al diseño del lubricentro y su categoría de aceites/filtros
        $query = "SELECT p.*, c.nombre as nombre_categoria 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
                  WHERE p.estado = 1
                  ORDER BY p.id_producto ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.*, c.nombre as nombre_categoria 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
                  WHERE p.id_producto = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeNombre($nombre, $excluir_id = null) {
        $nombre = strtolower(trim($nombre));
        $query = "SELECT id_producto FROM " . $this->table . " WHERE LOWER(TRIM(nombre)) = :nombre AND estado = 1";
        
        if ($excluir_id) {
            $query .= " AND id_producto != :excluir";
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
        if (empty($this->codigo)) {
            $this->codigo = $this->generarCodigoUnico();
        }

        if ($this->existeNombre($this->nombre)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (codigo, nombre, id_categoria, marca, stock, stock_minimo, precio_compra, precio_venta, unidad_medida, estado) 
                  VALUES (:codigo, :nombre, :cat, :marca, :stock, :stock_min, :p_compra, :p_venta, :unidad, 1)";
        
        $stmt = $this->conn->prepare($query);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->marca = htmlspecialchars(strip_tags($this->marca));

        $stmt->bindParam(':codigo', $this->codigo);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':cat', $this->id_categoria);
        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':stock_min', $this->stock_minimo);
        $stmt->bindParam(':p_compra', $this->precio_compra);
        $stmt->bindParam(':p_venta', $this->precio_venta);
        $stmt->bindParam(':unidad', $this->unidad_medida);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    private function generarCodigoUnico() {
        $prefijo = 'LUB';
        $fecha = date('YmdHis');
        $random = rand(100, 999);
        $codigo = $prefijo . '-' . $fecha . '-' . $random;
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE codigo = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            return $this->generarCodigoUnico();
        }
        
        return $codigo;
    }

    public function actualizar($id) {
        if ($this->existeNombre($this->nombre, $id)) {
            return false;
        }

        if (empty($this->codigo)) {
            $this->codigo = $this->generarCodigoUnico();
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET codigo = :codigo, nombre = :nombre, id_categoria = :cat, 
                      marca = :marca, stock_minimo = :stock_min, 
                      precio_compra = :p_compra, precio_venta = :p_venta, unidad_medida = :unidad
                  WHERE id_producto = :id";
        
        $stmt = $this->conn->prepare($query);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->marca = htmlspecialchars(strip_tags($this->marca));

        $stmt->bindParam(':codigo', $this->codigo);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':cat', $this->id_categoria);
        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':stock_min', $this->stock_minimo);
        $stmt->bindParam(':p_compra', $this->precio_compra);
        $stmt->bindParam(':p_venta', $this->precio_venta);
        $stmt->bindParam(':unidad', $this->unidad_medida);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>