<?php

require_once "../../config/dbConnection.php";

class JuegosModel
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
     * Obtener todos los juegos
     * 
     * @param array $filtros Filtros a aplicar
     * @return array Array con los juegos
     */
    public function getAllJuegos($filtros = [])
    {
        try {
            $query = "SELECT * FROM juegos";
            $condiciones = [];
            $params = [];

            // Aplicar filtros si existen
            if (!empty($filtros)) {
                // Filtro por plataforma
                if (isset($filtros['plataforma']) && !empty($filtros['plataforma'])) {
                    $condiciones[] = "plataforma = :plataforma";
                    $params[':plataforma'] = $filtros['plataforma'];
                }

                // Filtro por género
                if (isset($filtros['genero']) && !empty($filtros['genero'])) {
                    $condiciones[] = "genero = :genero";
                    $params[':genero'] = $filtros['genero'];
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

            // Ordenar por
            $query .= " ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($query);

            // Bind params
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo juegos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un juego por su ID
     * 
     * @param int $idJuego ID del juego
     * @return mixed Array con la información del juego o false si no existe
     */
    public function getJuegoById($idJuego)
    {
        try {
            $query = "SELECT * FROM juegos WHERE ID_J = :idJuego";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idJuego", $idJuego, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error obteniendo juego por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener plataformas únicas
     * 
     * @return array Array con las plataformas
     */
    public function getPlataformas()
    {
        try {
            $query = "SELECT DISTINCT plataforma FROM juegos ORDER BY plataforma";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo plataformas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener géneros únicos
     * 
     * @return array Array con los géneros
     */
    public function getGeneros()
    {
        try {
            $query = "SELECT DISTINCT genero FROM juegos ORDER BY genero";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo géneros: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estados únicos
     * 
     * @return array Array con los estados
     */
    public function getEstados()
    {
        try {
            $query = "SELECT DISTINCT estado FROM juegos ORDER BY estado";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo estados: " . $e->getMessage());
            return [];
        }
    }
}
