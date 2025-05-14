<?php

// Usar Composer autoload si existe
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    // Cargar manualmente las clases de PHPMailer (usar ruta adecuada)
    require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
}

// Importar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService
{
    private $fromEmail;
    private $fromName;
    private $smtpPassword;
    private $debug = false;

    public function __construct($debug = false)
    {
        // Cargar la configuración del correo
        $configPath = __DIR__ . '/../config/mail_config.php';

        if (!file_exists($configPath)) {
            throw new Exception("No se pudo encontrar el archivo de configuración de correo");
        }

        $config = require $configPath;

        $this->fromEmail = $config['email'];
        $this->fromName = $config['name'];
        $this->smtpPassword = $config['password'];
        $this->debug = $debug;

        // Crear directorio de logs si no existe
        $logDir = __DIR__ . '/../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Función de registro de actividad para depurar problemas
     */
    private function log($message)
    {
        $logFile = __DIR__ . '/../logs/mail.log';
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents(
            $logFile,
            "$timestamp $message" . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Envía un correo electrónico usando PHPMailer y Gmail
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlMessage, $textMessage = '')
    {
        // Crear una instancia de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Registrar el intento de envío
            $this->log("Intentando enviar correo a: $toEmail con asunto: $subject");

            // Activar depuración detallada si está habilitada
            if ($this->debug) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = function ($str, $level) {
                    $this->log("DEBUG: $str");
                };
            }

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->fromEmail;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración para Gmail (pueden ser necesarios ajustes adicionales)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Remitente y destinatario
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo($this->fromEmail, $this->fromName);

            // Contenido del email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            $mail->CharSet = 'UTF-8';

            if (!empty($textMessage)) {
                $mail->AltBody = $textMessage;
            } else {
                // Si no hay texto alternativo, crear uno básico eliminando las etiquetas HTML
                $mail->AltBody = strip_tags($htmlMessage);
            }

            // Enviar el email
            $result = $mail->send();
            $this->log("Correo enviado correctamente a: $toEmail");
            return $result;
        } catch (Exception $e) {
            // Registrar el error de forma detallada
            $errorMsg = "Error al enviar correo a $toEmail: " . $mail->ErrorInfo;
            $this->log("ERROR: $errorMsg");
            error_log($errorMsg);
            return false;
        }
    }

    /**
     * Envía un correo de bienvenida a un nuevo usuario
     *
     * @param string $email Email del usuario
     * @param string $nombre Nombre del usuario
     * @return boolean Resultado del envío
     */
    public function sendWelcomeEmail($email, $nombre)
    {
        $subject = '¡Bienvenido a RetroGames Store!';

        $htmlMessage = "
        <html>
        <head>
            <title>Bienvenido a RetroGames Store</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { width: 100%; max-width: 600px; margin: 0 auto; }
                .header { background-color: #2e294e; padding: 20px; text-align: center; color: white; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 0.8em; }
                .btn { display: inline-block; background-color: #4caf50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Bienvenido a RetroGames Store!</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>¡Gracias por registrarte en RetroGames Store! Estamos encantados de que te hayas unido a nuestra comunidad de amantes de los videojuegos retro.</p>
                    <p>En nuestra tienda encontrarás una amplia selección de videojuegos, consolas y accesorios clásicos que te harán revivir esos momentos nostálgicos.</p>
                    <p>No dudes en contactarnos si tienes alguna pregunta o necesitas ayuda.</p>
                    <p style='text-align:center; margin-top: 30px;'>
                        <a href='http://localhost/retro-games-store/app/view/home.php' class='btn'>Visitar la tienda</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>RetroGames Store - Tu tienda de videojuegos retro</p>
                    <p>Calle Retro, 123, Ciudad - +34 923 456 789</p>
                    <p>Este correo fue enviado a $email</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($email, $nombre, $subject, $htmlMessage);
    }

    /**
     * Envía un correo notificando un inicio de sesión
     *
     * @param string $email Email del usuario
     * @param string $nombre Nombre del usuario
     * @param array $loginInfo Información adicional del inicio de sesión
     * @return boolean Resultado del envío
     */
    public function sendLoginNotificationEmail($email, $nombre, $loginInfo = [])
    {
        $fecha = date('d/m/Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        $navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        $subject = 'Inicio de sesión detectado - RetroGames Store';

        $htmlMessage = "
        <html>
        <head>
            <title>Inicio de sesión en RetroGames Store</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { width: 100%; max-width: 600px; margin: 0 auto; }
                .header { background-color: #2e294e; padding: 20px; text-align: center; color: white; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 0.8em; }
                .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .info-table td { padding: 8px; border-bottom: 1px solid #eee; }
                .info-table tr:last-child td { border-bottom: none; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Inicio de Sesión Detectado</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>Hemos detectado un inicio de sesión reciente en tu cuenta de RetroGames Store.</p>
                    <p>Detalles del inicio de sesión:</p>
                    <table class='info-table'>
                        <tr>
                            <td><strong>Fecha y hora:</strong></td>
                            <td>$fecha</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección IP:</strong></td>
                            <td>$ip</td>
                        </tr>
                        <tr>
                            <td><strong>Navegador:</strong></td>
                            <td>$navegador</td>
                        </tr>
                    </table>
                    <p>Si no reconoces esta actividad, por favor cambia tu contraseña inmediatamente y contacta con nuestro equipo de soporte.</p>
                </div>
                <div class='footer'>
                    <p>RetroGames Store - Tu tienda de videojuegos retro</p>
                    <p>Calle Retro, 123, Ciudad - +34 923 456 789</p>
                    <p>Este correo fue enviado a $email</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($email, $nombre, $subject, $htmlMessage);
    }
}
