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
     * Obtener los datos del usuario por ID
     * 
     * @param int $idUsuario ID del usuario
     * @return array|bool Datos del usuario o false si no existe
     */
    public function getUserById($idUsuario)
    {
        return $this->usuarioModel->getUserById($idUsuario);
    }

    /**
     * Obtener la dirección del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array|null Datos de la dirección o null si no existe
     */
    public function getDireccionUsuario($idUsuario)
    {
        return $this->direccionModel->getDireccionByUsuario($idUsuario);
    }

    /**
     * Actualizar los datos del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $datos Nuevos datos del usuario
     * @return bool Resultado de la operación
     */
    public function updateUser($idUsuario, $datos)
    {
        return $this->usuarioModel->updateUser($idUsuario, $datos);
    }

    /**
     * Actualizar o crear la dirección del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $datos Datos de la dirección
     * @return bool Resultado de la operación
     */
    public function updateDireccion($idUsuario, $datos)
    {
        // Verificar si ya existe una dirección para este usuario
        $direccion = $this->direccionModel->getDireccionByUsuario($idUsuario);

        if ($direccion) {
            // Actualizar dirección existente
            return $this->direccionModel->updateDireccion($direccion['ID_Direccion'], $datos);
        } else {
            // Crear nueva dirección
            $datos['ID_U'] = $idUsuario;
            return $this->direccionModel->createDireccion($datos);
        }
    }

    /**
     * Cambiar la contraseña del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $oldPassword Contraseña actual
     * @param string $newPassword Nueva contraseña
     * @return array Resultado de la operación
     */
    public function changePassword($idUsuario, $oldPassword, $newPassword)
    {
        // Obtener usuario para verificar contraseña actual
        $usuario = $this->usuarioModel->getUserById($idUsuario);

        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        // Verificar contraseña actual
        if (!password_verify($oldPassword, $usuario['contraseña'])) {
            return [
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ];
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->usuarioModel->updatePassword($idUsuario, $hashedPassword);

        if ($result) {
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
}
