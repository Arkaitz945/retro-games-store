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
require_once "../helpers/ImageHelper.php";

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
    <link rel="stylesheet" href="css/videojuegos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/notification.css">
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
                    <?php if ($esAdmin): ?>
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
                            <?php
                            // Mantener la corrección de rutas de imágenes
                            $imagen = 'css/img/no-image.jpg';
                            if (!empty($juego['imagen'])) {
                                $nombreArchivo = basename($juego['imagen']);
                                $imagen = "css/img/videojuegos/$nombreArchivo";
                            }

                            $id = isset($juego['id']) ? (int)$juego['id'] : 0;
                            $nombre = isset($juego['nombre']) ? htmlspecialchars($juego['nombre']) : 'Sin nombre';
                            $plataforma = isset($juego['plataforma']) ? htmlspecialchars($juego['plataforma']) : '';
                            $anio = isset($juego['ano']) ? htmlspecialchars($juego['ano']) : '';
                            $estado = isset($juego['estado']) ? htmlspecialchars($juego['estado']) : '';
                            $precio = isset($juego['precio']) ? number_format((float)$juego['precio'], 2) : '0.00';
                            ?>

                            <div class="product-image">
                                <?php if ($plataforma): ?>
                                    <span class="product-platform"><?php echo $plataforma; ?></span>
                                <?php endif; ?>
                                <img src="<?php echo $imagen; ?>" alt="<?php echo $nombre; ?>" class="product-img">
                            </div>
                            <div class="product-info">
                                <div>
                                    <h3 class="product-title"><?php echo $nombre; ?></h3>
                                    <div class="product-details">
                                        <?php if ($anio): ?>
                                            <span class="product-year"><?php echo $anio; ?></span>
                                        <?php endif; ?>
                                        <?php if ($estado): ?>
                                            <span class="product-state"><?php echo $estado; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <p class="product-price"><?php echo $precio; ?>€</p>
                                    <div class="product-buttons">
                                        <a href="producto.php?id=<?php echo $juego['ID_J']; ?>" class="btn btn-primary">Ver Detalles</a>
                                        <?php if ($juego['stock'] > 0): ?>
                                            <button
                                                class="btn btn-add-cart"
                                                data-id="<?php echo $juego['ID_J']; ?>"
                                                data-tipo="juego"
                                                data-nombre="<?php echo htmlspecialchars($juego['nombre']); ?>">
                                                <i class="fas fa-shopping-cart"></i> Añadir
                                            </button>
                                        <?php else: ?>
                                            <span class="out-of-stock">Agotado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Mantener solo esta notificación al final del body -->
    <div class="cart-toast" id="cart-toast">
        <div class="cart-toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="cart-toast-content">
            <div class="cart-toast-title">Añadido al carrito</div>
            <div class="cart-toast-message" id="cart-toast-message"></div>
        </div>
        <button class="cart-toast-close" onclick="closeToast()">
            <i class="fas fa-times"></i>
        </button>
    </div>

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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-add-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Add to cart button clicked');

                    // Obtener información del producto
                    const productId = this.getAttribute('data-id');
                    const productType = this.getAttribute('data-tipo') || 'juego';
                    const productName = this.getAttribute('data-nombre');

                    console.log(`Adding to cart: ${productName} (${productType} #${productId})`);

                    // Hacer una petición AJAX para añadir al carrito
                    fetch(`ajax_add_to_cart.php?action=add&tipo=${productType}&id=${productId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error de red al añadir al carrito');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Mostrar notificación de éxito
                                showToast(`${productName} se ha añadido correctamente a tu carrito`, false);
                                // Actualizar contador del carrito
                                if (data.cartCount) {
                                    updateCartBadge(data.cartCount);
                                } else {
                                    updateCartCounter();
                                }
                            } else {
                                // Mostrar notificación de error
                                showToast(data.message || 'Error al añadir el producto al carrito', true);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Error al añadir el producto al carrito', true);
                        });
                });
            });

            // Función para actualizar el contador del carrito
            function updateCartCounter() {
                fetch('get_cart_count.php')
                    .then(response => response.json())
                    .then(data => {
                        updateCartBadge(data.count);
                    })
                    .catch(error => {
                        console.error('Error al actualizar contador:', error);
                    });
            }

            // Función para actualizar el badge del carrito
            function updateCartBadge(count) {
                const badges = document.querySelectorAll('.cart-badge');
                if (badges.length > 0) {
                    badges.forEach(badge => {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'inline-block' : 'none';
                    });
                }
            }
        });

        // Función para mostrar la notificación toast
        function showToast(message, isError = false) {
            const toast = document.getElementById('cart-toast');
            const toastMessage = document.getElementById('cart-toast-message');

            // Cambiar el mensaje
            toastMessage.textContent = message;

            // Cambiar clase según tipo de mensaje
            if (isError) {
                toast.classList.add('error');
            } else {
                toast.classList.remove('error');
            }

            // Mostrar el toast
            toast.classList.add('show');

            // Establecer un timeout para ocultarlo
            setTimeout(function() {
                toast.classList.remove('show');
            }, 5000); // 5 segundos
        }

        // Función para cerrar el toast manualmente
        function closeToast() {
            document.getElementById('cart-toast').classList.remove('show');
        }
    </script>
</body>

</html>