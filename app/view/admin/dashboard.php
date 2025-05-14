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

$nombreUsuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - RetroGames Store</title>
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
                <li><a href="dashboard.php" class="active">Admin Panel</a></li>
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
            <h1>Panel de Administración</h1>
            <p>Bienvenido al panel de administración de RetroGames Store. Desde aquí puedes gestionar todos los aspectos de la tienda.</p>

            <div class="admin-sections">
                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h2>Gestión de Videojuegos</h2>
                    <p>Añade, edita o elimina videojuegos del catálogo.</p>
                    <a href="productos/juegos.php" class="admin-btn">Gestionar Videojuegos</a>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <h2>Gestión de Consolas</h2>
                    <p>Añade, edita o elimina consolas del catálogo.</p>
                    <a href="productos/consolas.php" class="admin-btn">Gestionar Consolas</a>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h2>Gestión de Revistas</h2>
                    <p>Añade, edita o elimina revistas del catálogo.</p>
                    <a href="productos/revistas.php" class="admin-btn">Gestionar Revistas</a>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>Gestión de Usuarios</h2>
                    <p>Administra las cuentas de usuarios.</p>
                    <a href="usuarios.php" class="admin-btn">Gestionar Usuarios</a>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Gestión de Pedidos</h2>
                    <p>Revisa y gestiona los pedidos realizados.</p>
                    <a href="pedidos.php" class="admin-btn">Gestionar Pedidos</a>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h2>Estadísticas</h2>
                    <p>Visualiza estadísticas de ventas y usuarios.</p>
                    <a href="estadisticas.php" class="admin-btn">Ver Estadísticas</a>
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