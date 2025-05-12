<?php

require_once "../model/UsuarioModel.php";

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Validates user credentials and returns user data if successful
     * 
     * @param string $email The user's email
     * @param string $password The password
     * @return mixed Array with user data if login successful, false otherwise
     */
    public function loginUsuario($email, $password)
    {
        // Validate inputs
        if (empty($email) || empty($password)) {
            return false;
        }

        // Get user from database
        $usuario = $this->usuarioModel->getUserByEmail($email);

        // Debugging
        if ($usuario) {
            error_log("Usuario encontrado: " . print_r($usuario, true));
        } else {
            error_log("Usuario no encontrado con email: " . $email);
        }

        // If user exists and password is correct
        if ($usuario && password_verify($password, $usuario['contraseña'])) {
            return [
                [
                    'ID_Usuario' => $usuario['ID_U'],
                    'nombre' => $usuario['nombre'],
                    'EsAdmin' => $usuario['esAdmin']
                ]
            ];
        }

        return false;
    }

    /**
     * Check if an email already exists in the database
     * 
     * @param string $email The email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExiste($email)
    {
        // Get user from database
        $usuario = $this->usuarioModel->getUserByEmail($email);

        // If user exists, return true
        return ($usuario !== false);
    }

    /**
     * Register a new user
     * 
     * @param string $nombre User's name
     * @param string $email User's email
     * @param string $password User's password
     * @return bool True if registration successful, false otherwise
     */
    public function registrarUsuario($nombre, $email, $password)
    {
        try {
            // Check if email already exists
            if ($this->emailExiste($email)) {
                return false;
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create new user (ajustado para coincidir con la estructura de la tabla)
            return $this->usuarioModel->createUser($nombre, $email, $hashedPassword);
        } catch (Exception $e) {
            // Log the error and return false
            error_log("Error en registrarUsuario: " . $e->getMessage());
            return false;
        }
    }

    // Additional methods can be added here as needed
}
