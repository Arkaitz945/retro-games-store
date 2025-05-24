<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión
require_once 'app/config/dbConnection.php';

echo "<h1>Verificación y Corrección de Tablas</h1>";

// Obtener conexión
$conn = getDBConnection();

if (!$conn) {
    die("<p style='color:red'>No se pudo conectar a la base de datos.</p>");
}

echo "<p style='color:green'>Conexión a la base de datos establecida correctamente.</p>";

// Verificar la estructura de la tabla pedidos
echo "<h2>Verificando tabla 'pedidos'</h2>";

try {
    // Comprobar si la tabla existe
    $stmt = $conn->query("SHOW TABLES LIKE 'pedidos'");
    $tablaPedidosExiste = $stmt->rowCount() > 0;

    if (!$tablaPedidosExiste) {
        echo "<p>La tabla 'pedidos' no existe. Creando...</p>";

        $crearTablaPedidos = "
        CREATE TABLE pedidos (
            id_pedido INT AUTO_INCREMENT PRIMARY KEY,
            numero_pedido VARCHAR(50) NOT NULL,
            id_usuario INT NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            fecha DATETIME NOT NULL,
            estado VARCHAR(20) NOT NULL DEFAULT 'pendiente'
        ) ENGINE=InnoDB
        ";

        $conn->exec($crearTablaPedidos);
        echo "<p style='color:green'>Tabla 'pedidos' creada correctamente.</p>";
    } else {
        echo "<p style='color:green'>La tabla 'pedidos' ya existe.</p>";

        // Mostrar la estructura actual
        $columnas = $conn->query("DESCRIBE pedidos")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Columnas en 'pedidos': " . implode(", ", $columnas) . "</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error al verificar/crear la tabla 'pedidos': " . $e->getMessage() . "</p>";
}

// Verificar la estructura de la tabla detalles_pedido
echo "<h2>Verificando tabla 'detalles_pedido'</h2>";

try {
    // Comprobar si la tabla existe
    $stmt = $conn->query("SHOW TABLES LIKE 'detalles_pedido'");
    $tablaDetallesExiste = $stmt->rowCount() > 0;

    if (!$tablaDetallesExiste) {
        echo "<p>La tabla 'detalles_pedido' no existe. Creando...</p>";

        $crearTablaDetalles = "
        CREATE TABLE detalles_pedido (
            ID_Detalle INT AUTO_INCREMENT PRIMARY KEY,
            id_pedido INT NOT NULL,
            tipo_producto VARCHAR(20) NOT NULL DEFAULT 'juego',
            id_producto INT NOT NULL,
            cantidad INT NOT NULL,
            precio DECIMAL(10,2) NOT NULL
        ) ENGINE=InnoDB
        ";

        $conn->exec($crearTablaDetalles);
        echo "<p style='color:green'>Tabla 'detalles_pedido' creada correctamente.</p>";
    } else {
        echo "<p style='color:green'>La tabla 'detalles_pedido' ya existe.</p>";

        // Mostrar la estructura actual
        $columnas = $conn->query("DESCRIBE detalles_pedido")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Columnas en 'detalles_pedido': " . implode(", ", $columnas) . "</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error al verificar/crear la tabla 'detalles_pedido': " . $e->getMessage() . "</p>";
}

echo "<p><a href='app/view/checkout.php'>Volver al checkout</a></p>";
