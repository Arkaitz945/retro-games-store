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
require_once "../../controller/admin/UsuariosAdminController.php";

$pedidosController = new PedidosAdminController();
$usuariosController = new UsuariosAdminController();

$nombreUsuario = $_SESSION['usuario'];

// Obtener estadísticas de ventas
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';
$ventasEstadisticas = $pedidosController->getEstadisticasVentas($periodo);
$productosMasVendidos = $pedidosController->getProductosMasVendidos(5);
$usuariosEstadisticas = $usuariosController->getUsuariosEstadisticas($periodo);

// Preparar datos para gráficos
$ventasLabels = [];
$ventasData = [];
$ventasPedidos = [];

foreach ($ventasEstadisticas as $estadistica) {
    $ventasLabels[] = $estadistica['periodo'];
    $ventasData[] = $estadistica['ventas'];
    $ventasPedidos[] = $estadistica['num_pedidos'];
}

$ventasLabelsJSON = json_encode($ventasLabels);
$ventasDataJSON = json_encode($ventasData);
$ventasPedidosJSON = json_encode($ventasPedidos);

// Datos para gráfico de productos más vendidos
$productoLabels = [];
$productoData = [];
$colores = [
    'rgba(255, 99, 132, 0.7)',
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 206, 86, 0.7)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)'
];

foreach ($productosMasVendidos as $index => $producto) {
    $productoLabels[] = $producto['nombre_producto'];
    $productoData[] = $producto['total_vendido'];
}

$productoLabelsJSON = json_encode($productoLabels);
$productoDataJSON = json_encode($productoData);
$coloresJSON = json_encode($colores);

// Calcular totales
$totalVentas = array_sum($ventasData);
$totalPedidos = array_sum($ventasPedidos);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Admin Panel</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .stats-period {
            display: flex;
            gap: 10px;
        }

        .period-btn {
            padding: 8px 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .period-btn.active {
            background-color: #2e294e;
            color: white;
            border-color: #2e294e;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 15px 0;
            color: #2e294e;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .top-products {
            margin-top: 30px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .product-name {
            font-weight: 500;
        }

        .product-sales {
            font-weight: 700;
            color: #2e294e;
        }

        .no-data {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }

        @media (max-width: 992px) {
            .stats-charts {
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
                <li><a href="pedidos.php">Pedidos</a></li>
                <li><a href="estadisticas.php" class="active">Estadísticas</a></li>
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
                    <h1>Estadísticas</h1>
                    <a href="dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Panel
                    </a>
                </div>
            </div>

            <div class="admin-content">
                <div class="stats-header">
                    <h2>Resumen de ventas</h2>
                    <div class="stats-period">
                        <a href="?periodo=dia" class="period-btn <?php echo $periodo === 'dia' ? 'active' : ''; ?>">Últimos 30 días</a>
                        <a href="?periodo=mes" class="period-btn <?php echo $periodo === 'mes' ? 'active' : ''; ?>">Últimos 12 meses</a>
                        <a href="?periodo=anio" class="period-btn <?php echo $periodo === 'anio' ? 'active' : ''; ?>">Por años</a>
                    </div>
                </div>

                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-label">Ventas Totales</div>
                        <div class="stat-value"><?php echo number_format($totalVentas, 2); ?>€</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Pedidos Realizados</div>
                        <div class="stat-value"><?php echo $totalPedidos; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Valor Medio Pedido</div>
                        <div class="stat-value">
                            <?php echo $totalPedidos > 0 ? number_format($totalVentas / $totalPedidos, 2) : '0.00'; ?>€
                        </div>
                    </div>
                </div>

                <div class="stats-charts">
                    <div class="chart-container">
                        <div class="chart-title">Evolución de ventas</div>
                        <?php if (count($ventasLabels) > 0): ?>
                            <canvas id="ventasChart"></canvas>
                        <?php else: ?>
                            <div class="no-data">No hay datos suficientes para mostrar el gráfico</div>
                        <?php endif; ?>
                    </div>

                    <div class="chart-container">
                        <div class="chart-title">Productos más vendidos</div>
                        <?php if (count($productoLabels) > 0): ?>
                            <canvas id="productosChart"></canvas>
                        <?php else: ?>
                            <div class="no-data">No hay datos suficientes para mostrar el gráfico</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="chart-container top-products">
                    <div class="chart-title">Top 5 productos más vendidos</div>
                    <?php if (count($productosMasVendidos) > 0): ?>
                        <?php foreach ($productosMasVendidos as $producto): ?>
                            <div class="product-item">
                                <div class="product-name"><?php echo htmlspecialchars($producto['nombre_producto']); ?> (<?php echo ucfirst($producto['tipo_producto']); ?>)</div>
                                <div class="product-sales"><?php echo $producto['total_vendido']; ?> unidades - <?php echo number_format($producto['ingresos'], 2); ?>€</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">No hay datos de ventas disponibles</div>
                    <?php endif; ?>
                </div>
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

            // Gráfico de evolución de ventas
            <?php if (count($ventasLabels) > 0): ?>
                const ventasCtx = document.getElementById('ventasChart').getContext('2d');
                const ventasChart = new Chart(ventasCtx, {
                    type: 'line',
                    data: {
                        labels: <?php echo $ventasLabelsJSON; ?>,
                        datasets: [{
                                label: 'Ventas (€)',
                                data: <?php echo $ventasDataJSON; ?>,
                                borderColor: '#2e294e',
                                backgroundColor: 'rgba(46, 41, 78, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Número de pedidos',
                                data: <?php echo $ventasPedidosJSON; ?>,
                                borderColor: '#e63946',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Ventas (€)'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Pedidos'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            // Gráfico de productos más vendidos
            <?php if (count($productoLabels) > 0): ?>
                const productosCtx = document.getElementById('productosChart').getContext('2d');
                const productosChart = new Chart(productosCtx, {
                    type: 'pie',
                    data: {
                        labels: <?php echo $productoLabelsJSON; ?>,
                        datasets: [{
                            data: <?php echo $productoDataJSON; ?>,
                            backgroundColor: <?php echo $coloresJSON; ?>,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        return `${label}: ${value} unidades`;
                                    }
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>