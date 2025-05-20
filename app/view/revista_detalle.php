<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

require_once "../controller/RevistasController.php";
require_once "../controller/CarritoController.php";

$revistasController = new RevistasController();
$carritoController = new CarritoController();

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: revistas.php');
    exit();
}

$idRevista = $_GET['id'];
$revista = $revistasController->getRevistaById($idRevista);

// Si no se encuentra la revista, redirigir
if (!$revista) {
    header('Location: revistas.php');
    exit();
}

// Procesar agregar al carrito
$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['agregar_carrito']) && isset($_POST['cantidad'])) {
    $cantidad = intval($_POST['cantidad']);

    // Validar cantidad
    if ($cantidad < 1) {
        $mensaje = 'La cantidad debe ser al menos 1';
        $tipoMensaje = 'error';
    } elseif ($cantidad > $revista['stock']) {
        $mensaje = 'No hay suficiente stock disponible';
        $tipoMensaje = 'error';
    } else {
        // Agregar al carrito
        $resultado = $carritoController->addToCart('revista', $idRevista, $cantidad);

        if ($resultado['success']) {
            $mensaje = 'Revista añadida al carrito correctamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'error';
        }
    }
}

// Obtener revistas relacionadas
$revistasRelacionadas = $revistasController->getRevistasRelacionadas($idRevista, $revista['editorial']);
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
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                <?php endif; ?>
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
                        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                            <a href="admin/dashboard.php"><i class="fas fa-user-shield"></i> Panel de Administración</a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                        <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito
                            <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                                <span class="cart-badge"><?php echo array_sum(array_column($_SESSION['carrito'], 'cantidad')); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <?php endif; ?>
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

            <?php if ($mensaje) : ?>
                <div id="notification" class="notification <?php echo $tipoMensaje; ?>">
                    <span><?php echo $mensaje; ?></span>
                    <button id="close-notification"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <div class="product-detail">
                <div class="product-media">
                    <div class="product-main-image" style="background-image: url('<?php echo htmlspecialchars($revista['imagen']); ?>')"></div>
                </div>

                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($revista['titulo']); ?></h1>

                    <div class="product-meta">
                        <span class="product-platform"><?php echo htmlspecialchars($revista['editorial']); ?></span>
                        <span class="product-separator">|</span>
                        <span class="product-year"><?php echo date('Y', strtotime($revista['fecha_publicacion'])); ?></span>
                    </div>

                    <div class="product-publisher">
                        <strong>Editorial:</strong> <?php echo htmlspecialchars($revista['editorial']); ?>
                    </div>

                    <div class="product-developer">
                        <strong>Fecha de publicación:</strong> <?php echo date('d/m/Y', strtotime($revista['fecha_publicacion'])); ?>
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
                        <?php if ($revista['stock'] > 0) : ?>
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
                        <?php else : ?>
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
                    <p><?php echo nl2br(htmlspecialchars($revista['descripcion'])); ?></p>
                </div>
            </div>

            <?php if (!empty($revistasRelacionadas)) : ?>
                <div class="suggested-products">
                    <h2>Revistas relacionadas</h2>
                    <div class="products-grid">
                        <?php foreach ($revistasRelacionadas as $relacionada) : ?>
                            <div class="product-card">
                                <div class="product-img" style="background-image: url('<?php echo htmlspecialchars($relacionada['imagen']); ?>');"></div>
                                <div class="product-platform"><?php echo htmlspecialchars($relacionada['editorial']); ?></div>
                                <h3><?php echo htmlspecialchars($relacionada['titulo']); ?></h3>
                                <p class="price"><?php echo number_format($relacionada['precio'], 2); ?>€</p>
                                <div class="product-actions">
                                    <a href="revista_detalle.php?id=<?php echo $relacionada['ID_Revista']; ?>" class="btn-secondary">Ver Detalles</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
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
            // Control de menú desplegable
            const userBtn = document.querySelector('.user-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            if (userBtn && dropdownContent) {
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
            }

            // Notificación
            const notification = document.getElementById('notification');
            if (notification) {
                const closeBtn = document.getElementById('close-notification');
                closeBtn.addEventListener('click', function() {
                    notification.style.display = 'none';
                });

                // Auto-cerrar después de 5 segundos
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 5000);
            }

            // Control de cantidad
            const quantityInput = document.getElementById('product-quantity');
            const decreaseBtn = document.getElementById('decrease-quantity');
            const increaseBtn = document.getElementById('increase-quantity');

            if (quantityInput && decreaseBtn && increaseBtn) {
                const maxStock = <?php echo $revista['stock']; ?>;

                decreaseBtn.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });

                increaseBtn.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue < maxStock) {
                        quantityInput.value = currentValue + 1;
                    }
                });

                // Validar input manual
                quantityInput.addEventListener('change', function() {
                    let currentValue = parseInt(this.value);
                    if (isNaN(currentValue) || currentValue < 1) {
                        this.value = 1;
                    } else if (currentValue > maxStock) {
                        this.value = maxStock;
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
                                // Mostrar notificación de error
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