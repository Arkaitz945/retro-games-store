<?php

require_once "../../config/dbConnection.php";

class DireccionModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();

        if (!$this->conn) {
            die("Error: No se pudo conectar a la base de datos");
        }
    }

    /**
     * Obtener las direcciones de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Array con las direcciones
     */
    public function getDireccionesByUsuario($idUsuario)
    {
        try {
            $query = "SELECT * FROM direccion WHERE idUsuario = :idUsuario ORDER BY ID_Direccion DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo direcciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener una dirección por su ID
     * 
     * @param int $idDireccion ID de la dirección
     * @return mixed Array con la dirección o false si no existe
     */
    public function getDireccionById($idDireccion)
    {
        try {
            $query = "SELECT * FROM direccion WHERE ID_Direccion = :idDireccion";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idDireccion", $idDireccion, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error obteniendo dirección: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear una nueva dirección
     * 
     * @param array $direccion Datos de la dirección
     * @return int|bool ID de la dirección creada o false si falla
     */
    public function createDireccion($direccion)
    {
        try {
            $query = "INSERT INTO direccion (calle, numero, codigoPostal, idUsuario) 
                      VALUES (:calle, :numero, :codigoPostal, :idUsuario)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":calle", $direccion['calle']);
            $stmt->bindParam(":numero", $direccion['numero']);
            $stmt->bindParam(":codigoPostal", $direccion['codigoPostal']);
            $stmt->bindParam(":idUsuario", $direccion['idUsuario'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creando dirección: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar una dirección existente
     * 
     * @param array $direccion Datos de la dirección
     * @return bool True si se actualizó, false si falla
     */
    public function updateDireccion($direccion)
    {
        try {
            $query = "UPDATE direccion 
                      SET calle = :calle, 
                          numero = :numero, 
                          codigoPostal = :codigoPostal 
                      WHERE ID_Direccion = :idDireccion 
                        AND idUsuario = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":calle", $direccion['calle']);
            $stmt->bindParam(":numero", $direccion['numero']);
            $stmt->bindParam(":codigoPostal", $direccion['codigoPostal']);
            $stmt->bindParam(":idDireccion", $direccion['ID_Direccion'], PDO::PARAM_INT);
            $stmt->bindParam(":idUsuario", $direccion['idUsuario'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando dirección: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar una dirección
     * 
     * @param int $idDireccion ID de la dirección
     * @param int $idUsuario ID del usuario (para seguridad)
     * @return bool True si se eliminó, false si falla
     */
    public function deleteDireccion($idDireccion, $idUsuario)
    {
        try {
            $query = "DELETE FROM direccion 
                      WHERE ID_Direccion = :idDireccion 
                        AND idUsuario = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idDireccion", $idDireccion, PDO::PARAM_INT);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error eliminando dirección: " . $e->getMessage());
            return false;
        }
    }
}
