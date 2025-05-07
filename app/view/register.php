<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to home
if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}

require_once "../controller/UsuarioController.php";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);

    $errors = [];

    // Validate inputs
    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Formato de correo electrónico inválido";
    }

    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (empty($nombre)) {
        $errors[] = "El nombre es obligatorio";
    }

    if (empty($apellidos)) {
        $errors[] = "Los apellidos son obligatorios";
    }

    // If no errors, try to register
    if (empty($errors)) {
        $usuarioController = new UsuarioController();
        $result = $usuarioController->registrarUsuario($email, $password, $nombre, $apellidos);

        if ($result) {
            // Get user data and log them in
            $usuarioData = $usuarioController->loginUsuario($email, $password);
            if ($usuarioData) {
                $_SESSION['usuario'] = $usuarioData[0]['nombre'];
                $_SESSION['id'] = $usuarioData[0]['ID_Usuario'];
                $_SESSION['admin'] = $usuarioData[0]['EsAdmin'];
                header("Location: home.php");
                exit();
            }
        } else {
            $errors[] = "El correo electrónico ya está registrado o ocurrió un error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Retro Games Store</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-link {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .login-link a {
            color: #4caf50;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Registro de Usuario</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?php echo isset($apellidos) ? $apellidos : ''; ?>" required>
            </div>
            <button type="submit" name="register" value="1">Registrarse</button>
        </form>
        <p class="login-link">¿Ya tienes cuenta? <a href="index.php">Iniciar Sesión</a></p>
    </div>
</body>

</html>