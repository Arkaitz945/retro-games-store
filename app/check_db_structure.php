<?php
// Este script verifica y ajusta la estructura de la tabla detalles_pedido
require_once "config/dbConnection.php";

$conn = getDBConnection();

if (!$conn) {
    die("No se pudo conectar a la base de datos");
}

try {
    // Verificar si existe la tabla detalles_pedido
    $tableCheck = $conn->query("SHOW TABLES LIKE 'detalles_pedido'");
    $tableExists = $tableCheck->rowCount() > 0;

    if (!$tableExists) {
        // Crear la tabla con la estructura correcta
        $createTable = "CREATE TABLE detalles_pedido (
            ID_Detalle INT AUTO_INCREMENT PRIMARY KEY,
            ID_Pedido INT NOT NULL,
            ID_Producto INT NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (ID_Pedido) REFERENCES pedidos(ID_Pedido) ON DELETE CASCADE
        )";

        $conn->exec($createTable);
        echo "Tabla detalles_pedido creada correctamente<br>";
    } else {
        // Verificar estructura actual
        $columnsCheck = $conn->query("SHOW COLUMNS FROM detalles_pedido");
        $columns = $columnsCheck->fetchAll(PDO::FETCH_COLUMN);

        echo "Estructura actual de detalles_pedido: " . implode(", ", $columns) . "<br>";

        // Verificar campos específicos y agregarlos si faltan
        $requiredColumns = [
            'ID_Detalle' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'ID_Pedido' => 'INT NOT NULL',
            'ID_Producto' => 'INT NOT NULL',
            'cantidad' => 'INT NOT NULL',
            'precio_unitario' => 'DECIMAL(10,2) NOT NULL'
        ];

        foreach ($requiredColumns as $column => $definition) {
            if (!in_array($column, $columns)) {
                $conn->exec("ALTER TABLE detalles_pedido ADD COLUMN $column $definition");
                echo "Columna $column agregada a detalles_pedido<br>";
            }
        }
    }

    echo "Verificación completada con éxito.";
} catch (PDOException $e) {
    die("Error al verificar/modificar estructura: " . $e->getMessage());
}
