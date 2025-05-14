<?php
// Archivo de prueba para envío de correos
require_once "../utils/MailService.php";

// Establece reportar todos los errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Dirección de correo para probar
$testEmail = isset($_POST['email']) ? $_POST['email'] : '';
$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($testEmail)) {
    // Crear una instancia del servicio de correo con depuración habilitada
    $mailService = new MailService(true);

    try {
        // Intentar enviar un correo de prueba
        $envioExitoso = $mailService->sendEmail(
            $testEmail,
            'Usuario de Prueba',
            'Prueba de correo desde RetroGames Store',
            '<h1>Correo de prueba</h1><p>Este es un correo de prueba enviado a las ' . date('H:i:s') . '</p>'
        );

        $resultado = [
            'exito' => $envioExitoso,
            'mensaje' => $envioExitoso
                ? 'Correo enviado correctamente. Revisa tu bandeja de entrada (y carpeta de spam).'
                : 'Error al enviar el correo. Revisa los logs para más detalles.'
        ];
    } catch (Exception $e) {
        $resultado = [
            'exito' => false,
            'mensaje' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Obtener el contenido del archivo de log si existe
$logContent = '';
$logFile = __DIR__ . '/../logs/mail.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    // Limitar a las últimas 50 líneas
    $lines = explode("\n", $logContent);
    if (count($lines) > 50) {
        $lines = array_slice($lines, -50);
        $logContent = implode("\n", $lines);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Envío de Correo - RetroGames Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2e294e;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #4caf50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .log-container {
            margin-top: 30px;
        }

        .log-container h3 {
            color: #2e294e;
        }

        .log {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 0.9em;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }

        .config-info {
            margin-top: 30px;
            background: #e7f3fe;
            padding: 15px;
            border-radius: 4px;
            border-left: 5px solid #2196F3;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Prueba de Envío de Correo</h1>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Correo electrónico para la prueba:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($testEmail); ?>" required>
            </div>
            <button type="submit">Enviar correo de prueba</button>
        </form>

        <?php if ($resultado): ?>
            <div class="result <?php echo $resultado['exito'] ? 'success' : 'error'; ?>">
                <?php echo $resultado['mensaje']; ?>
            </div>
        <?php endif; ?>

        <div class="config-info">
            <h3>Información de Configuración</h3>
            <p><strong>Servidor SMTP:</strong> smtp.gmail.com</p>
            <p><strong>Puerto:</strong> 587</p>
            <p><strong>Seguridad:</strong> TLS</p>
            <p><strong>Correo remitente:</strong> retrogamesstore4@gmail.com</p>
            <p><strong>Autenticación:</strong> Habilitada</p>
        </div>

        <div class="log-container">
            <h3>Registro de actividad (últimas 50 líneas)</h3>
            <div class="log"><?php echo htmlspecialchars($logContent ?: 'No hay registros disponibles.'); ?></div>
        </div>

        <a href="home.php" class="back-link">Volver al inicio</a>
    </div>
</body>

</html>