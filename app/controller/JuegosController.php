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

    /**
     * Obtener un juego por su ID
     * 
     * @param int $id ID del juego
     * @return mixed Array con la información del juego o false si no existe
     */
    public function getJuegoById($id)
    {
        return $this->juegosModel->getJuegoById($id);
    }

    /**
     * Obtener juegos relacionados por plataforma o género
     * 
     * @param int $idJuego ID del juego actual (para excluirlo)
     * @param string $plataforma Plataforma del juego
     * @param string $genero Género del juego
     * @param int $limit Número máximo de juegos a devolver
     * @return array Array con los juegos relacionados
     */
    public function getJuegosRelacionados($idJuego, $plataforma, $genero, $limit = 4)
    {
        // Implementa un método en el modelo para obtener juegos relacionados
        // por ahora, simplemente obtendremos algunos juegos de la misma plataforma o género
        $filtros = [];

        // Primero intentamos buscar juegos de la misma plataforma
        $filtros['plataforma'] = $plataforma;
        $juegos = $this->juegosModel->getRelatedJuegos($idJuego, $filtros, $limit);

        // Si no hay suficientes, completamos con juegos del mismo género
        if (count($juegos) < $limit) {
            $filtros = [];
            $filtros['genero'] = $genero;
            $additionalJuegos = $this->juegosModel->getRelatedJuegos($idJuego, $filtros, $limit - count($juegos));
            $juegos = array_merge($juegos, $additionalJuegos);
        }

        // Si aún no hay suficientes, completamos con otros juegos
        if (count($juegos) < $limit) {
            $additionalJuegos = $this->juegosModel->getRelatedJuegos($idJuego, [], $limit - count($juegos));
            $juegos = array_merge($juegos, $additionalJuegos);
        }

        return $juegos;
    }
}
