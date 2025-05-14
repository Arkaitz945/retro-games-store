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

// Obtener filtros
$filtros = [];
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $filtros['estado'] = $_GET['estado'];
}
if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
    $filtros['fecha_desde'] = $_GET['fecha_desde'];
}
if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
    $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
}

$pedidos = $pedidosController->getAllPedidos($filtros);
$estados = $pedidosController->getEstadosPedidos();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';

// Recibir mensaje de la URL
if (isset($_GET['mensaje']) && isset($_GET['tipo'])) {
    $mensaje = $_GET['mensaje'];
    $tipoMensaje = $_GET['tipo'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Admin Panel</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <h1>Gestión de Pedidos</h1>
                    <a href="dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Panel
                    </a>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <div class="search-filter">
                    <form action="" method="get" class="filter-form">
                        <div class="filter-group">
                            <label for="estado">Estado:</label>
                            <select id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado; ?>" <?php echo (isset($filtros['estado']) && $filtros['estado'] == $estado) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($estado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="fecha_desde">Desde:</label>
                            <input type="date" id="fecha_desde" name="fecha_desde" value="<?php echo $filtros['fecha_desde'] ?? ''; ?>">
                        </div>

                        <div class="filter-group">
                            <label for="fecha_hasta">Hasta:</label>
                            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?php echo $filtros['fecha_hasta'] ?? ''; ?>">
                        </div>

                        <div class="filter-buttons">
                            <button type="submit" class="btn-filter">Filtrar</button>
                            <a href="pedidos.php" class="btn-clear">Limpiar</a>
                        </div>
                    </form>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="6" class="no-results">No hay pedidos disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><?php echo $pedido['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellidos']); ?> (<?php echo htmlspecialchars($pedido['email']); ?>)</td>
                                    <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                                    <td>
                                        <span class="badge estado-<?php echo $pedido['estado']; ?>">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="pedido_detalle.php?id=<?php echo $pedido['id']; ?>" class="btn-view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
        // JavaScript para el menú desplegable y funcionalidad
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

            // Validación de fechas
            const fechaDesde = document.getElementById('fecha_desde');
            const fechaHasta = document.getElementById('fecha_hasta');

            if (fechaDesde && fechaHasta) {
                // Validar que fecha hasta no sea menor que fecha desde
                fechaHasta.addEventListener('change', function() {
                    if (fechaDesde.value && this.value && this.value < fechaDesde.value) {
                        alert('La fecha "hasta" no puede ser anterior a la fecha "desde"');
                        this.value = fechaDesde.value;
                    }
                });

                fechaDesde.addEventListener('change', function() {
                    if (fechaHasta.value && this.value && fechaHasta.value < this.value) {
                        fechaHasta.value = this.value;
                    }
                });
            }

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