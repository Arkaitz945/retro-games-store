<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

// Get the header HTML content directly from a working page in your project
// For now, we'll use a simplified header to make the page work
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesorios Retro - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/productos.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .coming-soon {
            text-align: center;
            padding: 100px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
            max-width: 800px;
        }

        .coming-soon i {
            font-size: 80px;
            color: #2e294e;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }

        .coming-soon h2 {
            font-size: 36px;
            color: #2e294e;
            margin-bottom: 20px;
        }

        .coming-soon p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .notify-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }

        .notify-form input {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
        }

        .notify-btn {
            background-color: #2e294e;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .notify-btn:hover {
            background-color: #3d366a;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .notify-form {
                flex-direction: column;
                width: 100%;
            }

            .notify-form input {
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .notify-btn {
                border-radius: 5px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Banner superior (simplified version) -->
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
                <li><a href="accesorios.php" class="active">Accesorios</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <?php if (isset($_SESSION['usuario'])): ?>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                        <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                            <a href="admin/dashboard.php"><i class="fas fa-user-shield"></i> Admin Panel</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <?php endif; ?>

            <a href="carrito.php" class="cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                    <span class="cart-badge"><?php echo array_sum(array_column($_SESSION['carrito'], 'cantidad')); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="page-header">
            <div class="container">
                <h1><i class="fas fa-plug"></i> Accesorios Retro</h1>
                <p>Tu destino para accesorios de consolas clásicas y repuestos retro</p>
            </div>
        </div>

        <div class="container">
            <div class="coming-soon">
                <i class="fas fa-tools"></i>
                <h2>¡Próximamente!</h2>
                <p>Estamos trabajando en nuestra sección de accesorios retro. Muy pronto podrás encontrar aquí mandos, adaptadores, cables, tarjetas de memoria y muchos más accesorios para tus consolas favoritas.</p>

                <p>Suscríbete para recibir notificaciones cuando la sección esté disponible:</p>

                <form class="notify-form" action="#" method="post">
                    <input type="email" placeholder="Tu correo electrónico" required>
                    <button type="submit" class="notify-btn">Notificarme</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer (simplified version) -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Tu tienda de videojuegos y consolas retro</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="home.php">Inicio</a></li>
                        <li><a href="videojuegos.php">Videojuegos</a></li>
                        <li><a href="consolas.php">Consolas</a></li>
                        <li><a href="revistas.php">Revistas</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p>Calle Falsa 123</p>
                    <p>info@retrogames.com</p>
                    <p>+34 123 456 789</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> RetroGames Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formulario de notificación
            const notifyForm = document.querySelector('.notify-form');
            if (notifyForm) {
                notifyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const emailInput = this.querySelector('input[type="email"]');
                    const email = emailInput.value;

                    // Aquí podrías enviar el email a tu servidor
                    // Pero por ahora solo mostraremos un mensaje
                    alert('¡Gracias! Te notificaremos en ' + email + ' cuando la sección de accesorios esté disponible.');
                    emailInput.value = '';
                });
            }
        });
    </script>
</body>

</html>