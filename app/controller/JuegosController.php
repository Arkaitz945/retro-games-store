<?php

require_once "../model/JuegosModel.php";

class JuegosController
{
    private $juegosModel;

    public function __construct()
    {
        $this->juegosModel = new JuegosModel();
    }

    /**
     * Obtener todos los juegos con filtros opcionales
     * 
     * @param array $filtros Filtros a aplicar
     * @return array Juegos filtrados
     */
    public function getJuegos($filtros = [])
    {
        return $this->juegosModel->getAllJuegos($filtros);
    }

    /**
     * Obtener plataformas disponibles
     * 
     * @return array Plataformas
     */
    public function getPlataformas()
    {
        return $this->juegosModel->getPlataformas();
    }

    /**
     * Obtener géneros disponibles
     * 
     * @return array Géneros
     */
    public function getGeneros()
    {
        return $this->juegosModel->getGeneros();
    }

    /**
     * Obtener estados disponibles
     * 
     * @return array Estados
     */
    public function getEstados()
    {
        return $this->juegosModel->getEstados();
    }
}
