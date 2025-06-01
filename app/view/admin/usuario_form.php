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
$modoEdicion = false;
$usuario = null;
$direccion = null;
$titulo = "Añadir Nuevo Usuario";
$errorMensaje = '';

// Obtener usuario si estamos en modo edición
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $modoEdicion = true;
    $idUsuario = $_GET['id'];
    $usuario = $usuariosController->getUsuarioById($idUsuario);

    if (!$usuario) {
        header("Location: usuarios.php?mensaje=" . urlencode("Usuario no encontrado") . "&tipo=error");
        exit();
    }

    // Obtener la dirección del usuario usando el método del controlador de usuarios
    try {
        // Asumimos que el controlador puede obtener la dirección
        $direccion = $usuariosController->getDireccionUsuario($idUsuario);
        if (!$direccion) {
            error_log("No se encontró dirección para el usuario ID: " . $idUsuario);
        }
    } catch (Exception $e) {
        error_log("Error al obtener dirección: " . $e->getMessage());
        $errorMensaje = "No se pudieron cargar los datos de dirección. Por favor, verifique los registros de error.";
    }

    $titulo = "Editar Usuario";
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar'])) {
        // Crear array con datos del usuario
        $datosUsuario = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'esAdmin' => $_POST['esAdmin'] ?? 0
        ];

        // Datos de dirección
        $datosDireccion = [
            'calle' => $_POST['calle'] ?? '',
            'numero' => $_POST['numero'] ?? '',
            'codigoPostal' => $_POST['codigoPostal'] ?? '',
            'idUsuario' => $idUsuario ?? null
        ];

        if ($modoEdicion) {
            // Actualizar usuario existente
            $resultado = $usuariosController->updateUsuario($idUsuario, $datosUsuario);

            // Si existe dirección, actualizarla; si no, crearla
            if ($direccion) {
                // Usar el método del controlador de usuarios para actualizar la dirección
                $resultadoDireccion = $usuariosController->updateDireccionUsuario($direccion['ID_Direccion'], $datosDireccion);
            } else if (!empty($datosDireccion['calle'])) {
                // Usar el método del controlador de usuarios para crear la dirección
                $resultadoDireccion = $usuariosController->createDireccionUsuario($idUsuario, $datosDireccion);
            }

            if ($resultado['success']) {
                header("Location: usuarios.php?mensaje=" . urlencode("Usuario actualizado correctamente") . "&tipo=success");
                exit();
            } else {
                $mensaje = $resultado['message'];
                $tipoMensaje = 'error';
            }
        } else {
            // Código para crear nuevo usuario (si es necesario)
            // ...
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Admin Panel</title>
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
                    <h1><?php echo $titulo; ?></h1>
                    <a href="usuarios.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a Usuarios
                    </a>
                </div>
            </div>

            <?php if ($errorMensaje): ?>
                <div class="alert alert-warning">
                    <?php echo $errorMensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">
                <form method="post" action="" class="admin-form">
                    <!-- Datos personales -->
                    <div class="form-section">
                        <h2>Datos Personales</h2>

                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos"
                                value="<?php echo htmlspecialchars($usuario['apellidos'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="form-section">
                        <h2>Dirección</h2>

                        <?php if (isset($errorConexion) && $errorConexion): ?>
                            <div class="alert alert-warning">
                                No se pudo conectar a la base de datos para obtener la información de dirección.
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="calle">Calle</label>
                            <input type="text" class="form-control" id="calle" name="calle"
                                value="<?php echo htmlspecialchars($direccion['calle'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero"
                                value="<?php echo htmlspecialchars($direccion['numero'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="codigoPostal">Código Postal</label>
                            <input type="text" class="form-control" id="codigoPostal" name="codigoPostal"
                                value="<?php echo htmlspecialchars($direccion['codigoPostal'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="form-section">
                        <h2>Permisos</h2>

                        <div class="form-group">
                            <label for="esAdmin">Permisos de administrador</label>
                            <select class="form-control" id="esAdmin" name="esAdmin">
                                <option value="0" <?php echo (($usuario['esAdmin'] ?? 0) == 0) ? 'selected' : ''; ?>>Usuario estándar</option>
                                <option value="1" <?php echo (($usuario['esAdmin'] ?? 0) == 1) ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="usuarios.php" class="btn-cancel">Cancelar</a>
                        <button type="submit" name="guardar" class="btn-save">Guardar</button>
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