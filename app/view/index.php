<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Sesion</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>


    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" value="1">Iniciar Sesión</button>
        </form>
        <p class="register-link">¿Aún no estás registrado? <a href="register.php">Regístrate</a></p>
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
            echo "<h3>Usuario o contraseña incorrectos.</h3>";
        }
    }

    ?>
</body>

</html>