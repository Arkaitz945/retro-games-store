<?php

// Script de prueba para el envío de correos

require_once __DIR__ . '/MailService.php';

// Dirección de correo de destino (la que debería recibir el correo)
$testEmail = 'abads651@gmail.com';
$testName = 'Usuario de Prueba';

// Crear instancia del servicio de correo con depuración activada
$mailService = new MailService(true);

// Intentar enviar un correo de prueba
echo "Intentando enviar correo de prueba a: $testEmail...<br>";

$subject = 'Prueba de correo - RetroGames Store';
$htmlMessage = "
<html>
<head>
    <title>Prueba de correo</title>
</head>
<body>
    <h1>Prueba de envío de correo</h1>
    <p>Este es un correo de prueba enviado desde RetroGames Store.</p>
    <p>Si estás recibiendo este correo, significa que el sistema de envío de correos está funcionando correctamente.</p>
    <p>Fecha y hora: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>
";

$result = $mailService->sendEmail($testEmail, $testName, $subject, $htmlMessage);

if ($result) {
    echo "<p style='color:green;'>✅ El correo se ha enviado correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al enviar el correo. Revisa el archivo de log para más detalles.</p>";
    echo "<p>Ruta del log: " . __DIR__ . "/../logs/mail.log</p>";
}

// Mostrar contenido del log
$logFile = __DIR__ . '/../logs/mail.log';
if (file_exists($logFile)) {
    echo "<h3>Contenido del log:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
} else {
    echo "<p>No se ha encontrado archivo de log.</p>";
}
