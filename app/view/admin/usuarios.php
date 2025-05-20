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

require_once "../../controller/admin/UsuariosAdminController.php";

// Verificar si el archivo del modelo existe
$modeloPath = "../../model/UsuariosModel.php";
if (file_exists($modeloPath)) {
    error_log("Vista Usuarios: El archivo del modelo existe en: " . realpath($modeloPath));
} else {
    error_log("Vista Usuarios: ¡ERROR! El archivo del modelo NO existe en: " . realpath(dirname($modeloPath)) . "/" . basename($modeloPath));
}

$usuariosController = new UsuariosAdminController();
$usuarios = $usuariosController->getAllUsuarios();

// Depuración mejorada
error_log("Vista Usuarios: Tipo de datos recibidos: " . gettype($usuarios));
error_log("Vista Usuarios: Datos de usuarios recibidos en la vista: " . print_r($usuarios, true));

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';

// Procesar eliminación
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $resultado = $usuariosController->deleteUsuario($_POST['id']);
    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipoMensaje = 'success';
        // Recargar la lista después de eliminar
        $usuarios = $usuariosController->getAllUsuarios();
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
    <title>Gestión de Usuarios - Admin Panel</title>
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
                <li><a href="usuarios.php" class="active">Usuarios</a></li>
                <li><a href="pedidos.php">Pedidos</a></li>
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
                    <h1>Gestión de Usuarios</h1>
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
                <!-- Añadir mensaje de depuración visible -->
                <?php if (empty($usuarios)): ?>
                    <div class="alert alert-warning">
                        No se encontraron usuarios en la base de datos. Verifica los registros de error para más información.
                    </div>
                <?php endif; ?>

                <div class="search-filter">
                    <input type="text" id="search-input" placeholder="Buscar por nombre o email...">
                    <select id="role-filter">
                        <option value="">Todos los roles</option>
                        <option value="1">Administradores</option>
                        <option value="0">Usuarios estándar</option>
                    </select>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="5" class="no-results">No hay usuarios disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr data-nombre="<?php echo strtolower(htmlspecialchars($usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? ''))); ?>"
                                    data-email="<?php echo strtolower(htmlspecialchars($usuario['correo'] ?? $usuario['email'] ?? '')); ?>"
                                    data-rol="<?php echo $usuario['esAdmin'] ?? $usuario['admin'] ?? 0; ?>">
                                    <td><?php echo $usuario['ID_U'] ?? $usuario['id'] ?? ''; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['correo'] ?? $usuario['email'] ?? ''); ?></td>
                                    <td>
                                        <?php if (($usuario['esAdmin'] ?? $usuario['admin'] ?? 0) == 1): ?>
                                            <span class="badge admin-badge">Administrador</span>
                                        <?php else: ?>
                                            <span class="badge user-badge">Usuario</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <a href="usuario_form.php?id=<?php echo $usuario['ID_U'] ?? $usuario['id'] ?? ''; ?>" class="btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($_SESSION['id'] != ($usuario['ID_U'] ?? $usuario['id'] ?? '')): ?>
                                            <button type="button" class="btn-delete" title="Eliminar"
                                                data-id="<?php echo $usuario['ID_U'] ?? $usuario['id'] ?? ''; ?>"
                                                data-nombre="<?php echo htmlspecialchars($usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? '')); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación para eliminar -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirmar eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar al usuario "<span id="user-name"></span>"?</p>
            <p class="warning">Esta acción no se puede deshacer.</p>
            <div class="modal-buttons">
                <button id="cancel-delete" class="btn-cancel">Cancelar</button>
                <form id="delete-form" method="post" action="">
                    <input type="hidden" name="id" id="delete-id">
                    <button type="submit" name="delete" class="btn-confirm-delete">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

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
        // JavaScript para el menú desplegable y funcionalidad de la página
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

            // Modal de eliminación
            const modal = document.getElementById('delete-modal');
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const closeBtn = document.querySelector('.close');
            const cancelBtn = document.getElementById('cancel-delete');
            const deleteForm = document.getElementById('delete-form');
            const deleteId = document.getElementById('delete-id');
            const userName = document.getElementById('user-name');

            // Configurar botones de eliminación
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');

                    deleteId.value = id;
                    userName.textContent = nombre;
                    modal.style.display = 'block';
                });
            });

            // Cerrar modal
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });

            // Búsqueda y filtrado
            const searchInput = document.getElementById('search-input');
            const roleFilter = document.getElementById('role-filter');
            const tableRows = document.querySelectorAll('tbody tr');

            // Función de filtrado mejorada
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const role = roleFilter.value;
                let visibleRows = 0;

                tableRows.forEach(row => {
                    // Omitir fila de "no resultados"
                    if (row.classList.contains('no-results')) return;

                    const rowNombre = row.getAttribute('data-nombre');
                    const rowEmail = row.getAttribute('data-email');
                    const rowRol = row.getAttribute('data-rol');

                    const matchSearch = rowNombre.includes(searchTerm) || rowEmail.includes(searchTerm);
                    const matchRole = role === '' || rowRol === role;

                    if (matchSearch && matchRole) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Mostrar mensaje cuando no hay resultados
                const noResultsRow = document.querySelector('.no-results');
                if (noResultsRow) {
                    if (visibleRows === 0) {
                        noResultsRow.style.display = 'table-row';
                    } else {
                        noResultsRow.style.display = 'none';
                    }
                }
            }

            // Eventos para filtrado inmediato
            searchInput.addEventListener('keyup', filterTable);
            roleFilter.addEventListener('change', filterTable);

            // Inicializar la página (aplicar filtros si hay parámetros en URL)
            function initPage() {
                // Obtener parámetros de URL
                const urlParams = new URLSearchParams(window.location.search);
                const mensajeParam = urlParams.get('mensaje');
                const tipoParam = urlParams.get('tipo');

                // Mostrar mensaje si viene en la URL
                if (mensajeParam && tipoParam) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = `alert alert-${tipoParam}`;
                    alertDiv.textContent = decodeURIComponent(mensajeParam);

                    // Insertar antes de la tabla
                    const adminContent = document.querySelector('.admin-content');
                    adminContent.insertBefore(alertDiv, adminContent.firstChild);

                    // Auto-eliminar después de 5 segundos
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            }

            // Inicializar página
            initPage();
        });
    </script>
</body>

</html>