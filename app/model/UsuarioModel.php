<?php

require_once "../../config/dbConnection.php";

class UsuarioModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Get user by email
     * 
     * @param string $email User's email
     * @return array|bool User data if found, false otherwise
     */
    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * Create a new user
     * 
     * @param string $email User's email
     * @param string $password Hashed password
     * @param string $nombre User's first name
     * @param string $apellidos User's last name
     * @return bool True if user created successfully
     */
    public function createUser($email, $password, $nombre, $apellidos)
    {
        try {
            $query = "INSERT INTO usuarios (correo, contraseña, nombre, apellidos, esAdmin) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $esAdmin = 0; // Default value for new users
            $result = $stmt->execute([$email, $password, $nombre, $apellidos, $esAdmin]);

            return $result;
        } catch (PDOException $e) {
            // Handle error (in production, log this instead of echoing)
            // echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Additional database methods can be added here
}
