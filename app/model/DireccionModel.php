<?php

require_once __DIR__ . "/../config/database.php";

class DireccionModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();

        // Verificar si la conexión se estableció correctamente
        if (!$this->conn) {
            error_log("DireccionModel: Error al conectar a la base de datos. La conexión es nula.");
        }
    }

    /**
     * Obtiene la dirección de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array|null Datos de la dirección o null si no existe
     */
    public function getDireccionByUsuario($idUsuario)
    {
        try {
            // Verificar si hay conexión antes de continuar
            if (!$this->conn) {
                error_log("DireccionModel: No se puede obtener dirección - La conexión a la base de datos es nula");
                return null;
            }

            // Verificar si la tabla existe antes de la consulta
            $tableExistsQuery = "SHOW TABLES LIKE 'direccion'";
            $tableExistsStmt = $this->conn->prepare($tableExistsQuery);
            $tableExistsStmt->execute();

            if ($tableExistsStmt->rowCount() == 0) {
                error_log("DireccionModel: La tabla 'direccion' no existe en la base de datos");
                return null;
            }

            $query = "SELECT * FROM direccion WHERE idUsuario = :idUsuario LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario);
            $stmt->execute();

            $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

            // Registrar si se encontró dirección o no
            if ($direccion) {
                error_log("DireccionModel: Dirección encontrada para el usuario ID: " . $idUsuario);
            } else {
                error_log("DireccionModel: No se encontró dirección para el usuario ID: " . $idUsuario);
            }

            return $direccion ?: null;
        } catch (PDOException $e) {
            error_log("DireccionModel: Error al obtener dirección - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene todas las direcciones de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Lista de direcciones
     */
    public function getDireccionesByUsuario($idUsuario)
    {
        try {
            $query = "SELECT * FROM direccion WHERE idUsuario = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("DireccionModel: Error al obtener direcciones - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea una nueva dirección
     * 
     * @param array $direccion Datos de la dirección
     * @return bool|int ID de la dirección creada o false en caso de error
     */
    public function createDireccion($direccion)
    {
        try {
            $query = "INSERT INTO direccion (calle, numero, codigoPostal, idUsuario) 
                      VALUES (:calle, :numero, :codigoPostal, :idUsuario)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':calle', $direccion['calle']);
            $stmt->bindParam(':numero', $direccion['numero']);
            $stmt->bindParam(':codigoPostal', $direccion['codigoPostal']);
            $stmt->bindParam(':idUsuario', $direccion['idUsuario']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("DireccionModel: Error al crear dirección - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una dirección existente
     * 
     * @param int $idDireccion ID de la dirección
     * @param array $direccion Datos de la dirección
     * @return bool Resultado de la operación
     */
    public function updateDireccion($idDireccion, $direccion)
    {
        try {
            $query = "UPDATE direccion 
                      SET calle = :calle, numero = :numero, codigoPostal = :codigoPostal
                      WHERE ID_Direccion = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':calle', $direccion['calle']);
            $stmt->bindParam(':numero', $direccion['numero']);
            $stmt->bindParam(':codigoPostal', $direccion['codigoPostal']);
            $stmt->bindParam(':id', $idDireccion);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DireccionModel: Error al actualizar dirección - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una dirección
     * 
     * @param int $idDireccion ID de la dirección
     * @param int $idUsuario ID del usuario (para verificación de seguridad)
     * @return bool Resultado de la operación
     */
    public function deleteDireccion($idDireccion, $idUsuario)
    {
        try {
            $query = "DELETE FROM direccion 
                      WHERE ID_Direccion = :id AND idUsuario = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $idDireccion);
            $stmt->bindParam(':idUsuario', $idUsuario);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DireccionModel: Error al eliminar dirección - " . $e->getMessage());
            return false;
        }
    }
}
