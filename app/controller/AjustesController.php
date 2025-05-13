<?php

require_once "../model/UsuarioModel.php";
require_once "../model/DireccionModel.php";

class AjustesController
{
    private $usuarioModel;
    private $direccionModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->direccionModel = new DireccionModel();
    }

    /**
     * Obtener datos del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return mixed Array con datos del usuario o false
     */
    public function getUsuario($idUsuario)
    {
        return $this->usuarioModel->getUserById($idUsuario);
    }

    /**
     * Actualizar perfil del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $nombre Nombre del usuario
     * @param string $apellidos Apellidos del usuario
     * @return array Resultado de la operación
     */
    public function actualizarPerfil($idUsuario, $nombre, $apellidos)
    {
        $resultado = $this->usuarioModel->updateUserProfile($idUsuario, $nombre, $apellidos);

        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ];
        }
    }

    /**
     * Actualizar correo del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $email Nuevo correo
     * @return array Resultado de la operación
     */
    public function actualizarEmail($idUsuario, $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El correo electrónico no es válido'
            ];
        }

        $resultado = $this->usuarioModel->updateUserEmail($idUsuario, $email);

        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Correo electrónico actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'El correo electrónico ya está en uso por otro usuario'
            ];
        }
    }

    /**
     * Actualizar contraseña del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $currentPassword Contraseña actual
     * @param string $newPassword Nueva contraseña
     * @param string $confirmPassword Confirmación de nueva contraseña
     * @return array Resultado de la operación
     */
    public function actualizarContraseña($idUsuario, $currentPassword, $newPassword, $confirmPassword)
    {
        // Verificar contraseña actual
        if (!$this->usuarioModel->verifyUserPassword($idUsuario, $currentPassword)) {
            return [
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ];
        }

        // Verificar que las nuevas contraseñas coincidan
        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Las nuevas contraseñas no coinciden'
            ];
        }

        // Verificar longitud mínima
        if (strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres'
            ];
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $resultado = $this->usuarioModel->updateUserPassword($idUsuario, $hashedPassword);

        if ($resultado) {
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
    }

    /**
     * Obtener direcciones del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Direcciones del usuario
     */
    public function getDirecciones($idUsuario)
    {
        return $this->direccionModel->getDireccionesByUsuario($idUsuario);
    }

    /**
     * Añadir dirección
     * 
     * @param array $direccion Datos de la dirección
     * @return array Resultado de la operación
     */
    public function añadirDireccion($direccion)
    {
        $resultado = $this->direccionModel->createDireccion($direccion);

        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Dirección añadida correctamente',
                'id' => $resultado
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al añadir la dirección'
            ];
        }
    }

    /**
     * Actualizar dirección
     * 
     * @param array $direccion Datos de la dirección
     * @return array Resultado de la operación
     */
    public function actualizarDireccion($direccion)
    {
        $resultado = $this->direccionModel->updateDireccion($direccion);

        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Dirección actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la dirección'
            ];
        }
    }

    /**
     * Eliminar dirección
     * 
     * @param int $idDireccion ID de la dirección
     * @param int $idUsuario ID del usuario
     * @return array Resultado de la operación
     */
    public function eliminarDireccion($idDireccion, $idUsuario)
    {
        $resultado = $this->direccionModel->deleteDireccion($idDireccion, $idUsuario);

        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Dirección eliminada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar la dirección'
            ];
        }
    }
}
