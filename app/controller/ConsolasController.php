<?php

require_once __DIR__ . "/../model/ConsolasModel.php";

class ConsolasController
{
    private $consolasModel;

    public function __construct()
    {
        $this->consolasModel = new ConsolasModel();
    }

    /**
     * Obtener todas las consolas con filtros opcionales
     * 
     * @param array $filtros Filtros a aplicar (fabricante, estado, precio_max)
     * @return array Lista de consolas
     */
    public function getConsolas($filtros = [])
    {
        return $this->consolasModel->getAllConsolas($filtros);
    }

    /**
     * Obtener una consola por su ID
     * 
     * @param int $idConsola ID de la consola
     * @return array|false Datos de la consola o false si no existe
     */
    public function getConsolaById($idConsola)
    {
        return $this->consolasModel->getConsolaById($idConsola);
    }

    /**
     * Obtener fabricantes únicos para filtros
     * 
     * @return array Lista de fabricantes
     */
    public function getFabricantes()
    {
        return $this->consolasModel->getFabricantes();
    }

    /**
     * Obtener estados únicos para filtros
     * 
     * @return array Lista de estados
     */
    public function getEstados()
    {
        return $this->consolasModel->getEstados();
    }

    /**
     * Obtener consolas relacionadas por fabricante
     * 
     * @param int $idConsola ID de la consola a excluir
     * @param string $fabricante Fabricante para relacionar
     * @param int $limit Número máximo de resultados
     * @return array Consolas relacionadas
     */
    public function getConsolasRelacionadas($idConsola, $fabricante, $limit = 4)
    {
        return $this->consolasModel->getConsolasRelacionadas($idConsola, $fabricante, $limit);
    }
}
