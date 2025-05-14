<?php

// Fix the path to use __DIR__ for a more reliable absolute path
require_once __DIR__ . "/../../config/dbConnection.php";

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

    /**
     * Obtener juegos relacionados excluyendo uno específico
     * 
     * @param int $idJuego ID del juego a excluir
     * @param array $filtros Filtros adicionales (opcional)
     * @param int $limit Límite de resultados (opcional)
     * @return array Array con los juegos relacionados
     */
    public function getRelatedJuegos($idJuego, $filtros = [], $limit = 4)
    {
        try {
            $query = "SELECT * FROM juegos WHERE ID_J != :idJuego";
            $params = [':idJuego' => $idJuego];

            if (!empty($filtros)) {
                foreach ($filtros as $key => $value) {
                    $query .= " AND $key = :$key";
                    $params[":$key"] = $value;
                }
            }

            $query .= " ORDER BY RAND() LIMIT :limit";

            $stmt = $this->conn->prepare($query);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            // LIMIT requiere PDO::PARAM_INT
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo juegos relacionados: " . $e->getMessage());
            return [];
        }
    }
    public function createJuego($juego)
    {
        try {
            // Asumiendo que hay una conexión a la base de datos como propiedad de la clase
            $stmt = $this->conn->prepare("INSERT INTO juegos (nombre, plataforma, precio, stock, descripcion, genero, estado, imagen) 
                                        VALUES (:nombre, :plataforma, :precio, :stock, :descripcion, :genero, :estado, :imagen)");

            return $stmt->execute([
                ':nombre' => $juego['nombre'],
                ':plataforma' => $juego['plataforma'],
                ':precio' => $juego['precio'],
                ':stock' => $juego['stock'],
                ':descripcion' => $juego['descripcion'] ?? '',
                ':genero' => $juego['genero'] ?? null,
                ':estado' => $juego['estado'] ?? null,
                ':imagen' => $juego['imagen'] ?? null
            ]);
        } catch (PDOException $e) {
            // Manejar error
            return false;
        }
    }

    /**
     * Actualiza un juego existente
     * 
     * @param int $id ID del juego a actualizar
     * @param array $juego Datos del juego
     * @return bool Resultado de la operación
     */
    public function updateJuego($id, $juego)
    {
        try {
            $query = "UPDATE juegos SET 
                nombre = :nombre, 
                plataforma = :plataforma, 
                genero = :genero, 
                descripcion = :descripcion, 
                estado = :estado, 
                precio = :precio, 
                stock = :stock";

            // Campos opcionales con valores especiales si se proporcionan
            if (isset($juego['desarrollador'])) {
                $query .= ", desarrollador = :desarrollador";
            }
            if (isset($juego['publisher'])) {
                $query .= ", publisher = :publisher";
            }
            if (isset($juego['año_lanzamiento'])) {
                $query .= ", año_lanzamiento = :anio_lanzamiento";
            }
            if (isset($juego['incluye_caja'])) {
                $query .= ", incluye_caja = :incluye_caja";
            }
            if (isset($juego['incluye_manual'])) {
                $query .= ", incluye_manual = :incluye_manual";
            }
            if (isset($juego['region'])) {
                $query .= ", region = :region";
            }
            if (!empty($juego['imagen'])) {
                $query .= ", imagen = :imagen";
            }

            $query .= " WHERE ID_J = :id";

            $stmt = $this->conn->prepare($query);

            // Parámetros básicos
            $params = [
                ':nombre' => $juego['nombre'],
                ':plataforma' => $juego['plataforma'],
                ':genero' => $juego['genero'] ?? null,
                ':descripcion' => $juego['descripcion'] ?? '',
                ':estado' => $juego['estado'] ?? 'Usado',
                ':precio' => $juego['precio'],
                ':stock' => $juego['stock'],
                ':id' => $id
            ];

            // Parámetros opcionales
            if (isset($juego['desarrollador'])) {
                $params[':desarrollador'] = $juego['desarrollador'];
            }
            if (isset($juego['publisher'])) {
                $params[':publisher'] = $juego['publisher'];
            }
            if (isset($juego['año_lanzamiento'])) {
                $params[':anio_lanzamiento'] = $juego['año_lanzamiento'];
            }
            if (isset($juego['incluye_caja'])) {
                $params[':incluye_caja'] = $juego['incluye_caja'];
            }
            if (isset($juego['incluye_manual'])) {
                $params[':incluye_manual'] = $juego['incluye_manual'];
            }
            if (isset($juego['region'])) {
                $params[':region'] = $juego['region'];
            }
            if (!empty($juego['imagen'])) {
                $params[':imagen'] = $juego['imagen'];
            }

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error actualizando juego: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un juego
     * 
     * @param int $id ID del juego a eliminar
     * @return bool Resultado de la operación
     */
    public function deleteJuego($id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM juegos WHERE ID_J = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error eliminando juego: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene juegos relacionados por plataforma y/o género
     * 
     * @param int $idJuego ID del juego a excluir
     * @param string $plataforma Plataforma del juego
     * @param string $genero Género del juego
     * @param int $limit Límite de resultados
     * @return array Juegos relacionados
     */
    public function getJuegosRelacionados($idJuego, $plataforma, $genero, $limit = 4)
    {
        try {
            $query = "SELECT * FROM juegos WHERE ID_J != :idJuego AND (plataforma = :plataforma OR genero = :genero) ORDER BY RAND() LIMIT :limit";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idJuego', $idJuego, PDO::PARAM_INT);
            $stmt->bindParam(':plataforma', $plataforma);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo juegos relacionados: " . $e->getMessage());
            return [];
        }
    }
}
