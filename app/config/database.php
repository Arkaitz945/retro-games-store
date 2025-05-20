<?php

class Database
{
    private $host = "localhost";
    private $dbName = "retro_games"; // Posible corrección del nombre de la base de datos
    private $username = "root"; // Usuario por defecto para XAMPP
    private $password = ""; // Contraseña por defecto para XAMPP (vacía)
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            // Mejorar el manejo de errores y logging
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName;
            error_log("Database: Intentando conectar a {$dsn}");

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

            // Verificar si la conexión es válida
            if ($this->conn) {
                // Añadir log para verificar conexión exitosa
                error_log("Database: Conexión establecida correctamente a la base de datos " . $this->dbName);
            } else {
                error_log("Database: La conexión se ha creado pero es nula");
            }
        } catch (PDOException $e) {
            error_log("Database: Error de conexión: " . $e->getMessage());

            // Verificar si el problema es que la base de datos no existe
            if (strpos($e->getMessage(), "Unknown database") !== false) {
                error_log("Database: La base de datos '{$this->dbName}' no existe. Verifique el nombre correcto.");
            }
        }

        return $this->conn;
    }
}
