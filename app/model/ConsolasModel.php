<?php

// Fix the path to the database connection file
require_once __DIR__ . "/../../config/dbConnection.php";

class ConsolasModel
{
    private $conn;

    public function __construct()
    {
        // Get connection using the existing function
        $this->conn = getDBConnection();
    }

    /**
     * Obtiene todas las consolas
     */
    public function getAllConsolas()
    {
        $query = "SELECT * FROM consolas ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una consola por su ID
     */
    public function getConsolaById($id)
    {
        $query = "SELECT * FROM consolas WHERE ID_Consola = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva consola
     */
    public function createConsola($consola)
    {
        $query = "INSERT INTO consolas (nombre, fabricante, año_lanzamiento, descripcion, estado, precio, stock, imagen) 
                 VALUES (:nombre, :fabricante, :anio, :descripcion, :estado, :precio, :stock, :imagen)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':nombre', $consola['nombre']);
        $stmt->bindParam(':fabricante', $consola['fabricante']);
        $stmt->bindParam(':anio', $consola['anio_lanzamiento']);
        $stmt->bindParam(':descripcion', $consola['descripcion']);
        $stmt->bindParam(':estado', $consola['estado']);
        $stmt->bindParam(':precio', $consola['precio']);
        $stmt->bindParam(':stock', $consola['stock']);
        $stmt->bindParam(':imagen', $consola['imagen']);

        return $stmt->execute();
    }

    /**
     * Actualiza una consola existente
     */
    public function updateConsola($id, $consola)
    {
        $query = "UPDATE consolas SET 
                nombre = :nombre, 
                fabricante = :fabricante, 
                año_lanzamiento = :anio, 
                descripcion = :descripcion, 
                estado = :estado, 
                precio = :precio, 
                stock = :stock";

        // Añadir imagen solo si se proporciona
        if (!empty($consola['imagen'])) {
            $query .= ", imagen = :imagen";
        }

        $query .= " WHERE ID_Consola = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':nombre', $consola['nombre']);
        $stmt->bindParam(':fabricante', $consola['fabricante']);
        $stmt->bindParam(':anio', $consola['anio_lanzamiento']);
        $stmt->bindParam(':descripcion', $consola['descripcion']);
        $stmt->bindParam(':estado', $consola['estado']);
        $stmt->bindParam(':precio', $consola['precio']);
        $stmt->bindParam(':stock', $consola['stock']);
        $stmt->bindParam(':id', $id);

        // Bind imagen solo si se proporciona
        if (!empty($consola['imagen'])) {
            $stmt->bindParam(':imagen', $consola['imagen']);
        }

        return $stmt->execute();
    }

    /**
     * Elimina una consola
     */
    public function deleteConsola($id)
    {
        $query = "DELETE FROM consolas WHERE ID_Consola = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Obtiene fabricantes únicos de consolas
     */
    public function getFabricantes()
    {
        $query = "SELECT DISTINCT fabricante FROM consolas ORDER BY fabricante";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_column($result, 'fabricante');
    }

    /**
     * Obtiene estados únicos de consolas
     */
    public function getEstados()
    {
        $query = "SELECT DISTINCT estado FROM consolas ORDER BY estado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_column($result, 'estado');
    }
}
