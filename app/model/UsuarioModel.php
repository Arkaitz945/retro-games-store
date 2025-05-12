<?php

require_once "../../config/dbConnection.php";

class UsuarioModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();

        // Verificar si la conexión fue exitosa
        if (!$this->conn) {
            die("Error: No se pudo conectar a la base de datos");
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
        try {
            // Cambiado de "email" a "correo" para que coincida con la estructura de la tabla
            $query = "SELECT * FROM usuarios WHERE correo = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por email: " . $e->getMessage());
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
}
