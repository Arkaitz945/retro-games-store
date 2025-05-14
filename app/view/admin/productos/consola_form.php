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
$fabricantes = $consolasController->getFabricantes();
$estados = $consolasController->getEstados();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';
$accion = 'Añadir';
$consola = [
    'ID_Consola' => '',
    'nombre' => '',
    'fabricante' => '',
    'año_lanzamiento' => date('Y'),
    'descripcion' => '',
    'estado' => 'Excelente',
    'precio' => '',
    'stock' => 1,
    'imagen' => 'img/consolas/default.jpg'
];

// Verificar si es edición
if (isset($_GET['id'])) {
    $accion = 'Editar';
    $consola = $consolasController->getConsolaById($_GET['id']);
    if (!$consola) {
        header("Location: consolas.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $consolaData = [
        'nombre' => $_POST['nombre'],
        'fabricante' => $_POST['fabricante'],
        'anio_lanzamiento' => $_POST['anio_lanzamiento'],
        'descripcion' => $_POST['descripcion'],
        'estado' => $_POST['estado'],
        'precio' => $_POST['precio'],
        'stock' => $_POST['stock']
    ];

    // Procesar imagen si se ha subido una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
        $target_dir = "../../../img/consolas/";
        $file_extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $newFileName;

        // Asegurarse de que el directorio existe
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $consolaData['imagen'] = 'img/consolas/' . $newFileName;
        } else {
            $mensaje = "Error al subir la imagen.";
            $tipoMensaje = 'error';
        }
    }

    // Actualizar o crear consola
    if ($accion === 'Editar') {
        $resultado = $consolasController->updateConsola($consola['ID_Consola'], $consolaData);
    } else {
        $resultado = $consolasController->createConsola($consolaData);
    }

    if ($resultado['success']) {
        header("Location: consolas.php?mensaje=" . urlencode($resultado['message']) . "&tipo=success");
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
    <title><?php echo $accion; ?> Consola - Admin Panel</title>
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
                <h1><?php echo $accion; ?> Consola</h1>
                <a href="consolas.php" class="btn-back">
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
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($consola['nombre']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="fabricante">Fabricante*:</label>
                            <input type="text" id="fabricante" name="fabricante" value="<?php echo htmlspecialchars($consola['fabricante']); ?>" required list="fabricantes-list">
                            <datalist id="fabricantes-list">
                                <?php foreach ($fabricantes as $fabricante): ?>
                                    <option value="<?php echo htmlspecialchars($fabricante); ?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="form-group">
                            <label for="anio_lanzamiento">Año de lanzamiento:</label>
                            <input type="number" id="anio_lanzamiento" name="anio_lanzamiento" value="<?php echo htmlspecialchars($consola['año_lanzamiento']); ?>" min="1970" max="<?php echo date('Y'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado*:</label>
                            <select id="estado" name="estado" required>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo htmlspecialchars($estado); ?>" <?php echo ($estado == $consola['estado']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($estado); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio (€)*:</label>
                            <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($consola['precio']); ?>" step="0.01" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock*:</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($consola['stock']); ?>" min="0" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" rows="5"><?php echo htmlspecialchars($consola['descripcion']); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="imagen">Imagen:</label>
                            <div class="image-preview-container">
                                <?php if ($consola['imagen']): ?>
                                    <div class="current-image">
                                        <p>Imagen actual:</p>
                                        <img src="../../<?php echo htmlspecialchars($consola['imagen']); ?>" alt="Imagen actual" class="img-preview">
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="imagen" name="imagen" accept="image/*">
                                <p class="small">Subir una nueva imagen <?php echo $accion == 'Editar' ? '(Dejar en blanco para mantener la actual)' : ''; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="consolas.php" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-submit"><?php echo $accion; ?> consola</button>
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
                    const fabricante = document.getElementById('fabricante');
                    const precio = document.getElementById('precio');

                    if (!nombre.value.trim()) {
                        markInvalid(nombre, 'El nombre es obligatorio');
                        valid = false;
                    } else {
                        markValid(nombre);
                    }

                    if (!fabricante.value.trim()) {
                        markInvalid(fabricante, 'El fabricante es obligatorio');
                        valid = false;
                    } else {
                        markValid(fabricante);
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

            // Para fabricantes, agregar nuevo valor si no existe en la lista
            const fabricanteInput = document.getElementById('fabricante');
            if (fabricanteInput) {
                fabricanteInput.addEventListener('change', function() {
                    const datalist = document.getElementById('fabricantes-list');
                    const options = datalist.querySelectorAll('option');
                    let exists = false;

                    // Check if the value already exists in datalist
                    for (let option of options) {
                        if (option.value === this.value) {
                            exists = true;
                            break;
                        }
                    }

                    // Add the value if it's new
                    if (!exists && this.value.trim() !== '') {
                        const newOption = document.createElement('option');
                        newOption.value = this.value;
                        datalist.appendChild(newOption);
                    }
                });
            }
        });
    </script>
</body>

</html>