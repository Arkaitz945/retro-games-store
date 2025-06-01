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

// Verificar si se ha proporcionado un ID de revista
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: revistas.php");
    exit();
}

// Incluir controladores
require_once "../controller/RevistasController.php";
require_once "../controller/CarritoController.php";

$revistasController = new RevistasController();
$carritoController = new CarritoController();
$idRevista = (int)$_GET['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$cantidadCarrito = isset($_SESSION['id']) ? $carritoController->countCartItems($_SESSION['id']) : 0;

// Obtener datos de la revista
$revista = $revistasController->getRevistaById($idRevista);

// Si la revista no existe, redirigir
if (!$revista) {
    header("Location: revistas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($revista['titulo']); ?> - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/producto.css">
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos específicos para revistas */
        .product-editorial {
            margin-bottom: 15px;
        }

        .product-magazine-info {
            margin-bottom: 20px;
        }

        .product-magazine-info strong {
            font-weight: 600;
            display: inline-block;
            width: 140px;
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
                <li><a href="revistas.php" class="active">Revistas</a></li>
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
            <div class="breadcrumbs">
                <a href="home.php">Inicio</a> &gt;
                <a href="revistas.php">Revistas</a> &gt;
                <span><?php echo htmlspecialchars($revista['titulo']); ?></span>
            </div>

            <div class="product-detail">
                <div class="product-media">
                    <?php
                    // Corregir rutas de imágenes para revistas
                    $imagen = 'css/img/no-image.jpg';
                    if (!empty($revista['imagen'])) {
                        $nombreArchivo = basename($revista['imagen']);
                        $imagen = "css/img/revistas/$nombreArchivo";

                        // Verificar si existe la imagen
                        if (!file_exists($imagen)) {
                            // Intentar con la ruta completa si es absoluta
                            if (file_exists($revista['imagen'])) {
                                $imagen = $revista['imagen'];
                            } else {
                                $imagen = 'css/img/no-image.jpg';
                            }
                        }
                    }
                    ?>
                    <div class="product-main-image" style="background-image: url('<?php echo $imagen; ?>')"></div>
                </div>

                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($revista['titulo']); ?></h1>

                    <div class="product-meta">
                        <span class="product-editorial"><?php echo htmlspecialchars($revista['editorial']); ?></span>
                        <?php if (!empty($revista['fecha_publicacion'])): ?>
                            <span class="product-separator">|</span>
                            <span class="product-year"><?php echo date('Y', strtotime($revista['fecha_publicacion'])); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-magazine-info">
                        <div><strong>Editorial:</strong> <?php echo htmlspecialchars($revista['editorial']); ?></div>

                        <?php if (!empty($revista['fecha_publicacion'])): ?>
                            <div><strong>Fecha de publicación:</strong> <?php echo date('d/m/Y', strtotime($revista['fecha_publicacion'])); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($revista['numero'])): ?>
                            <div><strong>Número:</strong> <?php echo htmlspecialchars($revista['numero']); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($revista['paginas'])): ?>
                            <div><strong>Páginas:</strong> <?php echo htmlspecialchars($revista['paginas']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="product-stock">
                        <?php if ($revista['stock'] > 0): ?>
                            <span class="in-stock">
                                <i class="fas fa-check-circle"></i> En stock (<?php echo $revista['stock']; ?> disponibles)
                            </span>
                        <?php else: ?>
                            <span class="out-of-stock">
                                <i class="fas fa-times-circle"></i> Agotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="product-price">
                        <span class="price"><?php echo number_format($revista['precio'], 2); ?>€</span>
                    </div>

                    <div class="product-actions">
                        <?php if ($revista['stock'] > 0): ?>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus" id="decrease-quantity">-</button>
                                <input type="number" value="1" min="1" max="<?php echo $revista['stock']; ?>" id="product-quantity" class="quantity-input">
                                <button type="button" class="quantity-btn plus" id="increase-quantity">+</button>
                            </div>
                            <button type="button" id="add-to-cart-btn" class="btn-add-to-cart"
                                data-id="<?php echo $revista['ID_Revista']; ?>"
                                data-tipo="revista"
                                data-nombre="<?php echo htmlspecialchars($revista['titulo']); ?>">
                                <i class="fas fa-cart-plus"></i> Añadir al carrito
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-out-of-stock" disabled>
                                <i class="fas fa-times-circle"></i> Producto agotado
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="product-description">
                <h2>Descripción</h2>
                <div class="description-content">
                    <p><?php echo nl2br(htmlspecialchars($revista['descripcion'] ?? 'Sin descripción disponible')); ?></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast de notificación -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Control de menú desplegable
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

            // Control de cantidad
            const quantityInput = document.getElementById('product-quantity');
            const decreaseBtn = document.getElementById('decrease-quantity');
            const increaseBtn = document.getElementById('increase-quantity');

            if (quantityInput && decreaseBtn && increaseBtn) {
                decreaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value > 1) {
                        quantityInput.value = value - 1;
                    }
                });

                increaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    let max = parseInt(quantityInput.getAttribute('max'));
                    if (value < max) {
                        quantityInput.value = value + 1;
                    }
                });
            }

            // Añadir al carrito
            const addToCartBtn = document.getElementById('add-to-cart-btn');

            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const tipo = this.getAttribute('data-tipo');
                    const nombre = this.getAttribute('data-nombre');
                    const cantidad = parseInt(document.getElementById('product-quantity').value);

                    console.log(`Añadiendo al carrito: ${nombre} (${tipo} #${id}) - Cantidad: ${cantidad}`);

                    // Petición AJAX para añadir al carrito
                    fetch(`ajax_add_to_cart.php?action=add&tipo=${tipo}&id=${id}&cantidad=${cantidad}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error de red al añadir al carrito');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Mostrar notificación de éxito
                                showToast(`${nombre} (${cantidad} unidad${cantidad > 1 ? 'es' : ''}) se ha añadido correctamente a tu carrito`, false);
                                // Actualizar contador del carrito
                                if (data.cartCount) {
                                    updateCartBadge(data.cartCount);
                                } else {
                                    updateCartCounter();
                                }
                            } else {
                                // Mostrar notificación de error con el mensaje específico del servidor
                                showToast(data.message || 'Error al añadir el producto al carrito', true);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Error al añadir el producto al carrito', true);
                        });
                });
            }
        });

        // Función para mostrar notificación toast
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
    </script>
</body>

</html>