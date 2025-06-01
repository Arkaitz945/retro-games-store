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

$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroGames Store - Tu tienda de videojuegos clásicos</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilo renovado para la sección del catálogo */
        .catalog-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #2e294e, #4B4474);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .catalog-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('img/pattern-retro.png');
            opacity: 0.05;
            pointer-events: none;
        }

        .catalog-heading {
            text-align: center;
            margin-bottom: 50px;
            color: #FFD700;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .catalog-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .catalog-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: calc(25% - 30px);
            min-width: 250px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-bottom: 4px solid #FFD700;
        }

        .catalog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .catalog-card h2 {
            color: #2e294e;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 10px;
        }

        .catalog-card h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #FFD700;
        }

        .catalog-card p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 25px;
            flex-grow: 1;
        }

        .catalog-btn {
            display: inline-block;
            background-color: #2e294e;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            text-align: center;
            margin-top: auto;
            border: 2px solid #2e294e;
        }

        .catalog-btn:hover {
            background-color: #FFD700;
            color: #2e294e;
            border-color: #FFD700;
            transform: scale(1.05);
        }

        /* Nuevo estilo para el banner principal */
        .hero-section {
            position: relative;
            padding: 80px 0;
            background: linear-gradient(to right, #2e294e, #4a3f73);
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('img/background/pixelated-pattern.png');
            opacity: 0.1;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Cambio específico al estilo del título para hacerlo más limpio */
        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
            /* Quito animaciones y efectos de brillo */
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-btn {
            display: inline-block;
            background-color: #FFD700;
            color: #2e294e;
            font-weight: bold;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 2px solid #FFD700;
        }

        .hero-btn:hover {
            background-color: transparent;
            color: #FFD700;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
        }

        /* Decoraciones retro */
        .retro-decoration {
            position: absolute;
            z-index: 1;
        }

        .retro-decoration.pacman {
            width: 50px;
            height: 50px;
            top: 20%;
            left: 5%;
            background: url('img/icons/pacman.png') no-repeat;
            background-size: contain;
            animation: float 5s ease-in-out infinite;
        }

        .retro-decoration.space-invader {
            width: 40px;
            height: 40px;
            top: 30%;
            right: 8%;
            background: url('img/icons/space-invader.png') no-repeat;
            background-size: contain;
            animation: float 4s ease-in-out infinite 1s;
        }

        .retro-decoration.tetris {
            width: 60px;
            height: 60px;
            bottom: 20%;
            right: 15%;
            background: url('img/icons/tetris.png') no-repeat;
            background-size: contain;
            animation: float 6s ease-in-out infinite 2s;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .catalog-card {
                width: calc(50% - 30px);
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .catalog-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="home.php" class="active">Inicio</a></li>
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

    <main>
        <?php if (isset($_SESSION['registro_exitoso'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['registro_exitoso']; ?>
                <?php unset($_SESSION['registro_exitoso']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['login_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['login_message']; ?>
                <?php unset($_SESSION['login_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Resto del contenido de la página... -->

        <!-- Banner promocional -->
        <section class="hero-section">
            <!-- Decoraciones retro (opcionales, se pueden eliminar si no se tienen las imágenes) -->
            <div class="retro-decoration pacman"></div>
            <div class="retro-decoration space-invader"></div>
            <div class="retro-decoration tetris"></div>

            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">Revive la magia de los clásicos</h1>
                    <p class="hero-subtitle">Videojuegos, consolas y accesorios de épocas pasadas para coleccionistas y amantes de lo retro</p>
                    <a href="videojuegos.php" class="hero-btn">Explorar catálogo</a>
                </div>
            </div>
        </section>

        <!-- Presentación de la tienda -->
        <section class="about-section">
            <div class="container">
                <h2>Bienvenido a RetroGames Store</h2>
                <p>
                    En RetroGames Store, nos apasiona la historia de los videojuegos. Somos tu destino
                    para encontrar esos tesoros de la infancia que marcaron una época. Desde las primeras
                    consolas hasta los títulos más emblemáticos, nuestra misión es preservar el legado
                    del gaming retro y compartirlo con las nuevas generaciones.
                </p>
                <p>
                    Ofrecemos una cuidadosa selección de videojuegos, consolas restauradas y revistas
                    especializadas que capturan la esencia de cada era. Todos nuestros productos son
                    verificados y probados para garantizar su funcionamiento.
                </p>
            </div>
        </section>

        <!-- Sección del catálogo -->
        <section class="catalog-section">
            <div class="container">
                <h2 class="catalog-heading">Explora nuestro catálogo</h2>

                <div class="catalog-grid">
                    <!-- Tarjeta de Videojuegos -->
                    <div class="catalog-card">
                        <h2>Videojuegos</h2>
                        <p>Desde clásicos de NES hasta joyas de PlayStation, tenemos títulos para todas las generaciones.</p>
                        <a href="videojuegos.php" class="catalog-btn">Ver Videojuegos</a>
                    </div>

                    <!-- Tarjeta de Consolas -->
                    <div class="catalog-card">
                        <h2>Consolas</h2>
                        <p>Consolas vintage en perfecto estado, restauradas y listas para revivir la magia.</p>
                        <a href="consolas.php" class="catalog-btn">Ver Consolas</a>
                    </div>

                    <!-- Tarjeta de Revistas -->
                    <div class="catalog-card">
                        <h2>Revistas</h2>
                        <p>Publicaciones originales que documentan la evolución de los videojuegos a lo largo de las décadas.</p>
                        <a href="revistas.php" class="catalog-btn">Ver Revistas</a>
                    </div>

                    <!-- Tarjeta de Accesorios -->
                    <div class="catalog-card">
                        <h2>Accesorios</h2>
                        <p>Mandos, adaptadores, fundas y todo lo necesario para completar tu experiencia retro.</p>
                        <a href="accesorios.php" class="catalog-btn">Ver Accesorios</a>
                    </div>
                </div>
            </div>
        </section>
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
        // JavaScript para el menú desplegable
        document.addEventListener('DOMContentLoaded', function() {
            const userBtn = document.querySelector('.user-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            userBtn.addEventListener('click', function() {
                dropdownContent.classList.toggle('show');
            });

            // Cerrar el menú si el usuario hace clic afuera
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