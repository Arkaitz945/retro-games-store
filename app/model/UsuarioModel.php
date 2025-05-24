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
            $query = "SELECT * FROM usuarios WHERE correo = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
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
            $query = "INSERT INTO usuarios (nombre, apellidos, correo, contraseña, esAdmin) 
                    VALUES (:nombre, '', :email, :password, 0)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":email", $email);
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
            $query = "SELECT ID_U FROM usuarios WHERE correo = :email AND ID_U != :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // El email ya está en uso
                return false;
            }

            // Actualizar el email
            $query = "UPDATE usuarios SET correo = :email WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
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
}
