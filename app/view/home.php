<?php
// Start the session if not already started
session_start();

// Check if user is logged in
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Retro Games Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .user-name {
            font-weight: bold;
            color: #4caf50;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bienvenido a Retro Games Store</h1>
        <p>Hola <span class="user-name"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>, nos alegra verte de nuevo.</p>

        <p>Aquí podrás encontrar tu colección de juegos retro favoritos.</p>

        <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
            <p>Tienes acceso de administrador en esta plataforma.</p>
        <?php endif; ?>

        <p><a href="logout.php">Cerrar sesión</a></p>
    </div>
</body>

</html>