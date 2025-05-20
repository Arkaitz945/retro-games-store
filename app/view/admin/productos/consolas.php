<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../../index.php");
    exit();
}

require_once "../../../controller/admin/ConsolasAdminController.php";

$consolasController = new ConsolasAdminController();
$consolas = $consolasController->getAllConsolas();
$fabricantes = $consolasController->getFabricantes();
$estados = $consolasController->getEstados();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';

// Procesar eliminación
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $resultado = $consolasController->deleteConsola($_POST['id']);
    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipoMensaje = 'success';
        // Recargar la lista después de eliminar
        $consolas = $consolasController->getAllConsolas();
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
    <title>Gestión de Consolas - Admin Panel</title>
    <link rel="stylesheet" href="../../css/home.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <a href="../../home.php" class="logo-link">
                <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="../../home.php">Tienda</a></li>
                <li><a href="../dashboard.php">Admin Panel</a></li>
                <li><a href="juegos.php">Videojuegos</a></li>
                <li><a href="consolas.php" class="active">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-dropdown">
                <button class="user-btn"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($nombreUsuario); ?> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="../../home.php"><i class="fas fa-store"></i> Ver Tienda</a>
                    <div class="dropdown-divider"></div>
                    <a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="admin-container">
            <div class="admin-header">
                <div class="admin-header-left">
                    <h1>Gestión de Consolas</h1>
                    <a href="../dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Panel
                    </a>
                </div>
                <a href="consola_form.php" class="btn-add">
                    <i class="fas fa-plus"></i> Añadir Consola
                </a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <div class="search-filter">
                    <input type="text" id="search-input" placeholder="Buscar por nombre...">
                    <select id="fabricante-filter">
                        <option value="">Todos los fabricantes</option>
                        <?php foreach ($fabricantes as $fabricante): ?>
                            <option value="<?php echo htmlspecialchars($fabricante); ?>">
                                <?php echo htmlspecialchars($fabricante); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="estado-filter">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo htmlspecialchars($estado); ?>">
                                <?php echo htmlspecialchars($estado); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fabricante</th>
                            <th>Año</th>
                            <th>Estado</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($consolas)): ?>
                            <tr>
                                <td colspan="8" class="no-results">No hay consolas disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($consolas as $consola): ?>
                                <tr data-nombre="<?php echo strtolower(htmlspecialchars($consola['nombre'])); ?>"
                                    data-fabricante="<?php echo htmlspecialchars($consola['fabricante']); ?>"
                                    data-estado="<?php echo htmlspecialchars($consola['estado']); ?>">
                                    <td><?php echo $consola['ID_Consola']; ?></td>
                                    <td><?php echo htmlspecialchars($consola['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($consola['fabricante']); ?></td>
                                    <td><?php echo htmlspecialchars($consola['año_lanzamiento']); ?></td>
                                    <td><?php echo htmlspecialchars($consola['estado']); ?></td>
                                    <td><?php echo number_format($consola['precio'], 2); ?>€</td>
                                    <td><?php echo $consola['stock']; ?></td>
                                    <td class="actions">
                                        <a href="consola_form.php?id=<?php echo $consola['ID_Consola']; ?>" class="btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-delete" title="Eliminar" data-id="<?php echo $consola['ID_Consola']; ?>" data-nombre="<?php echo htmlspecialchars($consola['nombre']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
            <p>¿Estás seguro de que deseas eliminar la consola "<span id="console-name"></span>"?</p>
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
            const consoleName = document.getElementById('console-name');

            // Configurar botones de eliminación
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');

                    deleteId.value = id;
                    consoleName.textContent = nombre;
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
            const fabricanteFilter = document.getElementById('fabricante-filter');
            const estadoFilter = document.getElementById('estado-filter');
            const tableRows = document.querySelectorAll('tbody tr');

            // Función de filtrado mejorada
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const fabricante = fabricanteFilter.value;
                const estado = estadoFilter.value;
                let visibleRows = 0;

                tableRows.forEach(row => {
                    // Omitir fila de "no resultados"
                    if (row.classList.contains('no-results')) return;

                    const rowNombre = row.getAttribute('data-nombre');
                    const rowFabricante = row.getAttribute('data-fabricante');
                    const rowEstado = row.getAttribute('data-estado');

                    // Verificar si cumple con todos los filtros
                    const matchSearch = rowNombre.includes(searchTerm);
                    const matchFabricante = fabricante === '' || rowFabricante === fabricante;
                    const matchEstado = estado === '' || rowEstado === estado;

                    if (matchSearch && matchFabricante && matchEstado) {
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
            fabricanteFilter.addEventListener('change', filterTable);
            estadoFilter.addEventListener('change', filterTable);

            // Botón para añadir nueva consola
            const addButton = document.querySelector('.btn-add');
            if (addButton) {
                addButton.addEventListener('click', function(e) {
                    // Si se necesita lógica adicional antes de redirigir
                    // e.preventDefault();
                    // Lógica aquí...

                    // Por defecto, el enlace ya navega a consola_form.php
                });
            }

            // Botones de acción por fila
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Por defecto, estos enlaces ya navegan a consola_form.php con ID
                });
            });

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