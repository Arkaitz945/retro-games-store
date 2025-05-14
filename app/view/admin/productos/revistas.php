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

require_once "../../../controller/admin/RevistasAdminController.php";

$revistasController = new RevistasAdminController();
$revistas = $revistasController->getAllRevistas();
$editoriales = $revistasController->getEditoriales();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';

// Procesar eliminación
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $resultado = $revistasController->deleteRevista($_POST['id']);
    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipoMensaje = 'success';
        // Recargar la lista después de eliminar
        $revistas = $revistasController->getAllRevistas();
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
    <title>Gestión de Revistas - Admin Panel</title>
    <link rel="stylesheet" href="../../css/home.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/sticky-footer.css">
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
                <li><a href="consolas.php">Consolas</a></li>
                <li><a href="revistas.php" class="active">Revistas</a></li>
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
                    <h1>Gestión de Revistas</h1>
                    <a href="../dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al Panel
                    </a>
                </div>
                <a href="revista_form.php" class="btn-add">
                    <i class="fas fa-plus"></i> Añadir Revista
                </a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <div class="search-filter">
                    <input type="text" id="search-input" placeholder="Buscar por título...">
                    <select id="editorial-filter">
                        <option value="">Todas las editoriales</option>
                        <?php foreach ($editoriales as $editorial): ?>
                            <option value="<?php echo htmlspecialchars($editorial); ?>">
                                <?php echo htmlspecialchars($editorial); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Editorial</th>
                            <th>Fecha Publicación</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($revistas)): ?>
                            <tr>
                                <td colspan="8" class="no-results">No hay revistas disponibles</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($revistas as $revista): ?>
                                <tr data-titulo="<?php echo strtolower(htmlspecialchars($revista['titulo'])); ?>"
                                    data-editorial="<?php echo htmlspecialchars($revista['editorial']); ?>">
                                    <td><?php echo $revista['ID_Revista']; ?></td>
                                    <td class="product-image">
                                        <img src="../../<?php echo htmlspecialchars($revista['imagen']); ?>" alt="<?php echo htmlspecialchars($revista['titulo']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($revista['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($revista['editorial']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($revista['fecha_publicacion'])); ?></td>
                                    <td><?php echo number_format($revista['precio'], 2); ?>€</td>
                                    <td><?php echo $revista['stock']; ?></td>
                                    <td class="actions">
                                        <a href="revista_form.php?id=<?php echo $revista['ID_Revista']; ?>" class="btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-delete" title="Eliminar" data-id="<?php echo $revista['ID_Revista']; ?>" data-titulo="<?php echo htmlspecialchars($revista['titulo']); ?>">
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
            <p>¿Estás seguro de que deseas eliminar la revista "<span id="magazine-title"></span>"?</p>
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

            // Modal de eliminación
            const modal = document.getElementById('delete-modal');
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const closeBtn = document.querySelector('.close');
            const cancelBtn = document.getElementById('cancel-delete');
            const deleteForm = document.getElementById('delete-form');
            const deleteId = document.getElementById('delete-id');
            const magazineTitle = document.getElementById('magazine-title');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const titulo = this.getAttribute('data-titulo');

                    deleteId.value = id;
                    magazineTitle.textContent = titulo;
                    modal.style.display = 'block';
                });
            });

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
            const editorialFilter = document.getElementById('editorial-filter');
            const tableRows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const editorial = editorialFilter.value;

                tableRows.forEach(row => {
                    if (row.classList.contains('no-results')) return;

                    const rowTitulo = row.getAttribute('data-titulo');
                    const rowEditorial = row.getAttribute('data-editorial');

                    const matchSearch = rowTitulo.includes(searchTerm);
                    const matchEditorial = editorial === '' || rowEditorial === editorial;

                    if (matchSearch && matchEditorial) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            editorialFilter.addEventListener('change', filterTable);
        });
    </script>
</body>

</html>