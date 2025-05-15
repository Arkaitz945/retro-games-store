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

        if (!$this->conn) {
            die("Error: No se pudo conectar a la base de datos");
        }
    }

    /**
     * Obtiene todas las consolas
     * 
     * @param array $filtros Filtros a aplicar (fabricante, estado, precio_max)
     * @return array Lista de consolas
     */
    public function getAllConsolas($filtros = [])
    {
        try {
            $query = "SELECT * FROM consolas";
            $condiciones = [];
            $params = [];

            // Aplicar filtros si existen
            if (!empty($filtros)) {
                // Filtro por fabricante
                if (isset($filtros['fabricante']) && !empty($filtros['fabricante'])) {
                    $condiciones[] = "fabricante = :fabricante";
                    $params[':fabricante'] = $filtros['fabricante'];
                }

                // Filtro por precio máximo
                if (isset($filtros['precio_max']) && is_numeric($filtros['precio_max'])) {
                    $condiciones[] = "precio <= :precio_max";
                    $params[':precio_max'] = $filtros['precio_max'];
                }

                // Filtro por estado
                if (isset($filtros['estado']) && !empty($filtros['estado'])) {
                    $condiciones[] = "estado = :estado";
                    $params[':estado'] = $filtros['estado'];
                }

                // Añadir condiciones a la consulta
                if (!empty($condiciones)) {
                    $query .= " WHERE " . implode(" AND ", $condiciones);
                }
            }

            // Ordenar por nombre
            $query .= " ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($query);

            // Bind params
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo consolas: " . $e->getMessage());
            return [];
        }
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

    /**
     * Obtener consolas relacionadas por fabricante
     * 
     * @param int $idConsola ID de la consola a excluir
     * @param string $fabricante Fabricante para relacionar
     * @param int $limit Número máximo de resultados
     * @return array Consolas relacionadas
     */
    public function getConsolasRelacionadas($idConsola, $fabricante, $limit = 4)
    {
        try {
            $query = "SELECT * FROM consolas WHERE ID_Consola != :idConsola AND fabricante = :fabricante ORDER BY RAND() LIMIT :limit";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idConsola', $idConsola, PDO::PARAM_INT);
            $stmt->bindParam(':fabricante', $fabricante);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo consolas relacionadas: " . $e->getMessage());
            return [];
        }
    }
}
