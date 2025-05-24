<?php
// Script para mostrar la estructura de las tablas
require_once 'app/config/dbConnection.php';

echo "<h1>Estructura de tablas en la base de datos</h1>";

$conn = getDBConnection();
if (!$conn) {
    die("No se pudo conectar a la base de datos");
}

// Función para mostrar la estructura de una tabla
function showTableStructure($conn, $tableName)
{
    echo "<h2>Estructura de la tabla '$tableName':</h2>";

    try {
        $stmt = $conn->query("DESCRIBE $tableName");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Mostrar algunos datos de ejemplo
        $stmt = $conn->query("SELECT * FROM $tableName LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "<h3>Ejemplo de datos:</h3>";
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        } else {
            echo "<p>No hay datos en esta tabla.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error al obtener estructura: " . $e->getMessage() . "</p>";
    }
}

// Mostrar estructura de tablas relevantes
$tables = ['pedidos', 'detalles_pedido', 'carrito'];
foreach ($tables as $table) {
    showTableStructure($conn, $table);
}

echo "<p><a href='app/view/pedidos.php'>Volver a Pedidos</a></p>";
