<?php

require_once __DIR__ . "/../app/config/dbConnection.php";

class Database
{
    private $host = "localhost";
    private $dbName = "retro_games"; // Usar el mismo nombre que en dbConnection.php
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection()
    {
        // Usar la función global para mantener consistencia
        $this->conn = getDBConnection();
        return $this->conn;
    }

    // Para mantener compatibilidad con código existente
    public function getDBConnection()
    {
        return $this->getConnection();
    }
}
