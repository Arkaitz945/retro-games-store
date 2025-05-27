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

    /**
     * Get user by ID
     * 
     * @param int $idUsuario User ID
     * @return array|false User data or false if not found
     */
    public function getUserById($idUsuario)
    {
        return $this->usuarioModel->getUserById($idUsuario);
    }

    /**
     * Update user information
     * 
     * @param int $idUsuario User ID
     * @param array $datos User data to update (nombre, apellidos, correo)
     * @return bool True if update successful, false otherwise
     */
    public function updateUser($idUsuario, $datos)
    {
        try {
            // Check if email exists and belongs to another user
            if (isset($datos['correo']) && $datos['correo'] != '') {
                $usuarioExistente = $this->usuarioModel->getUserByEmail($datos['correo']);
                if ($usuarioExistente && $usuarioExistente['ID_U'] != $idUsuario) {
                    error_log("UsuarioController: Email ya está en uso por otro usuario");
                    return false;
                }
            }

            // Update user information
            return $this->usuarioModel->updateUser($idUsuario, $datos);
        } catch (Exception $e) {
            error_log("Error en updateUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change user password
     * 
     * @param int $idUsuario User ID
     * @param string $oldPassword Current password
     * @param string $newPassword New password
     * @return array Result with success status and message
     */
    public function changePassword($idUsuario, $oldPassword, $newPassword)
    {
        try {
            // Get user data
            $usuario = $this->usuarioModel->getUserById($idUsuario);

            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }

            // Verify current password
            $campoPassword = isset($usuario['contraseña']) ? 'contraseña' : 'password';
            $storedPassword = $usuario[$campoPassword];

            if (!password_verify($oldPassword, $storedPassword) && $oldPassword !== $storedPassword) {
                return [
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ];
            }

            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in database
            $updated = $this->usuarioModel->updatePassword($idUsuario, $hashedPassword);

            if ($updated) {
                return [
                    'success' => true,
                    'message' => 'Contraseña actualizada correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña'
                ];
            }
        } catch (Exception $e) {
            error_log("Error en changePassword: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno al cambiar la contraseña'
            ];
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
        return $this->usuarioModel->getDireccionUsuario($idUsuario);
    }

    /**
     * Update user's address
     * 
     * @param int $idUsuario User ID
     * @param array $datosDireccion Address data to update
     * @return bool True if update successful, false otherwise
     */
    public function updateDireccion($idUsuario, $datosDireccion)
    {
        try {
            // Check if user has an address
            $direccionExistente = $this->usuarioModel->getDireccionUsuario($idUsuario);

            if ($direccionExistente) {
                // Update existing address
                return $this->usuarioModel->updateDireccion($idUsuario, $datosDireccion);
            } else {
                // Create new address
                return $this->usuarioModel->createDireccion($idUsuario, $datosDireccion);
            }
        } catch (Exception $e) {
            error_log("Error en updateDireccion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a new address for user
     * 
     * @param int $idUsuario User ID
     * @param array $datosDireccion Address data
     * @return bool True if successful, false otherwise
     */
    public function addDireccion($idUsuario, $datosDireccion)
    {
        try {
            return $this->usuarioModel->createDireccion($idUsuario, $datosDireccion);
        } catch (Exception $e) {
            error_log("Error en addDireccion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user address
     * 
     * @param int $idDireccion Address ID
     * @param int $idUsuario User ID (for verification)
     * @return bool True if successful, false otherwise
     */
    public function deleteDireccion($idDireccion, $idUsuario)
    {
        try {
            // Verify the address belongs to the user before deleting
            $direccion = $this->usuarioModel->getDireccionById($idDireccion);

            if (!$direccion || $direccion['idUsuario'] != $idUsuario) {
                error_log("UsuarioController: Intento de eliminar dirección ajena");
                return false;
            }

            return $this->usuarioModel->deleteDireccion($idDireccion);
        } catch (Exception $e) {
            error_log("Error en deleteDireccion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a specific address by its ID
     * 
     * @param int $idDireccion Address ID
     * @param array $datosDireccion Address data
     * @return bool True if successful, false otherwise
     */
    public function updateDireccionById($idDireccion, $datosDireccion)
    {
        try {
            // Verify the address exists
            $direccion = $this->usuarioModel->getDireccionById($idDireccion);

            if (!$direccion) {
                error_log("UsuarioController: Dirección no encontrada: $idDireccion");
                return false;
            }

            return $this->usuarioModel->updateDireccionById($idDireccion, $datosDireccion);
        } catch (Exception $e) {
            error_log("Error en updateDireccionById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all addresses for a user
     * 
     * @param int $idUsuario User ID
     * @return array Array of addresses
     */
    public function getDireccionesUsuario($idUsuario)
    {
        return $this->usuarioModel->getDireccionesUsuario($idUsuario);
    }
}
