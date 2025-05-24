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

// Verificar si viene de un pedido confirmado
$numeroPedido = "";
$mostrarMensaje = false;

if (isset($_GET['pedido'])) {
    $numeroPedido = $_GET['pedido'];
    $mostrarMensaje = true;
} elseif (isset($_SESSION['numero_pedido'])) {
    $numeroPedido = $_SESSION['numero_pedido'];
    $mostrarMensaje = true;
    // Limpiar la sesión para evitar mostrar esta página al recargar
    unset($_SESSION['numero_pedido']);
    unset($_SESSION['pedido_completado']);
}

// Si no hay número de pedido, redirigir a carrito
if (!$mostrarMensaje) {
    header("Location: carrito.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Recuperar total del pedido si está disponible
$totalPedido = isset($_SESSION['total_pedido']) ? $_SESSION['total_pedido'] : null;
if (isset($_SESSION['total_pedido'])) {
    unset($_SESSION['total_pedido']);
}

// Si hay carrito en la sesión, eliminarlo
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
}

// Registrar para depuración
error_log("Mostrando página de agradecimiento para pedido: " . $numeroPedido);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Completado! - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/gracias.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Limpiar cualquier carrito en localStorage al cargar esta página
        window.onload = function() {
            localStorage.removeItem('carrito');
        };
    </script>
    <style>
        .confirmation-container {
            text-align: center;
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .confirmation-icon {
            font-size: 80px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .confirmation-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }

        .confirmation-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #666;
        }

        .order-number {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .delivery-joke {
            font-style: italic;
            font-size: 16px;
            background: #ffe8cc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-back {
            display: inline-block;
            background: #363636;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        .btn-back:hover {
            background: #222;
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
        <div class="confirmation-container">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="confirmation-title">¡Gracias por tu compra!</h1>
            <p class="confirmation-message">Hemos recibido tu pedido y estamos procesándolo.</p>

            <div class="order-number">
                Número de pedido: <?php echo htmlspecialchars($numeroPedido); ?>
            </div>

            <div class="delivery-joke">
                <p><i class="fas fa-truck"></i> Tu producto llegará en... (ni nosotros lo sabemos)</p>
                <p>Mientras tanto, puedes seguir coleccionando más tesoros retro o esperar pacientemente
                    junto a la ventana como en los viejos tiempos.</p>
            </div>

            <p>Te hemos enviado un correo electrónico con los detalles de tu compra.</p>
            <p>Puedes consultar el estado de tu pedido en cualquier momento desde la sección "Mis Pedidos".</p>

            <a href="home.php" class="btn-back">
                <i class="fas fa-home"></i> Volver a la tienda
            </a>
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