<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Incluir controlador
require_once "../controller/UsuarioController.php";

$usuarioController = new UsuarioController();
$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Obtener datos del usuario
$usuario = $usuarioController->getUserById($idUsuario);
$direccion = $usuarioController->getDireccionUsuario($idUsuario);

// Obtener todas las direcciones del usuario
$direcciones = $usuarioController->getDireccionesUsuario($idUsuario);

// Inicializar array de mensajes para diferentes formularios
$mensajes = [
    'datos' => ['tipo' => '', 'texto' => ''],
    'direccion' => ['tipo' => '', 'texto' => ''],
    'password' => ['tipo' => '', 'texto' => ''],
    'cuenta' => ['tipo' => '', 'texto' => ''],
    'correo' => ['tipo' => '', 'texto' => '']
];

// Determinar la pestaña activa - por defecto 'perfil'
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'perfil';

// Variables para mensaje general
$mensaje = null;
$tipoMensaje = null;

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar datos personales
    if (isset($_POST['actualizar_perfil'])) {  // Changed from actualizar_datos to actualizar_perfil
        $datos = [
            'nombre' => $_POST['nombre'],
            'apellidos' => $_POST['apellidos'],
            'correo' => $_POST['correo'] ?? $usuario['correo'] // Use existing email if not provided
        ];

        $resultado = $usuarioController->updateUser($idUsuario, $datos);

        if ($resultado) {
            $mensajes['datos']['tipo'] = 'success';
            $mensajes['datos']['texto'] = "Datos actualizados correctamente";
            // Actualizar nombre de usuario en sesión
            $_SESSION['usuario'] = $_POST['nombre'];
            $nombreUsuario = $_POST['nombre'];
            // Actualizar datos en la variable
            $usuario = $usuarioController->getUserById($idUsuario);
        } else {
            $mensajes['datos']['tipo'] = 'error';
            $mensajes['datos']['texto'] = "Error al actualizar los datos";
        }
    }

    // Actualizar correo
    if (isset($_POST['actualizar_correo'])) {
        $nuevoCorreo = $_POST['correo'];

        // Verificar que el correo sea válido
        if (!filter_var($nuevoCorreo, FILTER_VALIDATE_EMAIL)) {
            $mensajes['correo']['tipo'] = 'error';
            $mensajes['correo']['texto'] = "El correo electrónico no es válido";
        } else {
            $datos = ['correo' => $nuevoCorreo];
            $resultado = $usuarioController->updateUser($idUsuario, $datos);

            if ($resultado) {
                $mensajes['correo']['tipo'] = 'success';
                $mensajes['correo']['texto'] = "Correo actualizado correctamente";
                // Actualizar datos en la variable
                $usuario = $usuarioController->getUserById($idUsuario);
            } else {
                $mensajes['correo']['tipo'] = 'error';
                $mensajes['correo']['texto'] = "Error al actualizar el correo. Puede que ya esté en uso.";
            }
        }
    }

    // Actualizar dirección
    if (isset($_POST['actualizar_direccion'])) {
        $datosDireccion = [
            'calle' => $_POST['calle'],
            'numero' => $_POST['numero'],
            'codigoPostal' => $_POST['codigo_postal']
        ];

        $resultadoDireccion = $usuarioController->updateDireccion($idUsuario, $datosDireccion);

        if ($resultadoDireccion) {
            $mensajes['direccion']['tipo'] = 'success';
            $mensajes['direccion']['texto'] = "Dirección actualizada correctamente";
            // Actualizar datos en la variable
            $direccion = $usuarioController->getDireccionUsuario($idUsuario);
        } else {
            $mensajes['direccion']['tipo'] = 'error';
            $mensajes['direccion']['texto'] = "Error al actualizar la dirección";
        }
    }

    // Cambiar contraseña
    if (isset($_POST['actualizar_password'])) {  // Changed from cambiar_password to actualizar_password
        $oldPassword = $_POST['current_password'];  // Changed from password_actual to current_password
        $newPassword = $_POST['new_password'];      // Changed from password_nueva to new_password
        $confirmPassword = $_POST['confirm_password']; // Changed from password_confirmar to confirm_password

        if ($newPassword !== $confirmPassword) {
            $mensajes['password']['tipo'] = 'error';
            $mensajes['password']['texto'] = "Las contraseñas no coinciden";
        } else {
            $resultadoPassword = $usuarioController->changePassword($idUsuario, $oldPassword, $newPassword);

            if ($resultadoPassword['success']) {
                $mensajes['password']['tipo'] = 'success';
                $mensajes['password']['texto'] = $resultadoPassword['message'];
            } else {
                $mensajes['password']['tipo'] = 'error';
                $mensajes['password']['texto'] = $resultadoPassword['message'];
            }
        }
    }

    // Guardar nueva dirección o actualizar existente
    if (isset($_POST['guardar_direccion'])) {
        $datosDireccion = [
            'calle' => $_POST['calle'],
            'numero' => $_POST['numero'],
            'codigoPostal' => $_POST['codigo_postal']
        ];

        // Si hay un ID de dirección, actualizar esa dirección específica
        if (!empty($_POST['id_direccion'])) {
            $idDireccion = $_POST['id_direccion'];
            $resultadoDireccion = $usuarioController->updateDireccionById($idDireccion, $datosDireccion);
            $mensaje = "actualizada";
        } else {
            // Si no hay ID, crear una nueva dirección
            $resultadoDireccion = $usuarioController->addDireccion($idUsuario, $datosDireccion);
            $mensaje = "añadida";
        }

        if ($resultadoDireccion) {
            $_SESSION['mensaje_direccion'] = [
                'tipo' => 'success',
                'texto' => "Dirección $mensaje correctamente"
            ];

            // Redirigir para actualizar la página
            header("Location: ajustes.php?tab=direcciones");
            exit();
        } else {
            $mensajes['direccion']['tipo'] = 'error';
            $mensajes['direccion']['texto'] = "Error al $mensaje la dirección";
        }
    }

    // Eliminar dirección
    if (isset($_POST['eliminar_direccion'])) {
        $idDireccion = $_POST['id_direccion'];

        if ($usuarioController->deleteDireccion($idDireccion, $idUsuario)) {
            $_SESSION['mensaje_direccion'] = [
                'tipo' => 'success',
                'texto' => "Dirección eliminada correctamente"
            ];

            // Redirigir para actualizar la página
            header("Location: ajustes.php?tab=direcciones");
            exit();
        } else {
            $mensajes['direccion']['tipo'] = 'error';
            $mensajes['direccion']['texto'] = "Error al eliminar la dirección";
        }
    }

    // Eliminar cuenta (si existe esta funcionalidad)
    if (isset($_POST['eliminar_cuenta'])) {
        // Lógica para eliminar cuenta
        // ...

        $mensajes['cuenta']['tipo'] = 'info';
        $mensajes['cuenta']['texto'] = "Esta funcionalidad aún no está implementada";
    }
}

// Verificar si hay mensajes almacenados en la sesión (de redirecciones)
if (isset($_SESSION['mensaje_direccion'])) {
    $mensajes['direccion'] = $_SESSION['mensaje_direccion'];
    unset($_SESSION['mensaje_direccion']); // Limpiar el mensaje después de usarlo
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes de Cuenta - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/ajustes.css">
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <a href="home.php" class="logo-link">
                <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="videojuegos.php">Videojuegos</a></li>
                <li><a href="consolas.php">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
                <li><a href="accesorios.php">Accesorios</a></li>
                <?php if ($esAdmin): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="user-menu">
            <div class="user-dropdown">
                <button class="user-btn"><i class="fas fa-user"></i> <?php echo htmlspecialchars($nombreUsuario); ?> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                        <a href="admin/dashboard.php"><i class="fas fa-user-shield"></i> Panel de Administración</a>
                        <div class="dropdown-divider"></div>
                    <?php endif; ?>
                    <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito
                        <?php if ($cantidadCarrito > 0): ?>
                            <span class="cart-badge"><?php echo $cantidadCarrito; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="ajustes.php" class="active"><i class="fas fa-cog"></i> Ajustes</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="container">
            <div class="settings-container">
                <div class="settings-header">
                    <h1>Ajustes de Cuenta</h1>
                </div>

                <div class="settings-tabs">
                    <div class="settings-tab <?php echo $activeTab === 'perfil' ? 'active' : ''; ?>" data-tab="perfil">
                        <i class="fas fa-user"></i> Perfil
                    </div>
                    <div class="settings-tab <?php echo $activeTab === 'correo' ? 'active' : ''; ?>" data-tab="correo">
                        <i class="fas fa-envelope"></i> Correo Electrónico
                    </div>
                    <div class="settings-tab <?php echo $activeTab === 'password' ? 'active' : ''; ?>" data-tab="password">
                        <i class="fas fa-lock"></i> Contraseña
                    </div>
                    <div class="settings-tab <?php echo $activeTab === 'direcciones' ? 'active' : ''; ?>" data-tab="direcciones">
                        <i class="fas fa-map-marker-alt"></i> Direcciones
                    </div>
                </div>

                <div class="settings-content">
                    <!-- Pestaña de Perfil -->
                    <div class="tab-panel <?php echo $activeTab === 'perfil' ? 'active' : ''; ?>" id="perfil-panel">
                        <h2>Información Personal</h2>

                        <?php if (!empty($mensajes['datos']['texto'])): ?>
                            <div class="alert alert-<?php echo $mensajes['datos']['tipo']; ?>">
                                <?php echo $mensajes['datos']['texto']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="ajustes.php?tab=perfil" method="post">
                            <div class="form-row">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                            </div>

                            <div class="form-row">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($usuario['apellidos'] ?? ''); ?>">
                            </div>

                            <button type="submit" name="actualizar_perfil" class="btn-submit">Guardar Cambios</button>
                        </form>
                    </div>

                    <!-- Pestaña de correo -->
                    <div class="tab-panel <?php echo $activeTab === 'correo' ? 'active' : ''; ?>" id="correo-panel">
                        <h2>Cambiar Correo Electrónico</h2>

                        <?php if ($mensajes['correo']['texto']): ?>
                            <div class="form-message <?php echo $mensajes['correo']['tipo']; ?>">
                                <?php echo $mensajes['correo']['texto']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="ajustes.php?tab=correo" method="post">
                            <div class="form-row">
                                <label for="correo">Correo Electrónico</label>
                                <!-- Cambiar type="correo" a type="email" -->
                                <input type="email" id="correo" name="correo" class="form-control"
                                    value="<?php echo isset($usuario) && isset($usuario['correo']) ? htmlspecialchars($usuario['correo']) : ''; ?>" required>
                            </div>

                            <button type="submit" name="actualizar_correo" class="btn-submit">Cambiar Correo</button>
                        </form>
                    </div>

                    <!-- Pestaña de Contraseña -->
                    <div class="tab-panel <?php echo $activeTab === 'password' ? 'active' : ''; ?>" id="password-panel">
                        <h2>Cambiar Contraseña</h2>

                        <?php if (!empty($mensajes['password']['texto'])): ?>
                            <div class="alert alert-<?php echo $mensajes['password']['tipo']; ?>">
                                <?php echo $mensajes['password']['texto']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="ajustes.php?tab=password" method="post">
                            <div class="form-row">
                                <label for="current_password">Contraseña Actual</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>

                            <div class="form-row">
                                <label for="new_password">Nueva Contraseña</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>

                            <div class="form-row">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" name="actualizar_password" class="btn-submit">Cambiar Contraseña</button>
                        </form>
                    </div>

                    <!-- Pestaña de Direcciones -->
                    <div class="tab-panel <?php echo $activeTab === 'direcciones' ? 'active' : ''; ?>" id="direcciones-panel">
                        <h2>Mis Direcciones</h2>

                        <?php if ($mensajes['direccion']['texto']): ?>
                            <div class="form-message <?php echo $mensajes['direccion']['tipo']; ?>">
                                <?php echo $mensajes['direccion']['texto']; ?>
                            </div>
                        <?php endif; ?>

                        <button type="button" class="btn-add-address" id="btn-add-address">
                            <i class="fas fa-plus"></i> Añadir Nueva Dirección
                        </button>

                        <div class="address-list">
                            <?php if (empty($direcciones)): ?>
                                <p>No tienes direcciones guardadas.</p>
                            <?php else: ?>
                                <?php foreach ($direcciones as $direccion): ?>
                                    <div class="address-card">
                                        <div class="address-actions">
                                            <button type="button" class="btn-edit" data-id="<?php echo $direccion['ID_Direccion']; ?>"
                                                data-calle="<?php echo htmlspecialchars($direccion['calle']); ?>"
                                                data-numero="<?php echo htmlspecialchars($direccion['numero']); ?>"
                                                data-codigo="<?php echo htmlspecialchars($direccion['codigoPostal']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn-delete" data-id="<?php echo $direccion['ID_Direccion']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="address-details">
                                            <p><strong>Calle:</strong> <?php echo htmlspecialchars($direccion['calle']); ?></p>
                                            <p><strong>Número:</strong> <?php echo htmlspecialchars($direccion['numero']); ?></p>
                                            <p><strong>Código Postal:</strong> <?php echo htmlspecialchars($direccion['codigoPostal']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para añadir/editar dirección -->
    <div class="modal" id="address-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Añadir Dirección</h2>
                <button type="button" class="modal-close" id="modal-close">&times;</button>
            </div>
            <form action="ajustes.php?tab=direcciones" method="post" id="address-form">
                <input type="hidden" name="id_direccion" id="id_direccion" value="">

                <div class="form-row">
                    <label for="calle">Calle</label>
                    <input type="text" id="calle" name="calle" required>
                </div>

                <div class="form-row">
                    <label for="numero">Número</label>
                    <input type="text" id="numero" name="numero" required>
                </div>

                <div class="form-row">
                    <label for="codigo_postal">Código Postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="modal-cancel">Cancelar</button>
                    <button type="submit" name="guardar_direccion" class="btn-submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar dirección -->
    <div class="modal" id="confirm-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirmar Eliminación</h2>
                <button type="button" class="modal-close" id="confirm-close">&times;</button>
            </div>
            <p>¿Estás seguro de que quieres eliminar esta dirección?</p>
            <form action="ajustes.php?tab=direcciones" method="post">
                <input type="hidden" name="id_direccion" id="confirm_id_direccion" value="">

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="confirm-cancel">Cancelar</button>
                    <button type="submit" name="eliminar_direccion" class="btn-submit">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Tu tienda especializada en videojuegos, consolas y revistas retro.</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="about.php">Sobre nosotros</a></li>
                        <li><a href="contact.php">Contacto</a></li>
                        <li><a href="faq.php">Preguntas frecuentes</a></li>
                        <li><a href="envios.php">Política de envíos</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Retro, 123, Ciudad</p>
                    <p><i class="fas fa-phone"></i> +34 923 456 789</p>
                    <p><i class="fas fa-envelope"></i> info@retrogamesstore.com</p>
                </div>
                <div class="footer-column">
                    <h3>Síguenos</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> RetroGames Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cambio de pestañas
            const tabs = document.querySelectorAll('.settings-tab');
            const panels = document.querySelectorAll('.tab-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetPanel = this.getAttribute('data-tab');

                    // Actualizar pestañas
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Actualizar paneles
                    panels.forEach(panel => {
                        panel.classList.remove('active');
                        if (panel.id === targetPanel + '-panel') {
                            panel.classList.add('active');
                        }
                    });

                    // Actualizar URL sin recargar
                    history.replaceState(null, null, `?tab=${targetPanel}`);
                });
            });

            // Control de menú desplegable
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

            // Modal de dirección
            const addressModal = document.getElementById('address-modal');
            const confirmModal = document.getElementById('confirm-modal');
            const addAddressBtn = document.getElementById('btn-add-address');
            const modalClose = document.getElementById('modal-close');
            const modalCancel = document.getElementById('modal-cancel');
            const confirmClose = document.getElementById('confirm-close');
            const confirmCancel = document.getElementById('confirm-cancel');
            const modalTitle = document.getElementById('modal-title');
            const addressForm = document.getElementById('address-form');
            const idDireccionInput = document.getElementById('id_direccion');
            const calleInput = document.getElementById('calle');
            const numeroInput = document.getElementById('numero');
            const codigoPostalInput = document.getElementById('codigo_postal');

            // Abrir modal para añadir dirección
            addAddressBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Añadir Dirección';
                idDireccionInput.value = '';
                addressForm.reset();
                addressModal.classList.add('show');
            });

            // Cerrar modal de dirección
            modalClose.addEventListener('click', function() {
                addressModal.classList.remove('show');
            });

            modalCancel.addEventListener('click', function() {
                addressModal.classList.remove('show');
            });

            // Editar dirección
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const calle = this.getAttribute('data-calle');
                    const numero = this.getAttribute('data-numero');
                    const codigo = this.getAttribute('data-codigo');

                    modalTitle.textContent = 'Editar Dirección';
                    idDireccionInput.value = id;
                    calleInput.value = calle;
                    numeroInput.value = numero;
                    codigoPostalInput.value = codigo;

                    addressModal.classList.add('show');
                });
            });

            // Eliminar dirección
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    document.getElementById('confirm_id_direccion').value = id;
                    confirmModal.classList.add('show');
                });
            });

            // Cerrar modal de confirmación
            confirmClose.addEventListener('click', function() {
                confirmModal.classList.remove('show');
            });

            confirmCancel.addEventListener('click', function() {
                confirmModal.classList.remove('show');
            });

            // Cerrar modales al hacer clic fuera
            window.addEventListener('click', function(event) {
                if (event.target === addressModal) {
                    addressModal.classList.remove('show');
                }
                if (event.target === confirmModal) {
                    confirmModal.classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>