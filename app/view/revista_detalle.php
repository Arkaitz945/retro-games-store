<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

require_once "../controller/RevistasController.php";
require_once "../controller/CarritoController.php";

// Define an absolute path to the view directory
$viewPath = dirname(__FILE__);

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
        $resultado = $carritoController->agregarProducto('revista', $idRevista, $cantidad);

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
    <link rel="stylesheet" href="css/producto_detalle.css">
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Banner superior -->
    <?php require_once $viewPath . '/header.php'; ?>

    <!-- Contenido principal -->
    <main>
        <div class="container">
            <div class="breadcrumb">
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
                <div class="product-images">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($revista['imagen']); ?>" alt="<?php echo htmlspecialchars($revista['titulo']); ?>">
                    </div>
                </div>

                <div class="product-info">
                    <h1><?php echo htmlspecialchars($revista['titulo']); ?></h1>

                    <div class="product-meta">
                        <span class="badge editorial"><?php echo htmlspecialchars($revista['editorial']); ?></span>
                        <span class="badge fecha"><?php echo date('d/m/Y', strtotime($revista['fecha_publicacion'])); ?></span>
                    </div>

                    <div class="product-price">
                        <span class="current-price"><?php echo number_format($revista['precio'], 2); ?>€</span>
                    </div>

                    <div class="product-status">
                        <span class="availability <?php echo $revista['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo $revista['stock'] > 0 ? 'En stock' : 'Agotado'; ?>
                            <?php if ($revista['stock'] > 0) : ?>
                                (<?php echo $revista['stock']; ?> disponibles)
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if ($revista['stock'] > 0) : ?>
                        <form action="" method="post" class="cart-form">
                            <div class="quantity-input">
                                <button type="button" class="quantity-btn minus">-</button>
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $revista['stock']; ?>" required>
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                            <button type="submit" name="agregar_carrito" class="btn-add-cart">
                                <i class="fas fa-cart-plus"></i> Añadir al carrito
                            </button>
                        </form>
                    <?php else : ?>
                        <button disabled class="btn-add-cart disabled">
                            <i class="fas fa-cart-plus"></i> Agotado
                        </button>
                    <?php endif; ?>

                    <div class="product-description">
                        <h3>Descripción</h3>
                        <p><?php echo nl2br(htmlspecialchars($revista['descripcion'])); ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($revistasRelacionadas)) : ?>
                <section class="related-products">
                    <h2>Revistas relacionadas</h2>
                    <div class="related-grid">
                        <?php foreach ($revistasRelacionadas as $relacionada) : ?>
                            <div class="product-card">
                                <div class="product-img" style="background-image: url('<?php echo htmlspecialchars($relacionada['imagen']); ?>');">
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($relacionada['titulo']); ?></h3>
                                    <p class="product-manufacturer">
                                        <?php echo htmlspecialchars($relacionada['editorial']); ?> -
                                        <?php echo date('d/m/Y', strtotime($relacionada['fecha_publicacion'])); ?>
                                    </p>
                                    <div class="product-footer">
                                        <span class="price"><?php echo number_format($relacionada['precio'], 2); ?>€</span>
                                        <a href="revista_detalle.php?id=<?php echo $relacionada['ID_Revista']; ?>" class="btn-view">Ver detalles</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php require_once $viewPath . '/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            const minusBtn = document.querySelector('.quantity-btn.minus');
            const plusBtn = document.querySelector('.quantity-btn.plus');
            const quantityInput = document.querySelector('.quantity-input input');

            if (minusBtn && plusBtn && quantityInput) {
                const maxStock = <?php echo $revista['stock']; ?>;

                minusBtn.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });

                plusBtn.addEventListener('click', function() {
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
        });
    </script>
</body>

</html>