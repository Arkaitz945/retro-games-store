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
     * Register a new user
     * 
     * @param string $email User's email
     * @param string $password User's password
     * @param string $nombre User's first name
     * @param string $apellidos User's last name
     * @return bool True if registration successful, false otherwise
     */
    public function registrarUsuario($email, $password, $nombre, $apellidos)
    {
        // Check if email already exists
        if ($this->usuarioModel->getUserByEmail($email)) {
            return false;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Create new user
        return $this->usuarioModel->createUser($email, $hashedPassword, $nombre, $apellidos);
    }

    // Additional methods can be added here as needed
}
