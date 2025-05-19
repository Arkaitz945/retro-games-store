<?php
// Iniciar sesión para acceder a variables de sesión
session_start();

// Incluir controlador del carrito si el usuario está logueado
if (isset($_SESSION['id'])) {
    require_once "../controller/CarritoController.php";
    $carritoController = new CarritoController();
    $cantidadCarrito = $carritoController->countCartItems($_SESSION['id']);
} else {
    $cantidadCarrito = 0;
}

$nombreUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
$esAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Procesar el formulario cuando se envía
$mensajeEnviado = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_contacto'])) {
    // Validar campos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, introduce un email válido';
    } else {
        // En un entorno real, aquí enviaríamos el email
        // Por ejemplo, usando mail() o una librería como PHPMailer

        // Simulamos éxito para el ejemplo
        $mensajeEnviado = true;

        // Limpiar los valores del formulario después de enviarlo
        $nombre = $email = $asunto = $mensaje = '';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contact-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .contact-header h1 {
            color: #2e294e;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .contact-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .contact-form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #2e294e;
            outline: none;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            background-color: #2e294e;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #3d366a;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
        }

        .info-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 20px;
        }

        .info-card h3 {
            color: #2e294e;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.4rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .contact-details {
            margin-bottom: 20px;
        }

        .contact-detail {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .contact-detail i {
            font-size: 1.2rem;
            color: #2e294e;
            margin-right: 15px;
            margin-top: 3px;
        }

        .contact-detail .detail-content {
            flex: 1;
        }

        .detail-content h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }

        .detail-content p {
            margin: 0;
            color: #666;
        }

        .store-hours {
            margin-top: 20px;
        }

        .store-hours h4 {
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #333;
        }

        .hour-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .day {
            font-weight: 500;
        }

        .social-contact {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-contact a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #2e294e;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .social-contact a:hover {
            background-color: #3d366a;
            transform: translateY(-3px);
        }

        .map-container {
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 40px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 992px) {
            .contact-content {
                grid-template-columns: 1fr;
            }

            .contact-form-container {
                order: 2;
            }

            .contact-info {
                order: 1;
            }
        }
    </style>
</head>

<body>
    <!-- Banner superior -->
    <header class="main-header">
        <div class="logo">
            <a href="home.php" class="logo-link">
                <h1><i class="fas fa-gamepad"></i> RetroGames Store</h1>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="videojuegos.php">Videojuegos</a></li>
                <li><a href="consolas.php">Consolas</a></li>
                <li><a href="revistas.php">Revistas</a></li>
                <li><a href="accesorios.php">Accesorios</a></li>
                <?php if ($esAdmin): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="user-menu">
            <?php if (isset($_SESSION['usuario'])): ?>
                <div class="user-dropdown">
                    <button class="user-btn"><i class="fas fa-user"></i> <?php echo htmlspecialchars($nombreUsuario); ?> <i class="fas fa-caret-down"></i></button>
                    <div class="dropdown-content">
                        <?php if ($esAdmin): ?>
                            <a href="admin/dashboard.php"><i class="fas fa-user-shield"></i> Panel de Administración</a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                        <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito
                            <?php if ($cantidadCarrito > 0): ?>
                                <span class="cart-badge"><?php echo $cantidadCarrito; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="ajustes.php"><i class="fas fa-cog"></i> Ajustes</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        <div class="contact-container">
            <div class="contact-header">
                <h1>Contacta con Nosotros</h1>
                <p>¿Tienes alguna pregunta o comentario? Estaremos encantados de atenderte. Rellena el formulario y te responderemos lo antes posible.</p>
            </div>

            <div class="contact-content">
                <div class="contact-form-container">
                    <?php if ($mensajeEnviado): ?>
                        <div class="alert alert-success">
                            <p><strong>¡Mensaje enviado con éxito!</strong></p>
                            <p>Gracias por contactar con nosotros. Te responderemos lo antes posible.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <p><strong>Error:</strong> <?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <form class="contact-form" method="post" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="asunto">Asunto *</label>
                            <select id="asunto" name="asunto" required>
                                <option value="" <?php echo empty($asunto) ? 'selected' : ''; ?>>Selecciona un asunto</option>
                                <option value="Consulta sobre productos" <?php echo ($asunto ?? '') === 'Consulta sobre productos' ? 'selected' : ''; ?>>Consulta sobre productos</option>
                                <option value="Información de pedidos" <?php echo ($asunto ?? '') === 'Información de pedidos' ? 'selected' : ''; ?>>Información de pedidos</option>
                                <option value="Devoluciones" <?php echo ($asunto ?? '') === 'Devoluciones' ? 'selected' : ''; ?>>Devoluciones</option>
                                <option value="Colaboraciones" <?php echo ($asunto ?? '') === 'Colaboraciones' ? 'selected' : ''; ?>>Colaboraciones</option>
                                <option value="Otro asunto" <?php echo ($asunto ?? '') === 'Otro asunto' ? 'selected' : ''; ?>>Otro asunto</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" required><?php echo htmlspecialchars($mensaje ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="enviar_contacto" class="btn-submit">Enviar mensaje</button>
                    </form>
                </div>

                <div class="contact-info">
                    <div class="info-card">
                        <h3>Información de Contacto</h3>

                        <div class="contact-details">
                            <div class="contact-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="detail-content">
                                    <h4>Dirección</h4>
                                    <p>Calle Retro, 123</p>
                                    <p>28001 Madrid, España</p>
                                </div>
                            </div>

                            <div class="contact-detail">
                                <i class="fas fa-phone"></i>
                                <div class="detail-content">
                                    <h4>Teléfono</h4>
                                    <p>+34 923 456 789</p>
                                </div>
                            </div>

                            <div class="contact-detail">
                                <i class="fas fa-envelope"></i>
                                <div class="detail-content">
                                    <h4>Email</h4>
                                    <p>info@retrogamesstore.com</p>
                                </div>
                            </div>
                        </div>

                        <div class="store-hours">
                            <h4>Horario de la Tienda</h4>
                            <div class="hour-row">
                                <span class="day">Lunes - Viernes:</span>
                                <span class="hours">10:00 - 20:00</span>
                            </div>
                            <div class="hour-row">
                                <span class="day">Sábados:</span>
                                <span class="hours">10:00 - 14:00</span>
                            </div>
                            <div class="hour-row">
                                <span class="day">Domingos y Festivos:</span>
                                <span class="hours">Cerrado</span>
                            </div>
                        </div>

                        <div class="social-contact">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12144.50975876304!2d-3.7027833711653847!3d40.41683332412076!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd42288000000001%3A0x304f858720a4b30!2sMadrid%2C%20Spain!5e0!3m2!1sen!2sus!4v1672292775255!5m2!1sen!2sus" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h3>RetroGames Store</h3>
                    <p>Tu tienda especializada en videojuegos, consolas y revistas retro.</p>
                </div>
                <div class="footer-column">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="about.php">Sobre nosotros</a></li>
                        <li><a href="contact.php">Contacto</a></li>
                        <li><a href="faq.php">Preguntas frecuentes</a></li>
                        <li><a href="envios.php">Política de envíos</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Retro, 123, Ciudad</p>
                    <p><i class="fas fa-phone"></i> +34 923 456 789</p>
                    <p><i class="fas fa-envelope"></i> info@retrogamesstore.com</p>
                </div>
                <div class="footer-column">
                    <h3>Síguenos</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> RetroGames Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // JavaScript para el menú desplegable
            const userBtn = document.querySelector('.user-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            if (userBtn && dropdownContent) {
                userBtn.addEventListener('click', function() {
                    dropdownContent.classList.toggle('show');
                });

                window.addEventListener('click', function(event) {
                    if (!event.target.matches('.user-btn') && !event.target.parentNode.matches('.user-btn')) {
                        if (dropdownContent.classList.contains('show')) {
                            dropdownContent.classList.remove('show');
                        }
                    }
                });
            }

            // Validación del formulario en el lado del cliente
            const contactForm = document.querySelector('.contact-form');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    const nombre = document.getElementById('nombre').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const asunto = document.getElementById('asunto').value;
                    const mensaje = document.getElementById('mensaje').value.trim();

                    if (!nombre || !email || !asunto || !mensaje) {
                        e.preventDefault();
                        alert('Por favor, completa todos los campos obligatorios.');
                        return false;
                    }

                    // Validación básica de email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Por favor, introduce un email válido.');
                        return false;
                    }
                });
            }
        });
    </script>
</body>

</html>