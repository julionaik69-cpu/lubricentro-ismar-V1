<?php
class Empresa {
    private $conn;
    private $table_name = "configuracion_empresa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getConfig() {
        try {
            $q = "SELECT * FROM " . $this->table_name . " LIMIT 1";
            $stmt = $this->conn->prepare($q);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function guardar($datos) {
        try {
            // Consulta directa sobre la fila única de tu lubricentro
            $q = "UPDATE configuracion_empresa SET 
                    ruc = :ruc,
                    razon_social = :razon_social,
                    direccion = :direccion,
                    telefono = :telefono,
                    email = :email,
                    usuario_sol = :usuario_sol,
                    clave_sol = :clave_sol
                  WHERE id = 1"; // No dependemos de variables cruzadas en el WHERE
                  
            $stmt = $this->conn->prepare($q);
            return $stmt->execute([
                ':ruc'          => $datos['ruc'],
                ':razon_social' => $datos['razon_social'],
                ':direccion'    => $datos['direccion'],
                ':telefono'     => $datos['telefono'],
                ':email'        => $datos['email'],
                ':usuario_sol'  => $datos['usuario_sol'],
                ':clave_sol'    => $datos['clave_sol']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function guardarCertificado($ruta) {
        try {
            $q = "UPDATE " . $this->table_name . " SET certificado = :cert WHERE id = 1 OR 1=1 LIMIT 1";
            $stmt = $this->conn->prepare($q);
            return $stmt->execute([':cert' => $ruta]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>