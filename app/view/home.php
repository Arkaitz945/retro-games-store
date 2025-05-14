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
        <section class="hero-banner">
            <div class="hero-content">
                <h2>Revive la magia de los clásicos</h2>
                <p>Tu destino para videojuegos, consolas y revistas retro</p>
                <a href="#catalogo" class="btn-primary">Ver Catálogo</a>
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

        <!-- Categorías de productos -->
        <section id="catalogo" class="categories-section">
            <div class="container">
                <h2>Explora nuestro catálogo</h2>
                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-img videogames-img"></div>
                        <h3>Videojuegos</h3>
                        <p>Desde clásicos de NES hasta joyas de PlayStation, tenemos títulos para todas las generaciones.</p>
                        <a href="videojuegos.php" class="btn-secondary">Ver Videojuegos</a>
                    </div>

                    <div class="category-card">
                        <div class="category-img consoles-img"></div>
                        <h3>Consolas</h3>
                        <p>Consolas vintage en perfecto estado, restauradas y listas para revivir la magia.</p>
                        <a href="consolas.php" class="btn-secondary">Ver Consolas</a>
                    </div>

                    <div class="category-card">
                        <div class="category-img magazines-img"></div>
                        <h3>Revistas</h3>
                        <p>Publicaciones originales que documentan la evolución de los videojuegos a lo largo de las décadas.</p>
                        <a href="revistas.php" class="btn-secondary">Ver Revistas</a>
                    </div>

                    <div class="category-card">
                        <div class="category-img accessories-img"></div>
                        <h3>Accesorios</h3>
                        <p>Mandos, adaptadores, fundas y todo lo necesario para completar tu experiencia retro.</p>
                        <a href="accesorios.php" class="btn-secondary">Ver Accesorios</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de novedades o destacados -->
        <section class="featured-section">
            <div class="container">
                <h2>Productos Destacados</h2>
                <div class="featured-grid">
                    <!-- Aquí iría un loop PHP para mostrar productos destacados de la base de datos -->
                    <!-- Por ahora, colocaré algunos ejemplos estáticos -->
                    <div class="product-card">
                        <div class="product-img" style="background-image: url('img/products/super-mario-64.jpg');"></div>
                        <h3>Super Mario 64</h3>
                        <p class="price">59.99€</p>
                        <a href="producto.php?id=1" class="btn-secondary">Ver Detalles</a>
                    </div>

                    <div class="product-card">
                        <div class="product-img" style="background-image: url('img/products/ps1.jpg');"></div>
                        <h3>PlayStation 1</h3>
                        <p class="price">89.99€</p>
                        <a href="producto.php?id=2" class="btn-secondary">Ver Detalles</a>
                    </div>

                    <div class="product-card">
                        <div class="product-img" style="background-image: url('img/products/super-nes.jpg');"></div>
                        <h3>Super Nintendo</h3>
                        <p class="price">109.99€</p>
                        <a href="producto.php?id=3" class="btn-secondary">Ver Detalles</a>
                    </div>

                    <div class="product-card">
                        <div class="product-img" style="background-image: url('img/products/zelda-ocarina.jpg');"></div>
                        <h3>Zelda: Ocarina of Time</h3>
                        <p class="price">69.99€</p>
                        <a href="producto.php?id=4" class="btn-secondary">Ver Detalles</a>
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