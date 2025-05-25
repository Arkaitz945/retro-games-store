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
    <style>
        /* Estilos mejorados para filtros */
        .search-filter {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .filter-group select,
        .filter-group input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-group select:focus,
        .filter-group input[type="date"]:focus {
            border-color: #2e294e;
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 41, 78, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-left: auto;
            align-self: flex-end;
        }

        .btn-filter {
            background-color: #2e294e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-filter:hover {
            background-color: #3d366a;
        }

        .btn-clear {
            background-color: #f8f9fa;
            color: #444;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-clear:hover {
            background-color: #e9ecef;
            border-color: #bbb;
        }

        .filter-title {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #2e294e;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-title i {
            font-size: 14px;
        }

        /* Estilos para la tabla */
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .admin-table th {
            background-color: #2e294e;
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 500;
        }

        .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .admin-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .admin-table tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .estado-procesando {
            background-color: #cce5ff;
            color: #004085;
        }

        .estado-enviado {
            background-color: #d4edda;
            color: #155724;
        }

        .estado-entregado {
            background-color: #c3e6cb;
            color: #0a3622;
        }

        .estado-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Estilos mejorados para el botón de acciones */
        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background-color: #2e294e;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-view:hover {
            background-color: #3d366a;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(46, 41, 78, 0.2);
        }

        .btn-view:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-view i {
            font-size: 16px;
        }

        .actions {
            text-align: center;
            white-space: nowrap;
        }

        /* Estilos para la paginación (futura implementación) */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
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
                    <h3 class="filter-title"><i class="fas fa-filter"></i> Filtrar pedidos</h3>
                    <form action="" method="get" class="filter-form">
                        <div class="filter-group">
                            <label for="estado">Estado del pedido:</label>
                            <select id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado; ?>" <?php echo (isset($_GET['estado']) && $_GET['estado'] == $estado) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($estado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="fecha_desde"><i class="far fa-calendar-alt"></i> Desde fecha:</label>
                            <input type="date" id="fecha_desde" name="fecha_desde" value="<?php echo $filtros['fecha_desde'] ?? ''; ?>">
                        </div>

                        <div class="filter-group">
                            <label for="fecha_hasta"><i class="far fa-calendar-alt"></i> Hasta fecha:</label>
                            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?php echo $filtros['fecha_hasta'] ?? ''; ?>">
                        </div>

                        <div class="filter-buttons">
                            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Aplicar filtros</button>
                            <a href="pedidos.php" class="btn-clear"><i class="fas fa-times"></i> Limpiar</a>
                        </div>
                    </form>
                </div>

                <!-- Información de resultados -->
                <?php if (!empty($pedidos)): ?>
                    <div style="margin-bottom: 15px; color: #666;">
                        Mostrando <?php echo count($pedidos); ?> pedido(s)
                        <?php if (!empty($filtros)): ?>
                            con los filtros aplicados
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Número Pedido</th>
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
                                <td colspan="7" class="no-results">No hay pedidos disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><?php echo $pedido['ID_Pedido']; ?></td>
                                    <td><?php echo $pedido['numero_pedido']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellidos']); ?>
                                        <?php if (isset($pedido['email'])): ?>
                                            (<?php echo htmlspecialchars($pedido['email']); ?>)
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                                    <td>
                                        <span class="badge estado-<?php echo $pedido['estado']; ?>">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="pedido_detalle.php?id=<?php echo $pedido['ID_Pedido']; ?>"
                                            class="btn-view"
                                            title="Ver detalles del pedido <?php echo $pedido['numero_pedido']; ?>">
                                            <i class="fas fa-eye"></i> Ver
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

        // Función para verificar y redirigir a la página de detalles del pedido
        function verDetallePedido(idPedido) {
            // Validar que el ID existe
            if (!idPedido) {
                alert('Error: No se pudo identificar el pedido');
                return false;
            }

            console.log('Redirigiendo a detalles del pedido ID: ' + idPedido);

            // Aquí puedes agregar alguna lógica adicional si es necesario
            // Por ejemplo, mostrar un indicador de carga

            // La redirección se realiza automáticamente por el enlace
            return true;
        }
    </script>
</body>

</html>