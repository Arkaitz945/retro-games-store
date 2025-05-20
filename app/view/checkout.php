<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Incluir controladores necesarios
require_once "../controller/CarritoController.php";
require_once "../controller/PedidoController.php";

$carritoController = new CarritoController();
$pedidoController = new PedidoController();
$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Inicializar variables para mensajes
$mensaje = null;
$tipoMensaje = null;

// Verificar si hay productos en el carrito
$cartItems = $carritoController->getCart($idUsuario);
if (empty($cartItems)) {
    header("Location: carrito.php");
    exit();
}

// Procesar la confirmación del pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_pedido'])) {
    try {
        // 1. Reducir el stock de los productos
        $resultadoStock = $carritoController->reduceStock($idUsuario);

        if ($resultadoStock['success']) {
            // Generar número de pedido
            $numeroPedido = "PED-" . date('Ymd') . "-" . rand(1000, 9999);

            // Calcular el total del carrito
            $cartTotal = $carritoController->getCartTotal($idUsuario);

            // 2. Guardar el pedido y sus detalles en la base de datos
            $resultadoPedido = $pedidoController->savePedido($idUsuario, $numeroPedido, $cartItems, $cartTotal);

            if ($resultadoPedido['success']) {
                // 3. Vaciar el carrito después de completar el pedido
                $carritoController->clearCart($idUsuario);

                // Registrar mensaje de éxito para debugging
                error_log("Pedido completado exitosamente: " . $numeroPedido);

                // Redirigir a la página de agradecimiento con el número de pedido
                header("Location: gracias.php?pedido=" . urlencode($numeroPedido));
                exit();
            } else {
                // Si hay error al guardar el pedido, mostrar mensaje
                $mensaje = $resultadoPedido['message'];
                $tipoMensaje = 'error';
                error_log("Error al guardar pedido: " . $mensaje);
            }
        } else {
            // Si hay error con el stock, mostrar mensaje
            $mensaje = $resultadoStock['message'];
            $tipoMensaje = 'error';
            error_log("Error con el stock: " . $mensaje);
        }
    } catch (Exception $e) {
        $mensaje = "Error inesperado: " . $e->getMessage();
        $tipoMensaje = 'error';
        error_log("Exception en checkout: " . $e->getMessage());
    }
}

// Obtener el total del carrito
$cartTotal = $carritoController->getCartTotal($idUsuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/carrito.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .order-summary {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }

        .summary-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #ddd;
            padding-top: 15px;
            margin-top: 15px;
        }

        .btn-confirm {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .btn-confirm:hover {
            background: #3e8e41;
        }

        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <a href="home.php" class="logo-link">
                <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="videojuegos.php">Videojuegos</a></li>
                <li><a href="consolas.php">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
                <li><a href="accesorios.php">Accesorios</a></li>
                <?php if ($esAdmin): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-dropdown">
                <button class="user-btn"><i class="fas fa-user"></i> <?php echo htmlspecialchars($nombreUsuario); ?> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                        <a href="admin/dashboard.php"><i class="fas fa-user-shield"></i> Panel de Administración</a>
                        <div class="dropdown-divider"></div>
                    <?php endif; ?>
                    <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
                    <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="checkout-container">
            <div class="page-header">
                <h1>Finalizar Compra</h1>
                <p>Revisa tu pedido y confirma para completar la compra</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="order-summary">
                <div class="summary-header">Resumen del Pedido</div>

                <?php foreach ($cartItems as $item): ?>
                    <div class="summary-item">
                        <div>
                            <?php echo htmlspecialchars($item['nombre']); ?>
                            <span class="item-quantity">x<?php echo $item['cantidad']; ?></span>
                        </div>
                        <div><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>€</div>
                    </div>
                <?php endforeach; ?>

                <div class="summary-item">
                    <div>Subtotal</div>
                    <div><?php echo number_format($cartTotal, 2); ?>€</div>
                </div>

                <div class="summary-item">
                    <div>Envío</div>
                    <div>0.00€</div>
                </div>

                <div class="summary-item summary-total">
                    <div>Total</div>
                    <div><?php echo number_format($cartTotal, 2); ?>€</div>
                </div>
            </div>

            <form method="post" action="checkout.php">
                <button type="submit" name="confirmar_pedido" class="btn-confirm">
                    Confirmar Pedido <i class="fas fa-check"></i>
                </button>
            </form>

            <p style="text-align: center; margin-top: 15px;">
                <a href="carrito.php" style="color: #666; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Volver al carrito
                </a>
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Tu tienda especializada en videojuegos, consolas y revistas retro.</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="about.php">Sobre nosotros</a></li>
                        <li><a href="contact.php">Contacto</a></li>
                        <li><a href="faq.php">Preguntas frecuentes</a></li>
                        <li><a href="envios.php">Política de envíos</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Retro, 123, Ciudad</p>
                    <p><i class="fas fa-phone"></i> +34 923 456 789</p>
                    <p><i class="fas fa-envelope"></i> info@retrogamesstore.com</p>
                </div>
                <div class="footer-column">
                    <h3>Síguenos</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> RetroGames Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menú desplegable
        document.addEventListener('DOMContentLoaded', function() {
            const userBtn = document.querySelector('.user-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            userBtn.addEventListener('click', function() {
                dropdownContent.classList.toggle('show');
            });

            window.addEventListener('click', function(event) {
                if (!event.target.matches('.user-btn') && !event.target.parentNode.matches('.user-btn')) {
                    if (dropdownContent.classList.contains('show')) {
                        dropdownContent.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>

</html>