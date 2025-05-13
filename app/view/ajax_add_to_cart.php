<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Debe iniciar sesión para añadir productos al carrito'
    ]);
    exit();
}

// Incluir controlador
require_once "../controller/CarritoController.php";

$carritoController = new CarritoController();
$idUsuario = $_SESSION['id'];

// Procesar la solicitud
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $productoId = (int)$_GET['id'];
    $tipoProducto = isset($_GET['tipo']) ? $_GET['tipo'] : 'juego';
    $cantidad = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 1;

    $resultado = $carritoController->addToCart($idUsuario, $tipoProducto, $productoId, $cantidad);

    // Obtener la cantidad actual en el carrito
    $cartCount = $carritoController->countCartItems($idUsuario);

    // Añadir el contador al resultado
    $resultado['cartCount'] = $cartCount;

    // Devolver respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit();
}

// Si no hay acción válida, devolver error
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Solicitud no válida'
]);
