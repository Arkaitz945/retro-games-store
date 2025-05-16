<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

require_once "../controller/ConsolasController.php";
require_once "../controller/CarritoController.php";

$consolasController = new ConsolasController();
$carritoController = new CarritoController();

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: consolas.php');
    exit();
}

$idConsola = $_GET['id'];
$consola = $consolasController->getConsolaById($idConsola);

// Si no se encuentra la consola, redirigir
if (!$consola) {
    header('Location: consolas.php');
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
    } elseif ($cantidad > $consola['stock']) {
        $mensaje = 'No hay suficiente stock disponible';
        $tipoMensaje = 'error';
    } else {
        // Agregar al carrito
        $resultado = $carritoController->agregarProducto('consola', $idConsola, $cantidad);

        if ($resultado['success']) {
            $mensaje = 'Consola añadida al carrito correctamente';
            $tipoMensaje = 'success';
        } else {
            $mensaje = $resultado['message'];
            $tipoMensaje = 'error';
        }
    }
}

// Obtener consolas relacionadas
$consolasRelacionadas = $consolasController->getConsolasRelacionadas($idConsola, $consola['fabricante']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($consola['nombre']); ?> - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/consolas.css">
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Banner superior (inline header) -->
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
                <li><a href="consolas.php" class="active">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
                <li><a href="accesorios.php">Accesorios</a></li>
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
            <div class="breadcrumb">
                <a href="home.php">Inicio</a> &gt;
                <a href="consolas.php">Consolas</a> &gt;
                <span><?php echo htmlspecialchars($consola['nombre']); ?></span>
            </div>

            <!-- Notificación -->
            <?php if ($mensaje) : ?>
                <div id="notification" class="notification <?php echo $tipoMensaje; ?>">
                    <span><?php echo $mensaje; ?></span>
                    <button id="close-notification"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>

            <div class="product-detail-container">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($consola['imagen']); ?>" alt="<?php echo htmlspecialchars($consola['nombre']); ?>">
                </div>

                <div class="product-info">
                    <h1><?php echo htmlspecialchars($consola['nombre']); ?></h1>

                    <div class="product-meta">
                        <span><i class="fas fa-industry"></i> <?php echo htmlspecialchars($consola['fabricante']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($consola['año_lanzamiento']); ?></span>
                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($consola['estado']); ?></span>
                    </div>

                    <div class="price-container">
                        <span class="price"><?php echo number_format($consola['precio'], 2); ?>€</span>
                    </div>

                    <div class="stock-status">
                        <span class="<?php echo $consola['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo $consola['stock'] > 0 ? '<i class="fas fa-check-circle"></i> En stock' : '<i class="fas fa-times-circle"></i> Agotado'; ?>
                            <?php if ($consola['stock'] > 0) : ?>
                                (<?php echo $consola['stock']; ?> disponibles)
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if ($consola['stock'] > 0) : ?>
                        <form action="" method="post" class="add-to-cart-form">
                            <div class="quantity-input">
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" name="cantidad" id="cantidad" value="1" min="1" max="<?php echo $consola['stock']; ?>" required>
                            </div>
                            <button type="submit" name="agregar_carrito" class="btn-add-to-cart">
                                <i class="fas fa-cart-plus"></i> Añadir al carrito
                            </button>
                        </form>
                    <?php else : ?>
                        <button disabled class="btn-add-to-cart disabled">
                            <i class="fas fa-cart-plus"></i> Agotado
                        </button>
                    <?php endif; ?>

                    <div class="product-description">
                        <h2>Descripción</h2>
                        <p><?php echo nl2br(htmlspecialchars($consola['descripcion'])); ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($consolasRelacionadas)) : ?>
                <section class="related-products">
                    <h2>Consolas relacionadas</h2>
                    <div class="related-grid">
                        <?php foreach ($consolasRelacionadas as $relacionada) : ?>
                            <div class="product-card">
                                <div class="product-img" style="background-image: url('<?php echo htmlspecialchars($relacionada['imagen']); ?>');">
                                    <div class="product-badge">
                                        <?php echo htmlspecialchars($relacionada['estado']); ?>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($relacionada['nombre']); ?></h3>
                                    <p class="product-manufacturer"><?php echo htmlspecialchars($relacionada['fabricante']); ?> - <?php echo htmlspecialchars($relacionada['año_lanzamiento']); ?></p>
                                    <p class="price"><?php echo number_format($relacionada['precio'], 2); ?>€</p>
                                    <div class="product-actions">
                                        <a href="consola_detalle.php?id=<?php echo $relacionada['ID_Consola']; ?>" class="btn-secondary">Ver Detalles</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Notificación del carrito -->
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
            // JavaScript para el menú desplegable del usuario
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
            const cantidadInput = document.getElementById('cantidad');
            if (cantidadInput) {
                cantidadInput.addEventListener('change', function() {
                    const maxStock = <?php echo $consola['stock']; ?>;
                    if (this.value < 1) {
                        this.value = 1;
                    } else if (this.value > maxStock) {
                        this.value = maxStock;
                    }
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
    </script>
</body>

</html>