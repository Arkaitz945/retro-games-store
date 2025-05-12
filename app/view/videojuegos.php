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

// Incluir controlador
require_once "../controller/JuegosController.php";
require_once "../controller/CarritoController.php";

$juegosController = new JuegosController();
$carritoController = new CarritoController();
$cantidadCarrito = isset($_SESSION['id']) ? $carritoController->countCartItems($_SESSION['id']) : 0;

// Obtener filtros de la URL
$filtros = [];
if (isset($_GET['plataforma'])) $filtros['plataforma'] = $_GET['plataforma'];
if (isset($_GET['genero'])) $filtros['genero'] = $_GET['genero'];
if (isset($_GET['estado'])) $filtros['estado'] = $_GET['estado'];
if (isset($_GET['precio_max']) && is_numeric($_GET['precio_max'])) $filtros['precio_max'] = $_GET['precio_max'];

// Obtener datos para los filtros
$plataformas = $juegosController->getPlataformas();
$generos = $juegosController->getGeneros();
$estados = $juegosController->getEstados();

// Obtener juegos según filtros
$juegos = $juegosController->getJuegos($filtros);

$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videojuegos - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/productos.css">
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
                <li><a href="home.php">Inicio</a></li>
                <li><a href="videojuegos.php" class="active">Videojuegos</a></li>
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
                    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito
                        <?php if ($cantidadCarrito > 0): ?>
                            <span class="cart-badge"><?php echo $cantidadCarrito; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="container">
            <div class="page-header">
                <h1>Videojuegos Retro</h1>
                <p>Explora nuestra colección de videojuegos clásicos de diferentes plataformas y épocas.</p>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <h3>Filtrar por:</h3>
                <form action="videojuegos.php" method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="plataforma">Plataforma:</label>
                        <select name="plataforma" id="plataforma">
                            <option value="">Todas las plataformas</option>
                            <?php foreach ($plataformas as $plataforma): ?>
                                <option value="<?php echo $plataforma; ?>" <?php echo (isset($_GET['plataforma']) && $_GET['plataforma'] == $plataforma) ? 'selected' : ''; ?>>
                                    <?php echo $plataforma; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="genero">Género:</label>
                        <select name="genero" id="genero">
                            <option value="">Todos los géneros</option>
                            <?php foreach ($generos as $genero): ?>
                                <option value="<?php echo $genero; ?>" <?php echo (isset($_GET['genero']) && $_GET['genero'] == $genero) ? 'selected' : ''; ?>>
                                    <?php echo $genero; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="estado">Estado:</label>
                        <select name="estado" id="estado">
                            <option value="">Todos los estados</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?php echo $estado; ?>" <?php echo (isset($_GET['estado']) && $_GET['estado'] == $estado) ? 'selected' : ''; ?>>
                                    <?php echo $estado; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="precio_max">Precio máximo:</label>
                        <input type="number" name="precio_max" id="precio_max" min="0" step="1" value="<?php echo isset($_GET['precio_max']) ? $_GET['precio_max'] : ''; ?>">
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter">Aplicar filtros</button>
                        <a href="videojuegos.php" class="btn-clear">Limpiar filtros</a>
                    </div>
                </form>
            </div>

            <!-- Resultados -->
            <div class="results-info">
                <p>Se encontraron <?php echo count($juegos); ?> productos</p>
            </div>

            <!-- Productos -->
            <div class="products-grid">
                <?php if (empty($juegos)): ?>
                    <div class="no-results">
                        <p>No se encontraron juegos con los filtros seleccionados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($juegos as $juego): ?>
                        <div class="product-card">
                            <div class="product-img" style="background-image: url('<?php echo htmlspecialchars($juego['imagen']); ?>');"></div>
                            <div class="product-platform"><?php echo htmlspecialchars($juego['plataforma']); ?></div>
                            <h3><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                            <div class="product-details">
                                <span class="product-genre"><?php echo htmlspecialchars($juego['genero']); ?></span>
                                <span class="product-year"><?php echo htmlspecialchars($juego['año_lanzamiento']); ?></span>
                            </div>
                            <div class="product-condition"><?php echo htmlspecialchars($juego['estado']); ?></div>
                            <p class="price"><?php echo number_format($juego['precio'], 2); ?>€</p>
                            <div class="product-actions">
                                <a href="producto.php?id=<?php echo $juego['ID_J']; ?>" class="btn-secondary">Ver Detalles</a>
                                <?php if ($juego['stock'] > 0): ?>
                                    <a href="carrito.php?action=add&tipo=juego&id=<?php echo $juego['ID_J']; ?>" class="btn-add-cart">
                                        <i class="fas fa-cart-plus"></i> Añadir
                                    </a>
                                <?php else: ?>
                                    <span class="out-of-stock">Agotado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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