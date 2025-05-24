<?php

// Este archivo redirige al archivo de conexión principal
// para evitar duplicación de código y mantener consistencia

// Verificar si la función ya ha sido declarada
if (!function_exists('getDBConnection')) {
    // Incluir el archivo principal de conexión
    require_once __DIR__ . '/../app/config/dbConnection.php';
}

// No es necesario definir la función de nuevo, ya que se incluye desde el otro archivo
