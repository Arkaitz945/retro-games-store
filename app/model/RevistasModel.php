<?php

// Fix the path to the database connection file
require_once __DIR__ . "/../../config/dbConnection.php";

class RevistasModel
{
    private $conn;

    public function __construct()
    {
        // Get connection using the existing function
        $this->conn = getDBConnection();
    }

    /**
     * Obtiene todas las revistas
     */
    public function getAllRevistas($filtros = [])
    {
        $query = "SELECT * FROM revistas WHERE 1=1";
        $params = [];

        // Aplicar filtros si existen
        if (!empty($filtros['editorial'])) {
            $query .= " AND editorial = :editorial";
            $params[':editorial'] = $filtros['editorial'];
        }

        if (!empty($filtros['precio_max']) && is_numeric($filtros['precio_max'])) {
            $query .= " AND precio <= :precio_max";
            $params[':precio_max'] = $filtros['precio_max'];
        }

        $query .= " ORDER BY titulo ASC";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una revista por su ID
     */
    public function getRevistaById($id)
    {
        $query = "SELECT * FROM revistas WHERE ID_Revista = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva revista
     */
    public function createRevista($revista)
    {
        $query = "INSERT INTO revistas (titulo, editorial, fecha_publicacion, descripcion, precio, stock, imagen) 
                 VALUES (:titulo, :editorial, :fecha, :descripcion, :precio, :stock, :imagen)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':titulo', $revista['titulo']);
        $stmt->bindParam(':editorial', $revista['editorial']);
        $stmt->bindParam(':fecha', $revista['fecha_publicacion']);
        $stmt->bindParam(':descripcion', $revista['descripcion']);
        $stmt->bindParam(':precio', $revista['precio']);
        $stmt->bindParam(':stock', $revista['stock']);
        $stmt->bindParam(':imagen', $revista['imagen']);

        return $stmt->execute();
    }

    /**
     * Actualiza una revista existente
     */
    public function updateRevista($id, $revista)
    {
        $query = "UPDATE revistas SET 
                titulo = :titulo, 
                editorial = :editorial, 
                fecha_publicacion = :fecha, 
                descripcion = :descripcion, 
                precio = :precio, 
                stock = :stock";

        // Añadir imagen solo si se proporciona
        if (!empty($revista['imagen'])) {
            $query .= ", imagen = :imagen";
        }

        $query .= " WHERE ID_Revista = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':titulo', $revista['titulo']);
        $stmt->bindParam(':editorial', $revista['editorial']);
        $stmt->bindParam(':fecha', $revista['fecha_publicacion']);
        $stmt->bindParam(':descripcion', $revista['descripcion']);
        $stmt->bindParam(':precio', $revista['precio']);
        $stmt->bindParam(':stock', $revista['stock']);
        $stmt->bindParam(':id', $id);

        // Bind imagen solo si se proporciona
        if (!empty($revista['imagen'])) {
            $stmt->bindParam(':imagen', $revista['imagen']);
        }

        return $stmt->execute();
    }

    /**
     * Elimina una revista
     */
    public function deleteRevista($id)
    {
        $query = "DELETE FROM revistas WHERE ID_Revista = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Obtiene editoriales únicas de revistas
     */
    public function getEditoriales()
    {
        $query = "SELECT DISTINCT editorial FROM revistas ORDER BY editorial";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_column($result, 'editorial');
    }

    /**
     * Obtiene revistas relacionadas por editorial
     */
    public function getRevistasRelacionadas($idRevista, $editorial, $limit = 4)
    {
        $query = "SELECT * FROM revistas 
                 WHERE ID_Revista != :id AND editorial = :editorial 
                 ORDER BY RAND() 
                 LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idRevista, PDO::PARAM_INT);
        $stmt->bindParam(':editorial', $editorial);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
