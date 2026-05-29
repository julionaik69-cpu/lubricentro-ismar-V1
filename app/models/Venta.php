<?php
if (class_exists('Venta')) return;

class Venta {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function buscarProducto($termino) {
        // Corregido: LEFT JOIN hacia categorias_productos
        $query = "SELECT p.id_producto, p.nombre, p.stock, p.precio_venta, p.marca, p.unidad_medida, p.codigo, c.nombre as categoria
                  FROM productos p
                  LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
                  WHERE (p.nombre LIKE :term OR p.marca LIKE :term OR p.codigo LIKE :term OR c.nombre LIKE :term) 
                  AND p.stock > 0
                  AND p.estado = 1
                  ORDER BY p.nombre ASC
                  LIMIT 50";
        $stmt = $this->conn->prepare($query);
        $term = "%$termino%";
        $stmt->bindParam(':term', $term);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarVenta($id_usuario, $tipo_comprobante, $cliente_tipo_doc, $cliente_num_doc, $cliente_nombre, $total_carrito, $carrito, $metodo_pago) {
        try {
            $this->conn->beginTransaction();

            $cajaQuery = "SELECT id FROM cajas WHERE usuario_id = :uid AND estado = 1 LIMIT 1";
            $stmtCaja  = $this->conn->prepare($cajaQuery);
            $stmtCaja->bindParam(':uid', $id_usuario);
            $stmtCaja->execute();
            $caja    = $stmtCaja->fetch(PDO::FETCH_ASSOC);
            $caja_id = $caja ? $caja['id'] : null;

            $total_final = floatval($total_carrito);
            $op_gravadas = round($total_final / 1.18, 2);
            $igv_total   = round($total_final - $op_gravadas, 2);

            $stmtSerie = $this->conn->prepare("SELECT serie, correlativo FROM series_comprobantes WHERE tipo_comprobante = :tc AND estado = 1 LIMIT 1");
            $stmtSerie->bindParam(':tc', $tipo_comprobante);
            $stmtSerie->execute();
            $serieData = $stmtSerie->fetch(PDO::FETCH_ASSOC);

            if (!$serieData) {
                $serie = ($tipo_comprobante == '01') ? 'F001' : 'B001';
                $correlativo_str = "000001";
            } else {
                $serie = $serieData['serie'];
                $correlativo_num = $serieData['correlativo'];
                $correlativo_str = str_pad($correlativo_num, 6, "0", STR_PAD_LEFT);
            }

            $queryVenta = "INSERT INTO ventas 
               (usuario_id, tipo_comprobante, cliente_tipo_doc, cliente_num_doc, cliente_nombre, total, metodo_pago, estado, estado_sunat, fecha) 
               VALUES 
               (:usr, :tc, :ctd, :cnd, :cnom, :tot, :met, 1, 'REGISTRADO', NOW())";
            
            $stmt = $this->conn->prepare($queryVenta);
            $stmt->bindParam(':usr',      $id_usuario);
            $stmt->bindParam(':tc',       $tipo_comprobante);
            $stmt->bindParam(':ctd',      $cliente_tipo_doc);
            $stmt->bindParam(':cnd',      $cliente_num_doc);
            $stmt->bindParam(':cnom',     $cliente_nombre);
            $stmt->bindParam(':tot',      $total_final);
            $stmt->bindParam(':met',      $metodo_pago);
            $stmt->execute();
            
            $id_venta = $this->conn->lastInsertId();

            foreach ($carrito as $item) {
                if ($item['tipo'] === 'PRODUCTO') {
                    $stmtStock = $this->conn->prepare("SELECT stock FROM productos WHERE id_producto = :pid");
                    $stmtStock->bindParam(':pid', $item['id']);
                    $stmtStock->execute();
                    $prodData = $stmtStock->fetch(PDO::FETCH_ASSOC);

                    if (!$prodData || $prodData['stock'] < $item['cantidad']) {
                        $this->conn->rollBack();
                        return ['ok' => false, 'msg' => "Existencias insuficientes en almacén para: {$item['nombre']}"];
                    }

                    $stmtUpd = $this->conn->prepare("UPDATE productos SET stock = stock - :cant WHERE id_producto = :id");
                    $stmtUpd->bindParam(':cant', $item['cantidad']);
                    $stmtUpd->bindParam(':id',   $item['id']);
                    $stmtUpd->execute();
                }

                $queryDetalle = "INSERT INTO detalle_ventas 
                                 (venta_id, item_id, tipo_item, nombre, precio, cantidad, subtotal) 
                                 VALUES (:vid, :item_id, :tipo_item, :nombre, :precio, :cantidad, :sub)";
                
                $stmtDet = $this->conn->prepare($queryDetalle);
                $stmtDet->bindValue(':vid',       $id_venta);
                $stmtDet->bindValue(':item_id',   $item['id']);
                $stmtDet->bindValue(':tipo_item', $item['tipo']);
                $stmtDet->bindValue(':nombre',    $item['nombre']);
                $stmtDet->bindValue(':precio',    $item['precio']);
                $stmtDet->bindValue(':cantidad',  $item['cantidad']);
                $stmtDet->bindValue(':sub',       $item['subtotal']);
                $stmtDet->execute();
            }

            if ($serieData) {
                $stmtUpdSerie = $this->conn->prepare("UPDATE series_comprobantes SET correlativo = correlativo + 1 WHERE tipo_comprobante = :tc");
                $stmtUpdSerie->bindParam(':tc', $tipo_comprobante);
                $stmtUpdSerie->execute();
            }

            $this->conn->commit();
            return ['ok' => true, 'id' => $id_venta];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['ok' => false, 'msg' => $e->getMessage()];
        }
    }

    public function listarVentas($filtro = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filtro['fecha_inicio'])) {
            $where .= " AND date(v.fecha) >= :f_ini";
            $params[':f_ini'] = $filtro['fecha_inicio'];
        }
        if (!empty($filtro['fecha_fin'])) {
            $where .= " AND date(v.fecha) <= :f_fin";
            $params[':f_fin'] = $filtro['fecha_fin'];
        }
        if (isset($filtro['estado']) && $filtro['estado'] !== '') {
            $where .= " AND v.estado = :est";
            $params[':est'] = $filtro['estado'];
        }

        $query = "SELECT v.*, u.nombre as vendedor 
                  FROM ventas v
                  JOIN usuarios u ON v.usuario_id = u.id_usuario
                  WHERE $where
                  ORDER BY v.fecha DESC";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentaById($id) {
        $query = "SELECT v.*, u.nombre as vendedor 
                  FROM ventas v
                  JOIN usuarios u ON v.usuario_id = u.id_usuario
                  WHERE v.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetalleVenta($id) {
        $query = "SELECT * FROM detalle_ventas WHERE venta_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function anularVenta($id_venta) {
        try {
            $this->conn->beginTransaction();
            $detalles = $this->getDetalleVenta($id_venta);
            
            foreach ($detalles as $item) {
                if ($item['tipo_item'] === 'PRODUCTO') {
                    $stmt = $this->conn->prepare("UPDATE productos SET stock = stock + :cant WHERE id_producto = :pid");
                    $stmt->execute([':cant' => $item['cantidad'], ':pid' => $item['item_id']]);
                }
            }
            
            $stmt = $this->conn->prepare("UPDATE ventas SET estado = 0, estado_sunat = 'ANULADO' WHERE id = :id");
            $stmt->bindParam(':id', $id_venta);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function listarProductosDisponibles($limite = 20) {
        // Corregido: LEFT JOIN hacia categorias_productos
        $query = "SELECT p.id_producto, p.nombre, p.stock, p.precio_venta, p.marca, p.unidad_medida, p.codigo, c.nombre as categoria
                  FROM productos p
                  LEFT JOIN categorias_productos c ON p.id_categoria = c.id_categoria
                  WHERE p.stock > 0 AND p.estado = 1
                  ORDER BY p.nombre ASC
                  LIMIT :limite";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarCategorias() {
        $query = "SELECT id_categoria, nombre FROM categorias_productos ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>