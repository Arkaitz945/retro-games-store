<?php

require_once __DIR__ . "/../model/RevistasModel.php";

class RevistasController
{
    private $revistasModel;

    public function __construct()
    {
        $this->revistasModel = new RevistasModel();
    }

    /**
     * Obtener todas las revistas con filtros opcionales
     * 
     * @param array $filtros Filtros a aplicar (editorial, precio_max)
     * @return array Lista de revistas
     */
    public function getRevistas($filtros = [])
    {
        return $this->revistasModel->getAllRevistas($filtros);
    }

    /**
     * Obtener una revista por su ID
     * 
     * @param int $idRevista ID de la revista
     * @return array|false Datos de la revista o false si no existe
     */
    public function getRevistaById($idRevista)
    {
        return $this->revistasModel->getRevistaById($idRevista);
    }

    /**
     * Obtener editoriales únicas para filtros
     * 
     * @return array Lista de editoriales
     */
    public function getEditoriales()
    {
        return $this->revistasModel->getEditoriales();
    }

    /**
     * Obtener revistas relacionadas por editorial
     * 
     * @param int $idRevista ID de la revista a excluir
     * @param string $editorial Editorial para relacionar
     * @param int $limit Número máximo de resultados
     * @return array Revistas relacionadas
     */
    public function getRevistasRelacionadas($idRevista, $editorial, $limit = 4)
    {
        return $this->revistasModel->getRevistasRelacionadas($idRevista, $editorial, $limit);
    }
}
