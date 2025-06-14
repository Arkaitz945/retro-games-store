<?php

// Modificado para usar el modelo correcto (singular en lugar de plural)
require_once __DIR__ . "/../../model/UsuarioModel.php";

class UsuariosAdminController
{
    private $usuariosModel;

    public function __construct()
    {
        // Cambiado para usar el modelo correcto (UsuarioModel en lugar de UsuariosModel)
        $this->usuariosModel = new UsuarioModel();
    }

    /**
     * Obtiene todos los usuarios
     */
    public function getAllUsuarios()
    {
        $usuarios = $this->usuariosModel->getAllUsuarios();

        // Depuración
        error_log("UsuariosAdminController: Se obtuvieron " . count($usuarios) . " usuarios");

        // Verificar la estructura de los datos devueltos
        if (!empty($usuarios)) {
            error_log("Estructura del primer usuario: " . print_r($usuarios[0], true));
        }

        return $usuarios;
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function getUsuarioById($id)
    {
        return $this->usuariosModel->getUsuarioById($id);
    }

    /**
     * Actualiza un usuario existente
     */
    public function updateUsuario($id, $usuario)
    {
        // Validar datos
        if (empty($usuario['nombre']) || empty($usuario['email'])) {
            return [
                'success' => false,
                'message' => 'Los campos nombre y email son obligatorios'
            ];
        }

        // Validar formato de email
        if (!filter_var($usuario['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El formato del email no es válido'
            ];
        }

        $result = $this->usuariosModel->updateUsuario($id, $usuario);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar el usuario'
            ];
        }
    }

    /**
     * Elimina un usuario
     */
    public function deleteUsuario($id)
    {
        // Verificar si es el usuario actual o el último administrador
        $usuario = $this->usuariosModel->getUsuarioById($id);

        // No permitir eliminar al usuario actual
        if ($_SESSION['id'] == $id) {
            return [
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ];
        }

        // Verificar si es el último administrador
        if ($usuario['admin'] == 1) {
            $usuarios = $this->usuariosModel->getAllUsuarios();
            $adminCount = 0;

            foreach ($usuarios as $u) {
                if ($u['admin'] == 1) {
                    $adminCount++;
                }
            }

            if ($adminCount <= 1) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el último administrador'
                ];
            }
        }

        $result = $this->usuariosModel->deleteUsuario($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar el usuario'
            ];
        }
    }

    /**
     * Obtiene la dirección de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array|false Datos de la dirección o false si no existe
     */
    public function getDireccionUsuario($idUsuario)
    {
        return $this->usuariosModel->getDireccionUsuario($idUsuario);
    }

    /**
     * Actualiza una dirección existente
     * 
     * @param int $idDireccion ID de la dirección
     * @param array $datos Datos de la dirección
     * @return bool Resultado de la operación
     */
    public function updateDireccionUsuario($idDireccion, $datos)
    {
        return $this->usuariosModel->updateDireccionById($idDireccion, $datos);
    }

    /**
     * Crea una nueva dirección para un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $datos Datos de la dirección
     * @return bool Resultado de la operación
     */
    public function createDireccionUsuario($idUsuario, $datos)
    {
        return $this->usuariosModel->createDireccion($idUsuario, $datos);
    }

    /**
     * Obtiene estadísticas de usuarios
     */
    public function getUsuariosEstadisticas($periodo = 'mes')
    {
        return $this->usuariosModel->getUsuariosEstadisticas($periodo);
    }
}
