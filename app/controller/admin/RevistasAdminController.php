<?php

// Fix the path to include the model correctly
require_once __DIR__ . "/../../model/RevistasModel.php";

class RevistasAdminController
{
    private $revistasModel;

    public function __construct()
    {
        $this->revistasModel = new RevistasModel();
    }

    /**
     * Obtiene todas las revistas
     */
    public function getAllRevistas()
    {
        return $this->revistasModel->getAllRevistas();
    }

    /**
     * Obtiene una revista por su ID
     */
    public function getRevistaById($id)
    {
        return $this->revistasModel->getRevistaById($id);
    }

    /**
     * Crea una nueva revista
     */
    public function createRevista($revista)
    {
        // Validar datos
        if (empty($revista['titulo']) || empty($revista['editorial']) || empty($revista['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos título, editorial y precio son obligatorios'
            ];
        }

        if (!is_numeric($revista['precio']) || $revista['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($revista['stock']) || $revista['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->revistasModel->createRevista($revista);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Revista creada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear la revista'
            ];
        }
    }

    /**
     * Actualiza una revista existente
     */
    public function updateRevista($id, $revista)
    {
        // Validar datos
        if (empty($revista['titulo']) || empty($revista['editorial']) || empty($revista['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos título, editorial y precio son obligatorios'
            ];
        }

        if (!is_numeric($revista['precio']) || $revista['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($revista['stock']) || $revista['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->revistasModel->updateRevista($id, $revista);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Revista actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la revista'
            ];
        }
    }

    /**
     * Elimina una revista
     */
    public function deleteRevista($id)
    {
        $result = $this->revistasModel->deleteRevista($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Revista eliminada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar la revista'
            ];
        }
    }

    /**
     * Obtiene las editoriales de revistas
     */
    public function getEditoriales()
    {
        return $this->revistasModel->getEditoriales();
    }
}
