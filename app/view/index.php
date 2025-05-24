<?php
// Activar depuración para mostrar errores durante el desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos directamente para verificar antes de todo
require_once "../config/dbConnection.php";

// Prueba de conexión antes de continuar
$testConn = getDBConnection();
if (!$testConn) {
    echo '<div style="padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin: 20px;">
    <h3>Error de conexión a la base de datos</h3>
    <p>No se pudo establecer conexión con la base de datos. Por favor, verifica la configuración.</p>
    <p>Asegúrate de que:</p>
    <ul>
        <li>MySQL/MariaDB está corriendo en XAMPP</li>
        <li>La base de datos "retro_games" existe</li>
        <li>El usuario tiene permisos para acceder</li>
    </ul>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - RetroGames Store</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-logo">
            <i class="fas fa-gamepad"></i>
            <h1>RetroGames Store</h1>
        </div>

        <h2>Iniciar Sesión</h2>
        <p class="form-description">Ingresa tus credenciales para acceder a tu cuenta</p>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button type="submit" name="login" value="1" class="btn-auth">Iniciar Sesión</button>
        </form>

        <div class="auth-links">
            ¿Aún no tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </div>

        <div class="return-home">
            <a href="#"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
        </div>
    </div>

    <?php
    // Solo procesar el login si tenemos conexión a la base de datos
    if ($testConn && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $campoEmailSaneado = htmlspecialchars($_POST['email']);
        $campoContraseñaSaneado = htmlspecialchars($_POST['password']);

        // Mostrar datos para depuración
        error_log("Login attempt: Email=$campoEmailSaneado");

        require_once "../controller/UsuarioController.php";
        $usuarioController = new UsuarioController();

        try {
            $usuarioValido = $usuarioController->loginUsuario($campoEmailSaneado, $campoContraseñaSaneado);

            if ($usuarioValido) {
                // Start session if not already started
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                // Debug info
                error_log("Login successful: " . print_r($usuarioValido[0], true));

                // Save user information in session
                $_SESSION['usuario'] = $usuarioValido[0]['nombre'];
                $_SESSION['id'] = $usuarioValido[0]['ID_U'];
                $_SESSION['admin'] = $usuarioValido[0]['esAdmin'];

                header("Location: home.php");
                exit();
            } else {
                $error_message = 'Usuario o contraseña incorrectos.';
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const authContainer = document.querySelector('.auth-container');
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'alert alert-danger';
                        errorMsg.textContent = 'Usuario o contraseña incorrectos.';
                        authContainer.insertBefore(errorMsg, document.querySelector('form'));
                    });
                </script>";
            }
        } catch (Exception $e) {
            error_log("Error en el proceso de login: " . $e->getMessage());
            echo "<div class='alert alert-danger'>Error en el sistema. Por favor, intente más tarde.</div>";
        }
    }
    ?>
</body>

</html>