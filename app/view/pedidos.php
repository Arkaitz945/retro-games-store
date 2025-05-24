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

// Incluir controlador de pedidos
require_once "../controller/PedidoController.php";

$pedidoController = new PedidoController();
$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Obtener los pedidos del usuario
$pedidos = $pedidoController->getUserOrders($idUsuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/pedidos.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pedidos-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .pedido-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .pedido-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pedido-numero {
            font-weight: 600;
            font-size: 1.1em;
            color: #2e294e;
        }

        .pedido-fecha {
            color: #6c757d;
            font-size: 0.9em;
        }

        .pedido-body {
            padding: 20px;
        }

        .pedido-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .pedido-info-item {
            display: flex;
            flex-direction: column;
        }

        .pedido-info-label {
            font-size: 0.85em;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .pedido-info-value {
            font-weight: 600;
            color: #333;
        }

        .pedido-productos {
            margin-top: 20px;
        }

        .pedido-productos-titulo {
            font-weight: 600;
            margin-bottom: 15px;
            color: #2e294e;
        }

        .producto-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .producto-info {
            flex: 1;
        }

        .producto-nombre {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .producto-cantidad {
            font-size: 0.9em;
            color: #6c757d;
        }

        .producto-precio {
            font-weight: 600;
            text-align: right;
            min-width: 80px;
        }

        .pedido-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .pedido-total-label {
            font-weight: 600;
            margin-right: 15px;
        }

        .pedido-total-value {
            font-weight: 700;
            font-size: 1.2em;
            color: #2e294e;
        }

        .pedido-acciones {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-pedido-detalle {
            background-color: #2e294e;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-pedido-detalle i {
            margin-right: 5px;
        }

        .btn-pedido-detalle:hover {
            background-color: #3d366a;
        }

        .estado-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .estado-pendiente {
            background-color: #fff3cd;
            color: #664d03;
        }

        .estado-procesando {
            background-color: #cfe2ff;
            color: #084298;
        }

        .estado-enviado {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .estado-entregado {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .estado-cancelado {
            background-color: #f8d7da;
            color: #842029;
        }

        .no-pedidos {
            text-align: center;
            padding: 50px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 30px;
        }

        .no-pedidos i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .no-pedidos h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #2e294e;
        }

        .no-pedidos p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .btn-comprar {
            background-color: #2e294e;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }

        .btn-comprar:hover {
            background-color: #3d366a;
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
                    <a href="pedidos.php" class="active"><i class="fas fa-box"></i> Mis Pedidos</a>
                    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
                    <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="pedidos-container">
            <div class="page-header">
                <h1>Mis Pedidos</h1>
                <p>Historial de tus compras en RetroGames Store</p>
            </div>

            <?php if (empty($pedidos)): ?>
                <div class="no-pedidos">
                    <i class="fas fa-box-open"></i>
                    <h2>No tienes pedidos todavía</h2>
                    <p>Parece que aún no has realizado ninguna compra en nuestra tienda.</p>
                    <p>¡Explora nuestro catálogo y encuentra los mejores videojuegos y consolas retro!</p>
                    <a href="videojuegos.php" class="btn-comprar">Explorar productos</a>
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <div class="pedido-numero">Pedido #<?php echo htmlspecialchars($pedido['numero_pedido']); ?></div>
                            <div class="pedido-fecha"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></div>
                        </div>
                        <div class="pedido-body">
                            <div class="pedido-info">
                                <div class="pedido-info-item">
                                    <div class="pedido-info-label">Estado</div>
                                    <div class="pedido-info-value">
                                        <span class="estado-badge estado-<?php echo $pedido['estado']; ?>">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="pedido-info-item">
                                    <div class="pedido-info-label">Número de productos</div>
                                    <div class="pedido-info-value"><?php echo $pedido['num_productos']; ?></div>
                                </div>
                                <div class="pedido-info-item">
                                    <div class="pedido-info-label">Total</div>
                                    <div class="pedido-info-value"><?php echo number_format($pedido['total'], 2); ?>€</div>
                                </div>
                            </div>

                            <?php
                            // Obtener los detalles del pedido si están disponibles
                            $detalles = $pedidoController->getOrderDetails($pedido['ID_Pedido']);
                            if (!empty($detalles)):
                            ?>
                                <div class="pedido-productos">
                                    <div class="pedido-productos-titulo">Productos</div>
                                    <?php foreach ($detalles as $detalle): ?>
                                        <div class="producto-item">
                                            <div class="producto-info">
                                                <div class="producto-nombre"><?php echo htmlspecialchars($detalle['nombre_producto'] ?? 'Producto #' . $detalle['id_producto']); ?></div>
                                                <div class="producto-cantidad">Cantidad: <?php echo $detalle['cantidad']; ?></div>
                                            </div>
                                            <div class="producto-precio"><?php echo number_format($detalle['precio'] * $detalle['cantidad'], 2); ?>€</div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="pedido-total">
                                <div class="pedido-total-label">Total pagado:</div>
                                <div class="pedido-total-value"><?php echo number_format($pedido['total'], 2); ?>€</div>
                            </div>

                            <div class="pedido-acciones">
                                <a href="pedido_detalle.php?id=<?php echo $pedido['ID_Pedido']; ?>" class="btn-pedido-detalle">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Tu tienda especializada en videojuegos y consolas retro.</p>
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
            // Menú desplegable
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
        });
    </script>
</body>

</html>