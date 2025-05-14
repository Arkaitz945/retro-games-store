<?php

// Fix the path to include the model correctly
require_once __DIR__ . "/../../model/ConsolasModel.php";

class ConsolasAdminController
{
    private $consolasModel;

    public function __construct()
    {
        $this->consolasModel = new ConsolasModel();
    }

    /**
     * Obtiene todas las consolas
     */
    public function getAllConsolas()
    {
        return $this->consolasModel->getAllConsolas();
    }

    /**
     * Obtiene una consola por su ID
     */
    public function getConsolaById($id)
    {
        return $this->consolasModel->getConsolaById($id);
    }

    /**
     * Crea una nueva consola
     */
    public function createConsola($consola)
    {
        // Validar datos
        if (empty($consola['nombre']) || empty($consola['fabricante']) || empty($consola['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos nombre, fabricante y precio son obligatorios'
            ];
        }

        if (!is_numeric($consola['precio']) || $consola['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($consola['stock']) || $consola['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->consolasModel->createConsola($consola);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Consola creada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear la consola'
            ];
        }
    }

    /**
     * Actualiza una consola existente
     */
    public function updateConsola($id, $consola)
    {
        // Validar datos
        if (empty($consola['nombre']) || empty($consola['fabricante']) || empty($consola['precio'])) {
            return [
                'success' => false,
                'message' => 'Los campos nombre, fabricante y precio son obligatorios'
            ];
        }

        if (!is_numeric($consola['precio']) || $consola['precio'] <= 0) {
            return [
                'success' => false,
                'message' => 'El precio debe ser un número positivo'
            ];
        }

        if (!is_numeric($consola['stock']) || $consola['stock'] < 0) {
            return [
                'success' => false,
                'message' => 'El stock debe ser un número no negativo'
            ];
        }

        $result = $this->consolasModel->updateConsola($id, $consola);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Consola actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la consola'
            ];
        }
    }

    /**
     * Elimina una consola
     */
    public function deleteConsola($id)
    {
        $result = $this->consolasModel->deleteConsola($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Consola eliminada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar la consola'
            ];
        }
    }

    /**
     * Obtiene los fabricantes de consolas
     */
    public function getFabricantes()
    {
        return $this->consolasModel->getFabricantes();
    }

    /**
     * Obtiene los estados de consolas
     */
    public function getEstados()
    {
        return $this->consolasModel->getEstados();
    }
}
