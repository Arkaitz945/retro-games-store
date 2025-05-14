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

$usuariosController = new UsuariosAdminController();

$nombreUsuario = $_SESSION['usuario'];
$mensaje = '';
$tipoMensaje = '';
$accion = 'Editar';
$usuario = [
    'id' => '',
    'nombre' => '',
    'apellidos' => '',
    'email' => '',
    'telefono' => '',
    'direccion' => '',
    'admin' => 0
];

// Verificar si se proporciona un ID
if (isset($_GET['id'])) {
    $usuario = $usuariosController->getUsuarioById($_GET['id']);
    if (!$usuario) {
        header("Location: usuarios.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $usuarioData = [
        'nombre' => $_POST['nombre'],
        'apellidos' => $_POST['apellidos'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono'],
        'direccion' => $_POST['direccion'],
        'admin' => isset($_POST['admin']) ? 1 : 0
    ];

    // Actualizar usuario
    $resultado = $usuariosController->updateUsuario($usuario['id'], $usuarioData);

    if ($resultado['success']) {
        header("Location: usuarios.php?mensaje=" . urlencode($resultado['message']) . "&tipo=success");
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
    <title>Editar Usuario - Admin Panel</title>
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
                    <h1>Editar Usuario</h1>
                    <a href="usuarios.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <form method="post" class="admin-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre*:</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email*:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="direccion">Dirección:</label>
                            <textarea id="direccion" name="direccion" rows="3"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <label for="admin">
                                    <input type="checkbox" id="admin" name="admin" <?php echo $usuario['admin'] ? 'checked' : ''; ?>>
                                    Permisos de administrador
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="usuarios.php" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-submit">Guardar cambios</button>
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

            // Validación del formulario
            const form = document.querySelector('.admin-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;

                    // Validar campos obligatorios
                    const nombre = document.getElementById('nombre');
                    const email = document.getElementById('email');

                    if (!nombre.value.trim()) {
                        markInvalid(nombre, 'El nombre es obligatorio');
                        valid = false;
                    } else {
                        markValid(nombre);
                    }

                    if (!email.value.trim()) {
                        markInvalid(email, 'El email es obligatorio');
                        valid = false;
                    } else if (!validateEmail(email.value)) {
                        markInvalid(email, 'El formato del email no es válido');
                        valid = false;
                    } else {
                        markValid(email);
                    }

                    // Verificar si es el último administrador y se están quitando los permisos
                    const admin = document.getElementById('admin');
                    const usuarioId = <?php echo json_encode($usuario['id']); ?>;
                    const esAdmin = <?php echo json_encode((bool)$usuario['admin']); ?>;

                    if (esAdmin && !admin.checked) {
                        // Aquí podrías hacer una verificación AJAX para comprobar si es el último admin
                        // Por simplicidad, mostramos una advertencia
                        if (confirm('¿Estás seguro de quitar los permisos de administrador? Si es el último administrador, no podrás hacerlo.')) {
                            // Continuar
                        } else {
                            e.preventDefault();
                            admin.checked = true;
                            return false;
                        }
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

            function validateEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }
        });
    </script>
</body>

</html>