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

// Incluir controlador del carrito
require_once "../controller/CarritoController.php";

$carritoController = new CarritoController();
$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Procesar acciones del carrito
$mensaje = null;
$tipoMensaje = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar cantidades
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['cantidad'] as $cartItemId => $cantidad) {
            if ($cantidad <= 0) {
                // Si la cantidad es 0 o negativa, eliminar el item
                $resultado = $carritoController->removeFromCart($idUsuario, $cartItemId);
            } else {
                // Actualizar cantidad
                $resultado = $carritoController->updateCartItemQuantity($idUsuario, $cartItemId, $cantidad);
            }

            if (!$resultado['success']) {
                $mensaje = $resultado['message'];
                $tipoMensaje = 'error';
                break;
            }
        }

        if (!isset($mensaje)) {
            $mensaje = 'Carrito actualizado correctamente';
            $tipoMensaje = 'success';
        }
    }

    // Vaciar carrito
    if (isset($_POST['clear_cart'])) {
        $resultado = $carritoController->clearCart($idUsuario);

        if ($resultado['success']) {
            $mensaje = 'Carrito vaciado correctamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'error';
        }
    }
}

// Procesar acciones GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Añadir al carrito
    if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
        $productoId = (int)$_GET['id'];
        $tipoProducto = isset($_GET['tipo']) ? $_GET['tipo'] : 'juego'; // Por defecto es juego
        $cantidad = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 1;

        $resultado = $carritoController->addToCart($idUsuario, $tipoProducto, $productoId, $cantidad);

        // Si es una petición AJAX, devolver respuesta JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($resultado);
            exit;
        }

        // Para peticiones normales, seguir con el procesamiento habitual
        if ($resultado['success']) {
            $mensaje = 'Producto añadido al carrito correctamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'error';
        }
    }

    // Eliminar del carrito
    if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
        $cartItemId = (int)$_GET['id'];

        $resultado = $carritoController->removeFromCart($idUsuario, $cartItemId);

        if ($resultado['success']) {
            $mensaje = 'Producto eliminado del carrito correctamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'error';
        }
    }
}

// Obtener items del carrito
$cartItems = $carritoController->getCart($idUsuario);
$cartTotal = $carritoController->getCartTotal($idUsuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compra - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/carrito.css">
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
                    <a href="carrito.php" class="active"><i class="fas fa-shopping-cart"></i> Carrito</a>
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
                <h1>Tu Carrito de Compra</h1>
                <p>Revisa los productos que has seleccionado para comprar</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Tu carrito está vacío</h2>
                    <p>Parece que aún no has añadido ningún producto a tu carrito.</p>
                    <a href="videojuegos.php" class="btn-primary">Ver Videojuegos</a>
                </div>
            <?php else: ?>
                <form action="carrito.php" method="post" class="cart-form">
                    <div class="cart-header">
                        <div class="cart-row">
                            <div class="cart-col product-info">Producto</div>
                            <div class="cart-col product-price">Precio</div>
                            <div class="cart-col product-quantity">Cantidad</div>
                            <div class="cart-col product-subtotal">Subtotal</div>
                            <div class="cart-col product-actions">Acciones</div>
                        </div>
                    </div>

                    <div class="cart-body">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-row">
                                <div class="cart-col product-info">
                                    <div class="product-image" style="background-image: url('<?php echo htmlspecialchars($item['imagen']); ?>')"></div>
                                    <div class="product-details">
                                        <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                        <p class="product-platform"><?php echo htmlspecialchars($item['plataforma']); ?></p>
                                    </div>
                                </div>
                                <div class="cart-col product-price">
                                    <?php echo number_format($item['precio'], 2); ?>€
                                </div>
                                <div class="cart-col product-quantity">
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn minus" data-id="<?php echo $item['ID_Carrito']; ?>">-</button>
                                        <input type="number" name="cantidad[<?php echo $item['ID_Carrito']; ?>]" value="<?php echo $item['cantidad']; ?>" min="0" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                        <button type="button" class="quantity-btn plus" data-id="<?php echo $item['ID_Carrito']; ?>" data-max="<?php echo $item['stock']; ?>">+</button>
                                    </div>
                                </div>
                                <div class="cart-col product-subtotal">
                                    <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>€
                                </div>
                                <div class="cart-col product-actions">
                                    <a href="carrito.php?action=remove&id=<?php echo $item['ID_Carrito']; ?>" class="btn-remove" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-footer">
                        <div class="cart-actions">
                            <a href="videojuegos.php" class="btn-continue-shopping">
                                <i class="fas fa-arrow-left"></i> Seguir comprando
                            </a>
                            <button type="submit" name="update_cart" class="btn-update-cart">
                                <i class="fas fa-sync-alt"></i> Actualizar carrito
                            </button>
                            <button type="submit" name="clear_cart" class="btn-clear-cart">
                                <i class="fas fa-trash"></i> Vaciar carrito
                            </button>
                        </div>

                        <div class="cart-totals">
                            <div class="totals-row">
                                <div class="totals-label">Subtotal:</div>
                                <div class="totals-value"><?php echo number_format($cartTotal, 2); ?>€</div>
                            </div>
                            <div class="totals-row">
                                <div class="totals-label">Envío:</div>
                                <div class="totals-value">Calculado en el checkout</div>
                            </div>
                            <div class="totals-row total-row">
                                <div class="totals-label">Total:</div>
                                <div class="totals-value total-value"><?php echo number_format($cartTotal, 2); ?>€</div>
                            </div>
                            <a href="checkout.php" class="btn-checkout">
                                Proceder al pago <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
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

            // Control de cantidad
            const minusBtns = document.querySelectorAll('.quantity-btn.minus');
            const plusBtns = document.querySelectorAll('.quantity-btn.plus');

            minusBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const input = document.querySelector(`input[name="cantidad[${id}]"]`);
                    let value = parseInt(input.value);
                    if (value > 0) {
                        input.value = value - 1;
                        updateSubtotal(id);
                    }
                });
            });

            plusBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const max = parseInt(this.getAttribute('data-max'));
                    const input = document.querySelector(`input[name="cantidad[${id}]"]`);
                    let value = parseInt(input.value);
                    if (value < max) {
                        input.value = value + 1;
                        updateSubtotal(id);
                    }
                });
            });

            const quantityInputs = document.querySelectorAll('.quantity-input');
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const id = this.name.match(/\[(\d+)\]/)[1];
                    updateSubtotal(id);
                });
            });

            function updateSubtotal(id) {
                const row = document.querySelector(`input[name="cantidad[${id}]"]`).closest('.cart-row');
                const price = parseFloat(row.querySelector('.product-price').textContent.replace('€', '').trim());
                const quantity = parseInt(row.querySelector('.quantity-input').value);
                const subtotal = price * quantity;
                row.querySelector('.product-subtotal').textContent = subtotal.toFixed(2) + '€';

                // Actualizar total
                updateTotal();
            }

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.product-subtotal').forEach(el => {
                    total += parseFloat(el.textContent.replace('€', '').trim());
                });
                document.querySelector('.totals-value:first-of-type').textContent = total.toFixed(2) + '€';
                document.querySelector('.total-value').textContent = total.toFixed(2) + '€';
            }
        });
    </script>
</body>

</html>