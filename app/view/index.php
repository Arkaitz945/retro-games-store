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
    require_once "../controller/UsuarioController.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $campoEmailSaneado = htmlspecialchars($_POST['email']);
        $campoContraseñaSaneado = htmlspecialchars($_POST['password']);

        $usuarioValido = (new UsuarioController())->loginUsuario($campoEmailSaneado, $campoContraseñaSaneado);
        if ($usuarioValido == true) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Save user information in session
            $_SESSION['usuario'] = $usuarioValido[0]['nombre']; // Save the user's name
            $_SESSION['id'] = $usuarioValido[0]['ID_Usuario'];
            $_SESSION['admin'] = $usuarioValido[0]['EsAdmin'];
            header("Location: home.php");
            exit();
        } else {
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
    }
    ?>
</body>

</html>