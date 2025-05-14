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
$editoriales = $revistasController->getEditoriales();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';
$accion = 'Añadir';
$revista = [
    'ID_Revista' => '',
    'titulo' => '',
    'editorial' => '',
    'fecha_publicacion' => date('Y-m-d'),
    'descripcion' => '',
    'precio' => '',
    'stock' => 1,
    'imagen' => 'img/revistas/default.jpg'
];

// Verificar si es edición
if (isset($_GET['id'])) {
    $accion = 'Editar';
    $revista = $revistasController->getRevistaById($_GET['id']);
    if (!$revista) {
        header("Location: revistas.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $revistaData = [
        'titulo' => $_POST['titulo'],
        'editorial' => $_POST['editorial'],
        'fecha_publicacion' => $_POST['fecha_publicacion'],
        'descripcion' => $_POST['descripcion'],
        'precio' => $_POST['precio'],
        'stock' => $_POST['stock']
    ];

    // Procesar imagen si se ha subido una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
        $target_dir = "../../../img/revistas/";
        $file_extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $newFileName;

        // Asegurarse de que el directorio existe
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $revistaData['imagen'] = 'img/revistas/' . $newFileName;
        } else {
            $mensaje = "Error al subir la imagen.";
            $tipoMensaje = 'error';
        }
    }

    // Actualizar o crear revista
    if ($accion === 'Editar') {
        $resultado = $revistasController->updateRevista($revista['ID_Revista'], $revistaData);
    } else {
        $resultado = $revistasController->createRevista($revistaData);
    }

    if ($resultado['success']) {
        header("Location: revistas.php?mensaje=" . urlencode($resultado['message']) . "&tipo=success");
        exit();
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
    <title><?php echo $accion; ?> Revista - Admin Panel</title>
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
                <h1><?php echo $accion; ?> Revista</h1>
                <a href="revistas.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <form method="post" enctype="multipart/form-data" class="admin-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="titulo">Título*:</label>
                            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($revista['titulo']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="editorial">Editorial*:</label>
                            <input type="text" id="editorial" name="editorial" value="<?php echo htmlspecialchars($revista['editorial']); ?>" required list="editoriales-list">
                            <datalist id="editoriales-list">
                                <?php foreach ($editoriales as $editorial): ?>
                                    <option value="<?php echo htmlspecialchars($editorial); ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="form-group">
                            <label for="fecha_publicacion">Fecha de publicación*:</label>
                            <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?php echo htmlspecialchars($revista['fecha_publicacion']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio (€)*:</label>
                            <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($revista['precio']); ?>" step="0.01" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock*:</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($revista['stock']); ?>" min="0" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" rows="5"><?php echo htmlspecialchars($revista['descripcion']); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="imagen">Imagen:</label>
                            <div class="image-preview-container">
                                <?php if ($revista['imagen']): ?>
                                    <div class="current-image">
                                        <p>Imagen actual:</p>
                                        <img src="../../<?php echo htmlspecialchars($revista['imagen']); ?>" alt="Imagen actual" class="img-preview">
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="imagen" name="imagen" accept="image/*">
                                <p class="small">Subir una nueva imagen <?php echo $accion == 'Editar' ? '(Dejar en blanco para mantener la actual)' : ''; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="revistas.php" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-submit"><?php echo $accion; ?> revista</button>
                    </div>
                </form>
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

            // Preview de imagen
            const imagenInput = document.getElementById('imagen');
            const imagePreview = document.querySelector('.img-preview');

            imagenInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        if (imagePreview) {
                            imagePreview.src = e.target.result;
                        } else {
                            const newPreview = document.createElement('img');
                            newPreview.src = e.target.result;
                            newPreview.classList.add('img-preview');
                            document.querySelector('.image-preview-container').appendChild(newPreview);
                        }
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
</body>

</html>