<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión
require_once 'app/config/dbConnection.php';

echo "<h1>Verificación de la Estructura de la Base de Datos</h1>";

// Obtener conexión
$conn = getDBConnection();

if (!$conn) {
    die("<p style='color:red'>No se pudo conectar a la base de datos.</p>");
}

echo "<p style='color:green'>Conexión a la base de datos establecida correctamente.</p>";

// Función para verificar y crear tablas
function verificarTabla($conn, $tablaNombre, $sqlCreacion)
{
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$tablaNombre'");
        $tablaExiste = $stmt->rowCount() > 0;

        if (!$tablaExiste) {
            echo "<p>La tabla '$tablaNombre' no existe. Creando...</p>";
            $conn->exec($sqlCreacion);
            echo "<p style='color:green'>Tabla '$tablaNombre' creada correctamente.</p>";
        } else {
            echo "<p style='color:green'>La tabla '$tablaNombre' ya existe.</p>";

            // Mostrar estructura
            $columnas = $conn->query("SHOW COLUMNS FROM $tablaNombre")->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Columnas en '$tablaNombre': " . implode(", ", $columnas) . "</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error al verificar/crear la tabla '$tablaNombre': " . $e->getMessage() . "</p>";
    }
}

// SQL para crear la tabla pedidos
$sqlPedidos = "
CREATE TABLE pedidos (
    ID_Pedido INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(50) NOT NULL,
    ID_U INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha DATETIME NOT NULL,
    estado ENUM('pendiente', 'procesando', 'enviado', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB
";

// SQL para crear la tabla detalles_pedido
$sqlDetallesPedido = "
CREATE TABLE detalles_pedido (
    ID_Detalle INT AUTO_INCREMENT PRIMARY KEY,
    ID_Pedido INT NOT NULL,
    producto_id INT NOT NULL,
    tipo_producto VARCHAR(20) NOT NULL DEFAULT 'juego',
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ID_Pedido) REFERENCES pedidos(ID_Pedido) ON DELETE CASCADE
) ENGINE=InnoDB
";

// Verificar tablas
echo "<h2>Verificando tabla 'pedidos'</h2>";
verificarTabla($conn, 'pedidos', $sqlPedidos);

echo "<h2>Verificando tabla 'detalles_pedido'</h2>";
verificarTabla($conn, 'detalles_pedido', $sqlDetallesPedido);

// Verificar si hay pedidos
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM pedidos");
    $numPedidos = $stmt->fetchColumn();
    echo "<p>Número de pedidos en la base de datos: $numPedidos</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Error al contar pedidos: " . $e->getMessage() . "</p>";
}

echo "<p><a href='app/view/checkout.php'>Volver al checkout</a></p>";
