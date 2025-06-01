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

require_once "../../../controller/admin/JuegosAdminController.php";

$juegosController = new JuegosAdminController();
$plataformas = $juegosController->getPlataformas();
$generos = $juegosController->getGeneros();
$estados = $juegosController->getEstados();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';
$accion = 'Añadir';
$juego = [
    'ID_J' => '',
    'nombre' => '',
    'plataforma' => '',
    'genero' => '',
    'desarrollador' => '',
    'publisher' => '',
    'año_lanzamiento' => date('Y'),
    'descripcion' => '',
    'estado' => 'Excelente',
    'precio' => '',
    'stock' => 1,
    'incluye_caja' => 1,
    'incluye_manual' => 1,
    'region' => 'PAL',
    'imagen' => 'img/juegos/default.jpg'
];

// Verificar si es edición
if (isset($_GET['id'])) {
    $accion = 'Editar';
    $juego = $juegosController->getJuegoById($_GET['id']);
    if (!$juego) {
        header("Location: juegos.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $juegoData = [
        'nombre' => $_POST['nombre'],
        'plataforma' => $_POST['plataforma'],
        'genero' => $_POST['genero'],
        'desarrollador' => $_POST['desarrollador'],
        'publisher' => $_POST['publisher'],
        'año_lanzamiento' => $_POST['anio_lanzamiento'],
        'descripcion' => $_POST['descripcion'],
        'estado' => $_POST['estado'],
        'precio' => $_POST['precio'],
        'stock' => $_POST['stock'],
        'incluye_caja' => isset($_POST['incluye_caja']) ? 1 : 0,
        'incluye_manual' => isset($_POST['incluye_manual']) ? 1 : 0,
        'region' => $_POST['region']
    ];

    // Procesar imagen si se ha subido una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
        $target_dir = "../../../img/juegos/";
        $file_extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $newFileName;

        // Asegurarse de que el directorio existe
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $juegoData['imagen'] = 'img/juegos/' . $newFileName;
        } else {
            $mensaje = "Error al subir la imagen.";
            $tipoMensaje = 'error';
        }
    }

    // Actualizar o crear juego
    if ($accion === 'Editar') {
        $resultado = $juegosController->updateJuego($juego['ID_J'], $juegoData);
    } else {
        $resultado = $juegosController->createJuego($juegoData);
    }

    if ($resultado['success']) {
        header("Location: juegos.php?mensaje=" . urlencode($resultado['message']) . "&tipo=success");
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
    <title><?php echo $accion; ?> Videojuego - Admin Panel</title>
    <link rel="stylesheet" href="../../css/home.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkbox-group {
            margin-top: 5px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            line-height: 1;
        }

        .checkbox-wrapper input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 0;
            width: auto;
            height: auto;
        }
    </style>
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
                <li><a href="juegos.php" class="active">Videojuegos</a></li>
                <li><a href="consolas.php">Consolas</a></li>
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
                <h1><?php echo $accion; ?> Videojuego</h1>
                <a href="juegos.php" class="btn-back">
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
                            <label for="nombre">Nombre*:</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($juego['nombre']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="plataforma">Plataforma*:</label>
                            <select id="plataforma" name="plataforma" required>
                                <option value="">Seleccionar plataforma</option>
                                <?php foreach ($plataformas as $plataforma): ?>
                                    <option value="<?php echo htmlspecialchars($plataforma); ?>" <?php echo ($plataforma == $juego['plataforma']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($plataforma); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="genero">Género*:</label>
                            <select id="genero" name="genero" required>
                                <option value="">Seleccionar género</option>
                                <?php foreach ($generos as $genero): ?>
                                    <option value="<?php echo htmlspecialchars($genero); ?>" <?php echo ($genero == $juego['genero']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genero); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="desarrollador">Desarrollador:</label>
                            <input type="text" id="desarrollador" name="desarrollador" value="<?php echo htmlspecialchars($juego['desarrollador']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="publisher">Editor:</label>
                            <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($juego['publisher']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="anio_lanzamiento">Año de lanzamiento:</label>
                            <input type="number" id="anio_lanzamiento" name="anio_lanzamiento" value="<?php echo htmlspecialchars($juego['año_lanzamiento']); ?>" min="1970" max="<?php echo date('Y'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado*:</label>
                            <select id="estado" name="estado" required>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo htmlspecialchars($estado); ?>" <?php echo ($estado == $juego['estado']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($estado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio (€)*:</label>
                            <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($juego['precio']); ?>" step="0.01" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock*:</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($juego['stock']); ?>" min="0" required>
                        </div>

                        <div class="form-group">
                            <label>Incluye:</label>
                            <div class="checkbox-group">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="incluye_caja" name="incluye_caja" <?php echo $juego['incluye_caja'] ? 'checked' : ''; ?>>
                                    <label for="incluye_caja">Caja original</label>
                                </div>
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="incluye_manual" name="incluye_manual" <?php echo $juego['incluye_manual'] ? 'checked' : ''; ?>>
                                    <label for="incluye_manual">Manual de instrucciones</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="region">Región:</label>
                            <select id="region" name="region">
                                <option value="PAL" <?php echo ($juego['region'] == 'PAL') ? 'selected' : ''; ?>>PAL (Europa)</option>
                                <option value="NTSC-U" <?php echo ($juego['region'] == 'NTSC-U') ? 'selected' : ''; ?>>NTSC-U (América)</option>
                                <option value="NTSC-J" <?php echo ($juego['region'] == 'NTSC-J') ? 'selected' : ''; ?>>NTSC-J (Japón)</option>
                                <option value="Todas" <?php echo ($juego['region'] == 'Todas') ? 'selected' : ''; ?>>Todas las regiones</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" rows="5"><?php echo htmlspecialchars($juego['descripcion']); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label>Imagen actual:</label>
                            <div class="image-preview-container">
                                <?php if ($juego['imagen']): ?>
                                    <div class="current-image">
                                        <img src="../../<?php echo htmlspecialchars($juego['imagen']); ?>" alt="Imagen actual" class="img-preview">
                                    </div>
                                <?php else: ?>
                                    <p>No hay imagen asociada a este producto</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="juegos.php" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-submit"><?php echo $accion; ?> videojuego</button>
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
        // JavaScript para el menú desplegable y funcionalidad del formulario
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

            // Preview de imagen
            const imagenInput = document.getElementById('imagen');
            const imagePreview = document.querySelector('.img-preview');

            if (imagenInput) {
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
            }

            // Validación del formulario
            const form = document.querySelector('.admin-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;

                    // Validar campos obligatorios
                    const nombre = document.getElementById('nombre');
                    const plataforma = document.getElementById('plataforma');
                    const precio = document.getElementById('precio');

                    if (!nombre.value.trim()) {
                        markInvalid(nombre, 'El nombre es obligatorio');
                        valid = false;
                    } else {
                        markValid(nombre);
                    }

                    if (!plataforma.value) {
                        markInvalid(plataforma, 'La plataforma es obligatoria');
                        valid = false;
                    } else {
                        markValid(plataforma);
                    }

                    if (!precio.value || isNaN(precio.value) || parseFloat(precio.value) <= 0) {
                        markInvalid(precio, 'El precio debe ser un número positivo');
                        valid = false;
                    } else {
                        markValid(precio);
                    }

                    // Comprobar si hay errores
                    if (!valid) {
                        e.preventDefault();
                        // Scroll al primer error
                        const firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    } else {
                        // Mostrar indicador de carga
                        const submitBtn = document.querySelector('.btn-submit');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                        submitBtn.disabled = true;
                    }
                });
            }

            // Funciones auxiliares para validación
            function markInvalid(element, message) {
                element.classList.add('is-invalid');

                // Crear o actualizar mensaje de error
                let errorDiv = element.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    element.parentNode.insertBefore(errorDiv, element.nextSibling);
                }

                errorDiv.textContent = message;
            }

            function markValid(element) {
                element.classList.remove('is-invalid');

                // Eliminar mensaje de error si existe
                const errorDiv = element.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            }

            // Botón de cancelar
            const cancelBtn = document.querySelector('.btn-cancel');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    if (formChanged()) {
                        const confirmLeave = confirm('¿Estás seguro que deseas cancelar? Los cambios no guardados se perderán.');
                        if (!confirmLeave) {
                            e.preventDefault();
                        }
                    }
                });
            }

            // Función para verificar si el formulario ha cambiado
            let initialFormState = '';

            function formChanged() {
                if (initialFormState === '') {
                    initialFormState = getFormState();
                    return false;
                }
                return getFormState() !== initialFormState;
            }

            function getFormState() {
                return Array.from(form.elements)
                    .filter(el => el.type !== 'file' && el.type !== 'button' && el.type !== 'submit')
                    .map(el => {
                        if (el.type === 'checkbox') {
                            return el.checked;
                        }
                        return el.value;
                    })
                    .join('|');
            }
        });
    </script>
</body>

</html>