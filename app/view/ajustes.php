<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once "../controller/AjustesController.php";
require_once "../controller/CarritoController.php";

$ajustesController = new AjustesController();
$carritoController = new CarritoController();

$idUsuario = $_SESSION['id'];
$nombreUsuario = $_SESSION['usuario'];
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$cantidadCarrito = isset($_SESSION['id']) ? $carritoController->countCartItems($_SESSION['id']) : 0;

// Obtener datos del usuario
$usuario = $ajustesController->getUsuario($idUsuario);
$direcciones = $ajustesController->getDirecciones($idUsuario);

// Variable para mensajes
$mensajes = [
    'perfil' => ['tipo' => '', 'texto' => ''],
    'email' => ['tipo' => '', 'texto' => ''],
    'password' => ['tipo' => '', 'texto' => ''],
    'direccion' => ['tipo' => '', 'texto' => '']
];

// Procesar formulario de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));

    if (empty($nombre)) {
        $mensajes['perfil'] = ['tipo' => 'error', 'texto' => 'El nombre es obligatorio'];
    } else {
        $resultado = $ajustesController->actualizarPerfil($idUsuario, $nombre, $apellidos);
        $mensajes['perfil'] = [
            'tipo' => $resultado['success'] ? 'success' : 'error',
            'texto' => $resultado['message']
        ];

        if ($resultado['success']) {
            $_SESSION['usuario'] = $nombre;
            $usuario['nombre'] = $nombre;
            $usuario['apellidos'] = $apellidos;
        }
    }
}

// Procesar formulario de email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_email'])) {
    $email = htmlspecialchars(trim($_POST['email']));

    if (empty($email)) {
        $mensajes['email'] = ['tipo' => 'error', 'texto' => 'El email es obligatorio'];
    } else {
        $resultado = $ajustesController->actualizarEmail($idUsuario, $email);
        $mensajes['email'] = [
            'tipo' => $resultado['success'] ? 'success' : 'error',
            'texto' => $resultado['message']
        ];

        if ($resultado['success']) {
            $usuario['correo'] = $email;
        }
    }
}

// Procesar formulario de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $resultado = $ajustesController->actualizarContraseña($idUsuario, $currentPassword, $newPassword, $confirmPassword);
    $mensajes['password'] = [
        'tipo' => $resultado['success'] ? 'success' : 'error',
        'texto' => $resultado['message']
    ];
}

// Procesar formulario de dirección
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_direccion'])) {
    $direccion = [
        'calle' => htmlspecialchars(trim($_POST['calle'])),
        'numero' => htmlspecialchars(trim($_POST['numero'])),
        'codigoPostal' => htmlspecialchars(trim($_POST['codigo_postal'])),
        'idUsuario' => $idUsuario
    ];

    if (isset($_POST['id_direccion']) && !empty($_POST['id_direccion'])) {
        // Actualizar dirección existente
        $direccion['ID_Direccion'] = (int)$_POST['id_direccion'];
        $resultado = $ajustesController->actualizarDireccion($direccion);
    } else {
        // Añadir nueva dirección
        $resultado = $ajustesController->añadirDireccion($direccion);
    }

    $mensajes['direccion'] = [
        'tipo' => $resultado['success'] ? 'success' : 'error',
        'texto' => $resultado['message']
    ];

    if ($resultado['success']) {
        // Recargar direcciones
        $direcciones = $ajustesController->getDirecciones($idUsuario);
    }
}

// Procesar eliminación de dirección
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_direccion'])) {
    $idDireccion = (int)$_POST['id_direccion'];

    $resultado = $ajustesController->eliminarDireccion($idDireccion, $idUsuario);
    $mensajes['direccion'] = [
        'tipo' => $resultado['success'] ? 'success' : 'error',
        'texto' => $resultado['message']
    ];

    if ($resultado['success']) {
        // Recargar direcciones
        $direcciones = $ajustesController->getDirecciones($idUsuario);
    }
}

// Determinar pestaña activa
$activeTab = 'perfil';
if (isset($_GET['tab'])) {
    $allowedTabs = ['perfil', 'email', 'password', 'direcciones'];
    if (in_array($_GET['tab'], $allowedTabs)) {
        $activeTab = $_GET['tab'];
    }
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
                    <div class="settings-tab <?php echo $activeTab === 'email' ? 'active' : ''; ?>" data-tab="email">
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

                        <?php if ($mensajes['perfil']['texto']): ?>
                            <div class="form-message <?php echo $mensajes['perfil']['tipo']; ?>">
                                <?php echo $mensajes['perfil']['texto']; ?>
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

                    <!-- Pestaña de Email -->
                    <div class="tab-panel <?php echo $activeTab === 'email' ? 'active' : ''; ?>" id="email-panel">
                        <h2>Cambiar Correo Electrónico</h2>

                        <?php if ($mensajes['email']['texto']): ?>
                            <div class="form-message <?php echo $mensajes['email']['tipo']; ?>">
                                <?php echo $mensajes['email']['texto']; ?>
                            </div>
                        <?php endif; ?>

                        <form action="ajustes.php?tab=email" method="post">
                            <div class="form-row">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
                            </div>

                            <button type="submit" name="actualizar_email" class="btn-submit">Cambiar Correo</button>
                        </form>
                    </div>

                    <!-- Pestaña de Contraseña -->
                    <div class="tab-panel <?php echo $activeTab === 'password' ? 'active' : ''; ?>" id="password-panel">
                        <h2>Cambiar Contraseña</h2>

                        <?php if ($mensajes['password']['texto']): ?>
                            <div class="form-message <?php echo $mensajes['password']['tipo']; ?>">
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