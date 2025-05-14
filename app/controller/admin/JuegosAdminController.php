<?php

// Fix the path to include the model correctly
require_once __DIR__ . "/../../model/JuegosModel.php";

class JuegosAdminController
{
    private $juegosModel;

    public function __construct()
    {
        $this->juegosModel = new JuegosModel();
    }

    /**
     * Obtiene todos los juegos
     */
    public function getAllJuegos()
    {
        return $this->juegosModel->getAllJuegos();
    }

    /**
     * Obtiene un juego por su ID
     */
    public function getJuegoById($id)
    {
        return $this->juegosModel->getJuegoById($id);
    }

    /**
     * Crea un nuevo juego
     */
    public function createJuego($juego)
    {
        // Validar datos
        if (empty($juego['nombre']) || empty($juego['plataforma']) || empty($juego['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos nombre, plataforma y precio son obligatorios'
            ];
        }

        if (!is_numeric($juego['precio']) || $juego['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($juego['stock']) || $juego['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->juegosModel->createJuego($juego);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Juego creado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear el juego'
            ];
        }
    }

    /**
     * Actualiza un juego existente
     */
    public function updateJuego($id, $juego)
    {
        // Validar datos
        if (empty($juego['nombre']) || empty($juego['plataforma']) || empty($juego['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos nombre, plataforma y precio son obligatorios'
            ];
        }

        if (!is_numeric($juego['precio']) || $juego['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($juego['stock']) || $juego['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->juegosModel->updateJuego($id, $juego);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Juego actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar el juego'
            ];
        }
    }

    /**
     * Elimina un juego
     */
    public function deleteJuego($id)
    {
        $result = $this->juegosModel->deleteJuego($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Juego eliminado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar el juego'
            ];
        }
    }

    /**
     * Obtiene los datos para los selectores de filtros
     */
    public function getPlataformas()
    {
        return $this->juegosModel->getPlataformas();
    }

    public function getGeneros()
    {
        return $this->juegosModel->getGeneros();
    }

    public function getEstados()
    {
        return $this->juegosModel->getEstados();
    }
}
