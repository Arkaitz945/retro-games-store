<?php

class Database
{
    private $host = "localhost";
    private $dbName = "retro_games"; // Asegurarse de que coincida con dbConnection.php
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            // Usar directamente getDBConnection para mantener consistencia
            require_once __DIR__ . "/dbConnection.php";
            $this->conn = getDBConnection();
            return $this->conn;
        } catch (PDOException $e) {
            error_log("Database: Error de conexión: " . $e->getMessage());
            return null;
        }
    }

    // Alias para mantener compatibilidad
    public function getDBConnection()
    {
        return $this->getConnection();
    }
}
