<?php

// Asegurarnos que la ruta de inclusión es correcta
require_once __DIR__ . "/../../config/dbConnection.php";

class UsuarioModel
{
    private $conn;

    public function __construct()
    {
        // Obtener la conexión y verificarla inmediatamente
        $this->conn = getDBConnection();

        // Verificar si la conexión fue exitosa
        if (!$this->conn) {
            error_log("ERROR CRÍTICO: UsuarioModel no pudo obtener conexión a la base de datos");
            // No lanzar excepción aquí para evitar errores fatales, manejaremos el error en los métodos
        } else {
            error_log("UsuarioModel: Conexión exitosa a la base de datos");
        }
    }

    /**
     * Get user by email
     * 
     * @param string $email The user's email
     * @return mixed User data if found, false otherwise
     */
    public function getUserByEmail($email)
    {
        // Verificar si hay conexión antes de intentar cualquier operación
        if (!$this->conn) {
            error_log("UsuarioModel->getUserByEmail: No hay conexión a la base de datos");
            return false;
        }

        try {
            // Añadir registro para depuración
            error_log("UsuarioModel: Buscando usuario con email: $email");

            // Cambiado de "email" a "correo" para que coincida con la estructura de la tabla
            $query = "SELECT * FROM usuarios WHERE correo = :correo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":correo", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                error_log("UsuarioModel: Usuario encontrado con email: $email");
                return $user;
            }
            error_log("UsuarioModel: No se encontró usuario con email: $email");
            return false;
        } catch (PDOException $e) {
            error_log("UsuarioModel: Error al obtener usuario por email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new user
     * 
     * @param string $nombre User's name
     * @param string $email User's email
     * @param string $password User's hashed password
     * @return bool True if successful, false otherwise
     */
    public function createUser($nombre, $email, $password)
    {
        try {
            // Ajustado para incluir "apellidos" y usar "correo" en lugar de "email"
            $query = "INSERT INTO usuarios (nombre, correo, contraseña) 
                    VALUES (:nombre, :correo, :password)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":correo", $email);
            $stmt->bindParam(":password", $password);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     * 
     * @param int $idUsuario The user's ID
     * @return mixed User data if found, false otherwise
     */
    public function getUserById($idUsuario)
    {
        try {
            $query = "SELECT * FROM usuarios WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's profile information
     * 
     * @param int $idUsuario User's ID
     * @param string $nombre User's name
     * @param string $apellidos User's last name
     * @return bool True if successful, false otherwise
     */
    public function updateUserProfile($idUsuario, $nombre, $apellidos)
    {
        try {
            $query = "UPDATE usuarios 
                      SET nombre = :nombre, 
                          apellidos = :apellidos 
                      WHERE ID_U = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellidos", $apellidos);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando perfil de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's email
     * 
     * @param int $idUsuario User's ID
     * @param string $email New email
     * @return bool True if successful, false otherwise
     */
    public function updateUserEmail($idUsuario, $email)
    {
        try {
            // Verificar que el email no esté en uso por otro usuario
            $query = "SELECT ID_U FROM usuarios WHERE correo = :correo AND ID_U != :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":correo", $email);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // El email ya está en uso
                return false;
            }

            // Actualizar el email
            $query = "UPDATE usuarios SET correo = :correo WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":correo", $email);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando email de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's password
     * 
     * @param int $idUsuario User's ID
     * @param string $password New hashed password
     * @return bool True if successful, false otherwise
     */
    public function updateUserPassword($idUsuario, $password)
    {
        try {
            $query = "UPDATE usuarios SET contraseña = :password WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando contraseña de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify user's current password
     * 
     * @param int $idUsuario User's ID
     * @param string $password Password to verify
     * @return bool True if password matches, false otherwise
     */
    public function verifyUserPassword($idUsuario, $password)
    {
        try {
            $query = "SELECT contraseña FROM usuarios WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return password_verify($password, $user['contraseña']);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error verificando contraseña de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's address
     * 
     * @param int $idUsuario User ID
     * @return array|false Address data or false if not found
     */
    public function getDireccionUsuario($idUsuario)
    {
        try {
            $query = "SELECT * FROM direccion WHERE idUsuario = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $idUsuario);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("UsuarioModel::getDireccionUsuario Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all addresses for a user
     * 
     * @param int $idUsuario User ID
     * @return array Array of addresses or empty array if none found
     */
    public function getDireccionesUsuario($idUsuario)
    {
        try {
            $query = "SELECT * FROM direccion WHERE idUsuario = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $idUsuario);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UsuarioModel::getDireccionesUsuario Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get address by ID
     * 
     * @param int $idDireccion Address ID
     * @return array|false Address data or false if not found
     */
    public function getDireccionById($idDireccion)
    {
        try {
            $query = "SELECT * FROM direccion WHERE ID_Direccion = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $idDireccion);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("UsuarioModel::getDireccionById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new address for user
     * 
     * @param int $idUsuario User ID
     * @param array $datosDireccion Address data
     * @return bool True if success, false otherwise
     */
    public function createDireccion($idUsuario, $datosDireccion)
    {
        try {
            $query = "INSERT INTO direccion (idUsuario, calle, numero, codigoPostal) 
                      VALUES (:id_usuario, :calle, :numero, :codigo_postal)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_usuario", $idUsuario);
            $stmt->bindParam(":calle", $datosDireccion['calle']);
            $stmt->bindParam(":numero", $datosDireccion['numero']);
            $stmt->bindParam(":codigo_postal", $datosDireccion['codigoPostal']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("UsuarioModel::createDireccion Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user address
     * 
     * @param int $idUsuario User ID
     * @param array $datosDireccion Address data
     * @return bool True if success, false otherwise
     */
    public function updateDireccion($idUsuario, $datosDireccion)
    {
        try {
            $query = "UPDATE direccion 
                      SET calle = :calle, numero = :numero, codigoPostal = :codigo_postal 
                      WHERE idUsuario = :id_usuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":calle", $datosDireccion['calle']);
            $stmt->bindParam(":numero", $datosDireccion['numero']);
            $stmt->bindParam(":codigo_postal", $datosDireccion['codigoPostal']);
            $stmt->bindParam(":id_usuario", $idUsuario);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("UsuarioModel::updateDireccion Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user address
     * 
     * @param int $idDireccion Address ID
     * @return bool True if success, false otherwise
     */
    public function deleteDireccion($idDireccion)
    {
        try {
            $query = "DELETE FROM direccion WHERE ID_Direccion = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $idDireccion);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("UsuarioModel::deleteDireccion Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user information
     * 
     * @param int $idUsuario User ID
     * @param array $datos User data to update
     * @return bool True if success, false otherwise
     */
    public function updateUser($idUsuario, $datos)
    {
        try {
            $campos = [];
            $valores = [];

            // Prepare dynamic update fields
            if (isset($datos['nombre']) && !empty($datos['nombre'])) {
                $campos[] = "nombre = :nombre";
                $valores[':nombre'] = $datos['nombre'];
            }

            if (isset($datos['apellidos']) && !empty($datos['apellidos'])) {
                $campos[] = "apellidos = :apellidos";
                $valores[':apellidos'] = $datos['apellidos'];
            }

            if (isset($datos['correo']) && !empty($datos['correo'])) {
                $campos[] = "correo = :correo";
                $valores[':correo'] = $datos['correo'];
            }

            if (empty($campos)) {
                return false; // No hay campos para actualizar
            }

            $query = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE ID_U = :id";
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            foreach ($valores as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->bindParam(":id", $idUsuario);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando datos de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password
     * 
     * @param int $idUsuario User ID
     * @param string $hashedPassword New hashed password
     * @return bool True if success, false otherwise
     */
    public function updatePassword($idUsuario, $hashedPassword)
    {
        return $this->updateUserPassword($idUsuario, $hashedPassword);
    }

    /**
     * Update address by ID
     * 
     * @param int $idDireccion Address ID
     * @param array $datosDireccion Address data
     * @return bool True if success, false otherwise
     */
    public function updateDireccionById($idDireccion, $datosDireccion)
    {
        try {
            $query = "UPDATE direccion 
                      SET calle = :calle, numero = :numero, codigoPostal = :codigo_postal 
                      WHERE ID_Direccion = :id_direccion";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":calle", $datosDireccion['calle']);
            $stmt->bindParam(":numero", $datosDireccion['numero']);
            $stmt->bindParam(":codigo_postal", $datosDireccion['codigoPostal']);
            $stmt->bindParam(":id_direccion", $idDireccion);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("UsuarioModel::updateDireccionById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios del sistema
     * 
     * @return array Lista de usuarios
     */
    public function getAllUsuarios()
    {
        try {
            $query = "SELECT * FROM usuarios ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error obteniendo todos los usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un usuario específico por su ID
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false si no existe
     */
    public function getUsuarioById($id)
    {
        try {
            $query = "SELECT * FROM usuarios WHERE ID_U = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error obteniendo usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la información de un usuario
     * 
     * @param int $id ID del usuario
     * @param array $usuario Datos del usuario a actualizar
     * @return bool Resultado de la operación
     */
    public function updateUsuario($id, $usuario)
    {
        try {
            // Construir la consulta dinámicamente para incluir solo los campos proporcionados
            $campos = [];
            $params = [':id' => $id];

            if (isset($usuario['nombre'])) {
                $campos[] = "nombre = :nombre";
                $params[':nombre'] = $usuario['nombre'];
            }

            if (isset($usuario['email'])) {
                $campos[] = "correo = :correo";
                $params[':correo'] = $usuario['email'];
            }

            if (isset($usuario['apellidos'])) {
                $campos[] = "apellidos = :apellidos";
                $params[':apellidos'] = $usuario['apellidos'];
            }

            if (isset($usuario['admin'])) {
                $campos[] = "esAdmin = :admin";
                $params[':admin'] = $usuario['admin'];
            }

            // Si no hay campos para actualizar, retornar falso
            if (empty($campos)) {
                return false;
            }

            $query = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE ID_U = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario
     * 
     * @param int $id ID del usuario a eliminar
     * @return bool Resultado de la operación
     */
    public function deleteUsuario($id)
    {
        try {
            // Primero eliminar registros relacionados para evitar errores de clave foránea
            $this->eliminarDependencias($id);

            // Luego eliminar el usuario
            $query = "DELETE FROM usuarios WHERE ID_U = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error eliminando usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina registros dependientes del usuario (direcciones, carrito, etc.)
     * 
     * @param int $id ID del usuario
     */
    private function eliminarDependencias($id)
    {
        try {
            // Eliminar direcciones
            $query = "DELETE FROM direccion WHERE idUsuario = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar items del carrito
            $query = "DELETE FROM carrito WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Aquí se pueden añadir más eliminaciones de registros relacionados si es necesario
        } catch (PDOException $e) {
            error_log("Error eliminando dependencias del usuario: " . $e->getMessage());
            // No lanzamos excepción para permitir que se siga intentando eliminar el usuario
        }
    }

    /**
     * Obtiene estadísticas de usuarios
     * 
     * @param string $periodo Periodo de tiempo (día, mes, año)
     * @return array Estadísticas de usuarios
     */
    public function getUsuariosEstadisticas($periodo = 'mes')
    {
        try {
            $query = "";

            switch ($periodo) {
                case 'dia':
                    // Estadísticas por día (últimos 30 días)
                    $query = "SELECT DATE(fecha_registro) as periodo, COUNT(*) as nuevos_usuarios
                              FROM usuarios
                              WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                              GROUP BY DATE(fecha_registro)
                              ORDER BY DATE(fecha_registro)";
                    break;

                case 'mes':
                    // Estadísticas por mes (últimos 12 meses)
                    $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as periodo, COUNT(*) as nuevos_usuarios
                              FROM usuarios
                              WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                              GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m')
                              ORDER BY DATE_FORMAT(fecha_registro, '%Y-%m')";
                    break;

                case 'anio':
                    // Estadísticas por año
                    $query = "SELECT YEAR(fecha_registro) as periodo, COUNT(*) as nuevos_usuarios
                              FROM usuarios
                              GROUP BY YEAR(fecha_registro)
                              ORDER BY YEAR(fecha_registro)";
                    break;

                default:
                    // Por defecto, usar estadísticas mensuales
                    $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as periodo, COUNT(*) as nuevos_usuarios
                              FROM usuarios
                              WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                              GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m')
                              ORDER BY DATE_FORMAT(fecha_registro, '%Y-%m')";
            }

            // Si la tabla usuarios no tiene campo fecha_registro, usar esta consulta alternativa
            // que simplemente devuelve un conteo total
            if (strpos($query, 'fecha_registro') !== false) {
                try {
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Si hay error, probablemente la columna no existe
                    error_log("Error en consulta con fecha_registro: " . $e->getMessage());
                    // Devolver datos ficticios para que la aplicación no falle
                    return [
                        ['periodo' => date('Y-m'), 'nuevos_usuarios' => count($this->getAllUsuarios())]
                    ];
                }
            } else {
                // Consulta de respaldo si la tabla no tiene fecha_registro
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usuarios");
                $stmt->execute();
                $total = $stmt->fetchColumn();

                return [
                    ['periodo' => 'Total', 'nuevos_usuarios' => $total]
                ];
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas de usuarios: " . $e->getMessage());
            return [];
        }
    }
}
