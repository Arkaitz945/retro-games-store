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
     * Inicia sesión del usuario
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return mixed Datos del usuario si el login es correcto, false en caso contrario
     */
    public function loginUsuario($email, $password)
    {
        // Depuración para identificar problemas
        error_log("UsuarioController: Intento de login para email: $email");

        // Obtener usuario por email
        $usuario = $this->usuarioModel->getUserByEmail($email);

        if (!$usuario) {
            error_log("UsuarioController: Usuario no encontrado para email: $email");
            return false;
        }

        // Verificar la contraseña
        // Asumimos que el campo es 'contraseña', pero adaptamos según sea necesario
        $campoPassword = isset($usuario['contraseña']) ? 'contraseña' : (isset($usuario['password']) ? 'password' : 'clave');

        if (!isset($usuario[$campoPassword])) {
            error_log("UsuarioController: Campo de contraseña no encontrado en datos de usuario. Campos disponibles: " . implode(", ", array_keys($usuario)));
            return false;
        }

        $storedPassword = $usuario[$campoPassword];

        // Depuración
        error_log("UsuarioController: Verificando password utilizando campo: $campoPassword");

        // Verificar si la contraseña está hasheada
        if (password_verify($password, $storedPassword)) {
            error_log("UsuarioController: Password correcto para: $email");
            return [$usuario]; // Devolver array con el usuario para mantener compatibilidad
        } else {
            // Verificar si la contraseña está almacenada sin hash (caso de prueba)
            if ($password === $storedPassword) {
                error_log("UsuarioController: Password sin hash correcto para: $email (INSEGURO)");
                return [$usuario];
            }

            error_log("UsuarioController: Password incorrecto para: $email");
            return false;
        }
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
