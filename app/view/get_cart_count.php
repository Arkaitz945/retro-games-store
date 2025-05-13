<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['count' => 0]);
    exit();
}

// Incluir controlador
require_once "../controller/CarritoController.php";

$carritoController = new CarritoController();
$idUsuario = $_SESSION['id'];

// Obtener el número de items en el carrito
$count = $carritoController->countCartItems($idUsuario);

// Devolver como JSON
header('Content-Type: application/json');
echo json_encode(['count' => $count]);
