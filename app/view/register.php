<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya está logueado, redirigir a home
if (isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}

// Incluir el controlador de usuarios
require_once "../controller/UsuarioController.php";

// Variables para almacenar errores y datos
$errors = [];
$form_data = [
    'nombre' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
];

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Sanitizar y validar campos
    $form_data['nombre'] = htmlspecialchars(trim($_POST['nombre']));
    $form_data['email'] = htmlspecialchars(trim($_POST['email']));
    $form_data['password'] = htmlspecialchars($_POST['password']);
    $form_data['confirm_password'] = htmlspecialchars($_POST['confirm_password']);

    // Validar nombre
    if (empty($form_data['nombre'])) {
        $errors['nombre'] = "El nombre es obligatorio";
    } elseif (strlen($form_data['nombre']) < 3) {
        $errors['nombre'] = "El nombre debe tener al menos 3 caracteres";
    }

    // Validar email
    if (empty($form_data['email'])) {
        $errors['email'] = "El email es obligatorio";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Por favor ingrese un email válido";
    } else {
        // Verificar si el email ya existe
        $usuarioController = new UsuarioController();
        if ($usuarioController->emailExiste($form_data['email'])) {
            $errors['email'] = "Este email ya está registrado";
        }
    }

    // Validar contraseña
    if (empty($form_data['password'])) {
        $errors['password'] = "La contraseña es obligatoria";
    } elseif (strlen($form_data['password']) < 6) {
        $errors['password'] = "La contraseña debe tener al menos 6 caracteres";
    }

    // Validar confirmación de contraseña
    if ($form_data['password'] !== $form_data['confirm_password']) {
        $errors['confirm_password'] = "Las contraseñas no coinciden";
    }

    // Si no hay errores, crear el usuario
    if (empty($errors)) {
        $resultado = $usuarioController->registrarUsuario(
            $form_data['nombre'],
            $form_data['email'],
            $form_data['password']
        );

        if ($resultado) {
            // Redirigir a la página de login con mensaje de éxito
            header("Location: index.php?registro=exitoso");
            exit();
        } else {
            $errors['general'] = "Error al registrar el usuario. Por favor intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RetroGames Store</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-logo">
            <i class="fas fa-gamepad"></i>
            <h1>RetroGames Store</h1>
        </div>

        <h2>Crear Cuenta</h2>
        <p class="form-description">Únete a la comunidad de coleccionistas retro</p>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group <?php echo isset($errors['nombre']) ? 'has-error' : ''; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $form_data['nombre']; ?>" required>
                <?php if (isset($errors['nombre'])): ?>
                    <span class="error"><?php echo $errors['nombre']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $form_data['email']; ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" name="register" value="1" class="btn-auth">Registrarse</button>
        </form>

        <div class="auth-links">
            ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
        </div>

        <div class="return-home">
            <a href="#"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
        </div>
    </div>
</body>

</html>