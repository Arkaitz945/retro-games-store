<?php

/**
 * Establece conexión con la base de datos
 * @return PDO Objeto de conexión PDO o null en caso de error
 */
function getDBConnection()
{
    try {
        $host = "localhost";
        $dbname = "retro_games"; // Cambiado de retro_games_db a retro_games
        $username = "root";
        $password = "";

        // Intentar conexión simple primero (sin base de datos específica)
        try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si la base de datos existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
            $dbExists = $stmt->fetch();

            // Si no existe, mostrar un mensaje de error claro
            if (!$dbExists) {
                error_log("La base de datos '$dbname' no existe.");
                throw new PDOException("La base de datos '$dbname' no existe. Por favor, verifica el nombre correcto.");
            }

            // Ahora conectamos a la base de datos que sabemos que existe
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $conn = new PDO($dsn, $username, $password, $options);
            error_log("Conexión exitosa a la base de datos $dbname");

            return $conn;
        } catch (PDOException $e) {
            throw new PDOException("Error al conectar al servidor MySQL: " . $e->getMessage());
        }
    } catch (PDOException $e) {
        error_log("Error crítico de conexión: " . $e->getMessage());
        echo "Error de conexión: " . $e->getMessage();
        return null;
    }
}

/**
 * Función de ayuda para verificar conexión y mostrar errores detallados
 * Úsala en cualquier página para diagnosticar problemas
 */
function testDatabaseConnection()
{
    try {
        $conn = getDBConnection();
        if ($conn) {
            echo "<div style='color:green; padding:10px; border:1px solid green; margin:10px;'>";
            echo "✅ Conexión a la base de datos exitosa!";
            echo "</div>";
            return true;
        } else {
            echo "<div style='color:red; padding:10px; border:1px solid red; margin:10px;'>";
            echo "❌ No se pudo establecer conexión con la base de datos.";
            echo "</div>";
            return false;
        }
    } catch (Exception $e) {
        echo "<div style='color:red; padding:10px; border:1px solid red; margin:10px;'>";
        echo "❌ Error: " . $e->getMessage();
        echo "</div>";
        return false;
    }
}
