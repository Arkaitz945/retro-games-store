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
require_once "../../controller/ConsolasController.php";
require_once "../../controller/CarritoController.php";

$consolasController = new ConsolasController();
$carritoController = new CarritoController();
$cantidadCarrito = isset($_SESSION['id']) ? $carritoController->countCartItems($_SESSION['id']) : 0;

// Obtener filtros de la URL
$filtros = [];
if (isset($_GET['fabricante'])) $filtros['fabricante'] = $_GET['fabricante'];
if (isset($_GET['estado'])) $filtros['estado'] = $_GET['estado'];
if (isset($_GET['precio_max']) && is_numeric($_GET['precio_max'])) $filtros['precio_max'] = $_GET['precio_max'];

// Obtener datos para los filtros
$fabricantes = $consolasController->getFabricantes();
$estados = $consolasController->getEstados();

// Obtener consolas según filtros
$consolas = $consolasController->getConsolas($filtros);

$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consolas - RetroGames Store</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/productos.css">
    <link rel="stylesheet" href="../css/consolas.css">
    <link rel="stylesheet" href="../css/notification.css">
    <link rel="stylesheet" href="../css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Añadir esta línea en el head para incluir el nuevo CSS -->
    <link rel="stylesheet" href="css/admin-modal.css">
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
                <li><a href="consolas.php" class="active">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
                <li><a href="accesorios.php">Accesorios</a></li>
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
                <h1>Consolas Retro</h1>
                <p>Explora nuestra colección de consolas clásicas de diferentes marcas y modelos.</p>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <h3>Filtrar por:</h3>
                <form action="consolas.php" method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="fabricante">Fabricante:</label>
                        <select name="fabricante" id="fabricante">
                            <option value="">Todos los fabricantes</option>
                            <?php foreach ($fabricantes as $fabricante): ?>
                                <option value="<?php echo $fabricante; ?>" <?php echo (isset($_GET['fabricante']) && $_GET['fabricante'] == $fabricante) ? 'selected' : ''; ?>>
                                    <?php echo $fabricante; ?>
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
                        <a href="consolas.php" class="btn-clear">Limpiar filtros</a>
                    </div>
                </form>
            </div>

            <!-- Resultados -->
            <div class="results-info">
                <p>Se encontraron <?php echo count($consolas); ?> productos</p>
            </div>

            <!-- Productos -->
            <div class="products-grid">
                <?php if (empty($consolas)): ?>
                    <div class="no-results">
                        <p>No se encontraron consolas con los filtros seleccionados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($consolas as $consola): ?>
                        <div class="product-card">
                            <div class="product-img" style="background-image: url('<?php echo htmlspecialchars($consola['imagen']); ?>');"></div>
                            <div class="product-fabricante"><?php echo htmlspecialchars($consola['fabricante']); ?></div>
                            <h3><?php echo htmlspecialchars($consola['nombre']); ?></h3>
                            <div class="product-details">
                                <span class="product-estado"><?php echo htmlspecialchars($consola['estado']); ?></span>
                                <span class="product-year"><?php echo htmlspecialchars($consola['año_lanzamiento']); ?></span>
                            </div>
                            <p class="price"><?php echo number_format($consola['precio'], 2); ?>€</p>
                            <div class="product-actions">
                                <a href="consola_detalle.php?id=<?php echo $consola['ID_Consola']; ?>" class="btn-secondary">Ver Detalles</a>
                                <?php if ($consola['stock'] > 0): ?>
                                    <button type="button" class="btn-add-cart"
                                        data-id="<?php echo $consola['ID_Consola']; ?>"
                                        data-tipo="consola"
                                        data-nombre="<?php echo htmlspecialchars($consola['nombre']); ?>">
                                        <i class="fas fa-cart-plus"></i> Añadir
                                    </button>
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
        document.addEventListener('DOMContentLoaded', function() {
            // JavaScript para el menú desplegable
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

            // Manejar los botones de añadir al carrito
            document.querySelectorAll('.btn-add-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Obtener información del producto
                    const productId = this.getAttribute('data-id');
                    const productType = this.getAttribute('data-tipo') || 'consola';
                    const productName = this.getAttribute('data-nombre');

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

    <!-- Reemplazar el modal de confirmación existente por este nuevo HTML -->
    <div id="deleteModalOverlay" class="modal-overlay" style="display: none;">
        <div class="confirm-dialog">
            <h3>Confirmar eliminación</h3>
            <p>¿Estás seguro de que deseas eliminar esta consola? Esta acción no se puede deshacer.</p>
            <div class="confirm-buttons">
                <button class="btn-cancel" onclick="cancelDelete()">Cancelar</button>
                <button class="btn-delete" onclick="confirmDelete()">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        // Variables para la eliminación
        let consolaIdToDelete = null;

        // Función para mostrar la confirmación de eliminación
        function showDeleteConfirmation(id) {
            consolaIdToDelete = id;
            document.getElementById('deleteModalOverlay').style.display = 'flex';
        }

        // Función para cancelar la eliminación
        function cancelDelete() {
            consolaIdToDelete = null;
            document.getElementById('deleteModalOverlay').style.display = 'none';
        }

        // Función para confirmar la eliminación
        function confirmDelete() {
            if (consolaIdToDelete) {
                window.location.href = 'eliminar_consola.php?id=' + consolaIdToDelete;
            }
        }

        // Si haces clic fuera del diálogo, también cancela
        document.getElementById('deleteModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                cancelDelete();
            }
        });
    </script>
</body>

</html>