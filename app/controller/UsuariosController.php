<?php

require_once "../model/UsuariosModel.php";
require_once "../utils/MailService.php";

class UsuariosController
{
    private $usuariosModel;
    private $mailService;

    public function __construct()
    {
        $this->usuariosModel = new UsuariosModel();
        $this->mailService = new MailService();
    }

    /**
     * Registra un nuevo usuario y envía un correo de bienvenida
     *
     * @param string $nombre Nombre del usuario
     * @param string $apellidos Apellidos del usuario
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return array Resultado del registro
     */
    public function registrarUsuario($nombre, $apellidos, $email, $password)
    {
        // Verificar si el email ya está registrado
        if ($this->usuariosModel->emailExiste($email)) {
            return [
                'success' => false,
                'message' => 'Este email ya está registrado en el sistema'
            ];
        }

        // Registrar el usuario
        $resultado = $this->usuariosModel->crearUsuario($nombre, $apellidos, $email, $password);

        if ($resultado['success']) {
            // Enviar correo de bienvenida
            try {
                $envioCorreo = $this->mailService->sendWelcomeEmail($email, $nombre);
                $mensajeCorreo = $envioCorreo ? 'Hemos enviado un correo de bienvenida a tu dirección de email.' : 'No se pudo enviar el correo de bienvenida, pero tu cuenta ha sido creada correctamente.';
            } catch (Exception $e) {
                // Si hay error al enviar el correo, lo registramos pero continuamos
                error_log('Error al enviar correo de bienvenida: ' . $e->getMessage());
                $mensajeCorreo = 'Tu cuenta ha sido creada correctamente, pero hubo un problema al enviar el correo de bienvenida.';
            }

            return [
                'success' => true,
                'message' => 'Usuario registrado correctamente. ' . $mensajeCorreo,
                'userId' => $resultado['userId']
            ];
        } else {
            return $resultado;
        }
    }

    /**
     * Inicia sesión y envía notificación por correo
     *
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @param boolean $notificarLogin Si se debe enviar notificación de inicio de sesión
     * @return array Resultado del inicio de sesión
     */
    public function login($email, $password, $notificarLogin = true)
    {
        $resultado = $this->usuariosModel->verificarLogin($email, $password);

        if ($resultado['success']) {
            // Iniciar sesión
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['id'] = $resultado['usuario']['ID_U'];
            $_SESSION['usuario'] = $resultado['usuario']['nombre'];
            $_SESSION['email'] = $resultado['usuario']['correo'];
            $_SESSION['admin'] = $resultado['usuario']['admin'];

            // Enviar notificación de inicio de sesión si está activado
            if ($notificarLogin) {
                try {
                    // Activar debug para más información
                    $this->mailService = new MailService(true);

                    $envioCorreo = $this->mailService->sendLoginNotificationEmail(
                        $resultado['usuario']['correo'],
                        $resultado['usuario']['nombre']
                    );

                    if ($envioCorreo) {
                        // Si el correo se envió correctamente
                        $_SESSION['mail_status'] = 'Se ha enviado una notificación de inicio de sesión a tu correo.';
                    } else {
                        // Si hubo un problema al enviar el correo
                        $_SESSION['mail_status'] = 'No se pudo enviar la notificación de inicio de sesión.';
                        error_log("No se pudo enviar notificación de login a: {$resultado['usuario']['correo']}");
                    }
                } catch (Exception $e) {
                    // Si hay error al enviar el correo, lo registramos
                    $_SESSION['mail_status'] = 'Error al enviar la notificación de inicio de sesión.';
                    error_log('Error al enviar notificación de login: ' . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'message' => 'Inicio de sesión correcto',
                'usuario' => $resultado['usuario']
            ];
        } else {
            return $resultado;
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Eliminar todas las variables de sesión
        $_SESSION = array();

        // Si se está usando un cookie de sesión, eliminarlo también
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ];
    }

    /**
     * Obtiene los datos del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Datos del usuario
     */
    public function getUsuario($idUsuario)
    {
        return $this->usuariosModel->getUsuarioById($idUsuario);
    }

    /**
     * Actualiza los datos del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param array $datos Datos a actualizar
     * @return array Resultado de la actualización
     */
    public function actualizarUsuario($idUsuario, $datos)
    {
        return $this->usuariosModel->actualizarUsuario($idUsuario, $datos);
    }

    /**
     * Cambia la contraseña del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $passwordActual Contraseña actual
     * @param string $passwordNueva Nueva contraseña
     * @return array Resultado del cambio
     */
    public function cambiarPassword($idUsuario, $passwordActual, $passwordNueva)
    {
        // Primero verificamos que la contraseña actual sea correcta
        $verificacion = $this->usuariosModel->verificarPassword($idUsuario, $passwordActual);

        if (!$verificacion['success']) {
            return $verificacion;
        }

        // Si la contraseña es correcta, procedemos a cambiarla
        return $this->usuariosModel->cambiarPassword($idUsuario, $passwordNueva);
    }
}
