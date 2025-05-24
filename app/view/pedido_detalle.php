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

// Verificar si se proporciona un ID de pedido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}

// Incluir controlador de pedidos
require_once "../controller/PedidoController.php";

$pedidoController = new PedidoController();
$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$idPedido = (int)$_GET['id'];

// Verificar que el pedido pertenece al usuario actual
$pedido = $pedidoController->getUserOrderById($idUsuario, $idPedido);
if (!$pedido) {
    header("Location: pedidos.php");
    exit();
}

// Obtener detalles del pedido
$detalles = $pedidoController->getOrderDetails($idPedido);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pedido #<?php echo htmlspecialchars($pedido['numero_pedido']); ?> - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/pedidos.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pedido-detalle-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .pedido-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .pedido-info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .pedido-info-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2e294e;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }

        .pedido-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .pedido-info-label {
            font-weight: 500;
            color: #666;
        }

        .pedido-info-value {
            font-weight: 600;
            text-align: right;
        }

        .productos-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .productos-tabla th,
        .productos-tabla td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .productos-tabla th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2e294e;
        }

        .producto-tipo {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .tipo-juego {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .total-row td {
            border-top: 2px solid #2e294e;
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

        .btn-back {
            color: #2e294e;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-back i {
            margin-right: 5px;
        }

        .btn-back:hover {
            text-decoration: underline;
        }

        .pedido-timeline {
            margin: 30px 0;
            position: relative;
            padding-left: 30px;
        }

        .pedido-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #2e294e;
        }

        .timeline-item.active::before {
            background-color: #4CAF50;
        }

        .timeline-date {
            color: #6c757d;
            font-size: 0.85em;
        }

        .timeline-status {
            font-weight: 600;
            color: #2e294e;
        }

        .timeline-message {
            margin-top: 5px;
            color: #666;
        }

        @media (max-width: 768px) {
            .pedido-info-grid {
                grid-template-columns: 1fr;
            }
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
        <div class="pedido-detalle-container">
            <a href="pedidos.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver a mis pedidos
            </a>

            <div class="page-header">
                <h1>Detalle del Pedido #<?php echo htmlspecialchars($pedido['numero_pedido']); ?></h1>
                <p>Realizado el <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?> a las <?php echo date('H:i', strtotime($pedido['fecha'])); ?></p>
            </div>

            <div class="pedido-info-grid">
                <div class="pedido-info-card">
                    <h3>Información del Pedido</h3>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Número de Pedido:</div>
                        <div class="pedido-info-value"><?php echo htmlspecialchars($pedido['numero_pedido']); ?></div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Fecha:</div>
                        <div class="pedido-info-value"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Estado:</div>
                        <div class="pedido-info-value">
                            <span class="estado-badge estado-<?php echo $pedido['estado']; ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Total:</div>
                        <div class="pedido-info-value"><?php echo number_format($pedido['total'], 2); ?>€</div>
                    </div>
                </div>

                <div class="pedido-info-card">
                    <h3>Información de Envío</h3>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Nombre:</div>
                        <div class="pedido-info-value"><?php echo htmlspecialchars($nombreUsuario); ?></div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Dirección:</div>
                        <div class="pedido-info-value"><?php echo htmlspecialchars($pedido['direccion'] ?? 'No disponible'); ?></div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Método de envío:</div>
                        <div class="pedido-info-value">Envío estándar</div>
                    </div>
                    <div class="pedido-info-item">
                        <div class="pedido-info-label">Método de pago:</div>
                        <div class="pedido-info-value">Tarjeta de crédito</div>
                    </div>
                </div>
            </div>

            <h2>Productos en tu pedido</h2>
            <table class="productos-tabla">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detalles)): ?>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['nombre_producto'] ?? 'Producto #' . $detalle['id_producto']); ?></td>
                                <td>
                                    <span class="producto-tipo tipo-<?php echo $detalle['tipo_producto']; ?>">
                                        <?php echo ucfirst($detalle['tipo_producto']); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($detalle['precio'], 2); ?>€</td>
                                <td><?php echo $detalle['cantidad']; ?></td>
                                <td><?php echo number_format($detalle['precio'] * $detalle['cantidad'], 2); ?>€</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay detalles disponibles para este pedido.</td>
                        </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong><?php echo number_format($pedido['total'], 2); ?>€</strong></td>
                    </tr>
                </tbody>
            </table>

            <h2>Estado del pedido</h2>
            <div class="pedido-timeline">
                <?php
                // Mostrar línea de tiempo según el estado
                $estados = ['pendiente', 'procesando', 'enviado', 'entregado'];
                $estadoActual = array_search($pedido['estado'], $estados);

                if ($pedido['estado'] == 'cancelado') {
                    // Caso especial para pedidos cancelados
                ?>
                    <div class="timeline-item">
                        <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></div>
                        <div class="timeline-status">Pedido realizado</div>
                        <div class="timeline-message">Tu pedido ha sido recibido correctamente.</div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-date"><?php echo date('d/m/Y H:i', strtotime('+1 day', strtotime($pedido['fecha']))); ?></div>
                        <div class="timeline-status">Pedido cancelado</div>
                        <div class="timeline-message">Este pedido ha sido cancelado.</div>
                    </div>
                    <?php
                } else {
                    // Para pedidos en proceso normal
                    foreach ($estados as $index => $estado) {
                        $active = $index <= $estadoActual ? 'active' : '';
                        $fechaEstimada = strtotime('+' . ($index * 2) . ' days', strtotime($pedido['fecha']));
                    ?>
                        <div class="timeline-item <?php echo $active; ?>">
                            <div class="timeline-date"><?php echo date('d/m/Y', $fechaEstimada); ?></div>
                            <div class="timeline-status"><?php echo ucfirst($estado); ?></div>
                            <div class="timeline-message">
                                <?php
                                switch ($estado) {
                                    case 'pendiente':
                                        echo 'Tu pedido ha sido recibido y está pendiente de procesamiento.';
                                        break;
                                    case 'procesando':
                                        echo 'Estamos preparando tu pedido para el envío.';
                                        break;
                                    case 'enviado':
                                        echo 'Tu pedido ha sido enviado y está en camino.';
                                        break;
                                    case 'entregado':
                                        echo 'Tu pedido ha sido entregado con éxito.';
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
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