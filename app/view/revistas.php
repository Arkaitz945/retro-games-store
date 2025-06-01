<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

require_once "../controller/RevistasController.php";

$revistasController = new RevistasController();

// Obtener filtros de la URL
$filtros = [];
if (isset($_GET['editorial']) && !empty($_GET['editorial'])) {
    $filtros['editorial'] = $_GET['editorial'];
}
if (isset($_GET['precio_max']) && is_numeric($_GET['precio_max'])) {
    $filtros['precio_max'] = $_GET['precio_max'];
}

// Obtener revistas con los filtros aplicados
$revistas = $revistasController->getRevistas($filtros);

// Obtener opciones para filtros
$editoriales = $revistasController->getEditoriales();

// Calcular precio máximo para el slider
$precioMaximo = 0;
foreach ($revistas as $revista) {
    if ($revista['precio'] > $precioMaximo) {
        $precioMaximo = $revista['precio'];
    }
}
// Redondear al alza al siguiente centenar para el rango del slider
$precioMaximo = ceil($precioMaximo / 100) * 100;

// Obtener valor del filtro de precio máximo (para el slider)
$filtroPrecioMax = isset($_GET['precio_max']) ? $_GET['precio_max'] : $precioMaximo;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revistas - RetroGames Store</title>
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
            <div class="page-header">
                <h1>Revistas Retro</h1>
                <p>Explora nuestra colección de revistas clásicas de diferentes épocas y editoriales.</p>
            </div>

            <!-- Filtros -->
            <div class="filters-container">
                <h3>Filtrar por:</h3>
                <form action="revistas.php" method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="editorial">Editorial:</label>
                        <select name="editorial" id="editorial">
                            <option value="">Todas las editoriales</option>
                            <?php foreach ($editoriales as $editorial): ?>
                                <option value="<?php echo htmlspecialchars($editorial); ?>" <?php echo (isset($_GET['editorial']) && $_GET['editorial'] == $editorial) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($editorial); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="precio_max">Precio máximo:</label>
                        <input type="number" name="precio_max" id="precio_max" min="0" step="1" value="<?php echo isset($_GET['precio_max']) ? htmlspecialchars($_GET['precio_max']) : $precioMaximo; ?>">
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn-filter">Aplicar filtros</button>
                        <a href="revistas.php" class="btn-clear">Limpiar filtros</a>
                    </div>
                </form>
            </div>

            <!-- Info de resultados -->
            <div class="results-info">
                <p>Se encontraron <?php echo count($revistas); ?> revistas</p>
            </div>

            <!-- Productos -->
            <div class="products-grid">
                <?php if (empty($revistas)): ?>
                    <div class="no-results">
                        <p>No se encontraron revistas con los filtros seleccionados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($revistas as $revista): ?>
                        <div class="product-card">
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

                            // Usar el campo título en lugar de nombre para revistas
                            $titulo = isset($revista['titulo']) ? htmlspecialchars($revista['titulo']) : 'Sin título';
                            $id = $revista['ID_Revista'];
                            $precio = isset($revista['precio']) ? number_format($revista['precio'], 2) : '0.00';
                            $stock = isset($revista['stock']) ? (int)$revista['stock'] : 0;
                            ?>
                            <div class="product-img" style="background-image: url('<?php echo $imagen; ?>');"></div>
                            <div class="product-info">
                                <div>
                                    <h3 class="product-title"><?php echo $titulo; ?></h3>
                                    <div class="product-details">
                                        <span class="product-editorial"><?php echo htmlspecialchars($revista['editorial'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="product-price"><?php echo $precio; ?>€</p>
                                    <div class="product-buttons">
                                        <a href="revista_detalle.php?id=<?php echo $id; ?>" class="btn btn-primary">Ver Detalles</a>
                                        <?php if ($stock > 0): ?>
                                            <button
                                                class="btn btn-add-cart"
                                                data-id="<?php echo $id; ?>"
                                                data-tipo="revista"
                                                data-nombre="<?php echo $titulo; ?>">
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

    <!-- Footer (inline footer) -->
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

            // Manejar los botones de añadir al carrito
            document.querySelectorAll('.btn-add-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Obtener información del producto
                    const productId = this.getAttribute('data-id');
                    const productType = this.getAttribute('data-tipo') || 'revista';
                    const productName = this.getAttribute('data-nombre');

                    console.log(`Añadiendo al carrito: ${productName} (${productType} #${productId})`);

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