<?php

function getDBConnection()
{
    $host = "localhost";
    $db_name = "retrogamestore";
    $username = "admin";
    $password = "admin";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        // Mejorar el manejo de errores para más detalles
        error_log('Connection error: ' . $e->getMessage());
        echo 'Error de conexión: No se pudo conectar a la base de datos';
        return null;
    }
}
