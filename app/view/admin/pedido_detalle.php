<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../index.php");
    exit();
}

require_once "../../controller/admin/PedidosAdminController.php";

$pedidosController = new PedidosAdminController();
$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';

// Verificar si se proporciona un ID
if (!isset($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}

$idPedido = $_GET['id'];
$pedido = $pedidosController->getPedidoById($idPedido);
$detalles = $pedidosController->getDetallesPedido($idPedido);

if (!$pedido) {
    header("Location: pedidos.php?mensaje=Pedido no encontrado&tipo=error");
    exit();
}

// Actualizar estado del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_estado'])) {
    $resultado = $pedidosController->updateEstadoPedido($idPedido, $_POST['nuevo_estado']);
    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipoMensaje = 'success';
        // Actualizar datos del pedido
        $pedido = $pedidosController->getPedidoById($idPedido);
    } else {
        $mensaje = $resultado['message'];
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pedido #<?php echo $idPedido; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .detail-card h3 {
            margin-top: 0;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #2e294e;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
        }

        .detail-value {
            font-weight: 600;
        }

        .estado-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }

        .estado-form select {
            flex: 1;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .btn-update-status {
            background-color: #2e294e;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-update-status:hover {
            background-color: #3d366a;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th,
        .products-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .product-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .type-juego {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .type-consola {
            background-color: #cfe2ff;
            color: #084298;
        }

        .type-revista {
            background-color: #fff3cd;
            color: #664d03;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .total-row td {
            border-top: 2px solid #2e294e;
        }

        .badge {
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

        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <a href="../home.php" class="logo-link">
                <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="../home.php">Tienda</a></li>
                <li><a href="dashboard.php">Admin Panel</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="pedidos.php" class="active">Pedidos</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-dropdown">
                <button class="user-btn"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($nombreUsuario); ?> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="../home.php"><i class="fas fa-store"></i> Ver Tienda</a>
                    <div class="dropdown-divider"></div>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="admin-container">
            <div class="admin-header">
                <div class="admin-header-left">
                    <h1>Detalle de Pedido #<?php echo $idPedido; ?></h1>
                    <a href="pedidos.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a pedidos
                    </a>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <div class="order-details">
                    <div class="detail-card">
                        <h3>Información del Pedido</h3>
                        <div class="detail-row">
                            <span class="detail-label">Número de Pedido:</span>
                            <span class="detail-value">#<?php echo $pedido['id']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Fecha:</span>
                            <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total:</span>
                            <span class="detail-value"><?php echo number_format($pedido['total'], 2); ?>€</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Estado:</span>
                            <span class="detail-value">
                                <span class="badge estado-<?php echo $pedido['estado']; ?>">
                                    <?php echo ucfirst($pedido['estado']); ?>
                                </span>
                            </span>
                        </div>

                        <!-- Formulario para cambiar el estado -->
                        <form method="post" class="estado-form">
                            <select name="nuevo_estado" id="nuevo_estado">
                                <option value="pendiente" <?php echo ($pedido['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="procesando" <?php echo ($pedido['estado'] == 'procesando') ? 'selected' : ''; ?>>Procesando</option>
                                <option value="enviado" <?php echo ($pedido['estado'] == 'enviado') ? 'selected' : ''; ?>>Enviado</option>
                                <option value="entregado" <?php echo ($pedido['estado'] == 'entregado') ? 'selected' : ''; ?>>Entregado</option>
                                <option value="cancelado" <?php echo ($pedido['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn-update-status">Actualizar Estado</button>
                        </form>
                    </div>

                    <div class="detail-card">
                        <h3>Información del Cliente</h3>
                        <div class="detail-row">
                            <span class="detail-label">Nombre:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellidos']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pedido['email']); ?></span>
                        </div>
                        <?php if (!empty($pedido['telefono'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Teléfono:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($pedido['telefono']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="detail-label">Dirección:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pedido['direccion'] ?: 'No disponible'); ?></span>
                        </div>
                    </div>
                </div>

                <h3>Productos</h3>
                <table class="products-table">
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
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                <td>
                                    <span class="product-type type-<?php echo $detalle['tipo_producto']; ?>">
                                        <?php echo ucfirst($detalle['tipo_producto']); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($detalle['precio'], 2); ?>€</td>
                                <td><?php echo $detalle['cantidad']; ?></td>
                                <td><?php echo number_format($detalle['precio'] * $detalle['cantidad'], 2); ?>€</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total:</td>
                            <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Panel de Administración</p>
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

            // Auto-ocultar mensajes después de 5 segundos
            const alertMessage = document.querySelector('.alert');
            if (alertMessage) {
                setTimeout(() => {
                    alertMessage.style.opacity = '0';
                    setTimeout(() => {
                        alertMessage.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>