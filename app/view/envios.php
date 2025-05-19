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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Envíos - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .shipping-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .shipping-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .shipping-header h1 {
            color: #2e294e;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .shipping-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .shipping-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .shipping-section {
            margin-bottom: 30px;
        }

        .shipping-section:last-child {
            margin-bottom: 0;
        }

        .shipping-section h2 {
            color: #2e294e;
            font-size: 1.6rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .shipping-section p {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .shipping-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .shipping-table th,
        .shipping-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .shipping-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .shipping-table tr:last-child td {
            border-bottom: none;
        }

        .shipping-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #664d03;
        }

        .shipping-cta {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .shipping-cta p {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .btn-primary {
            display: inline-block;
            background-color: #2e294e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #3d366a;
        }

        @media (max-width: 768px) {
            .shipping-table {
                display: block;
                overflow-x: auto;
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
        <div class="shipping-container">
            <div class="shipping-header">
                <h1>Política de Envíos</h1>
                <p>Información sobre nuestros servicios de envío, plazos de entrega y costes</p>
            </div>

            <div class="shipping-content">
                <div class="shipping-section">
                    <h2>Costes de Envío</h2>
                    <p>En RetroGames Store nos esforzamos por ofrecer opciones de envío asequibles y transparentes. Los costes de envío se calculan en función del destino y del peso total del pedido.</p>

                    <table class="shipping-table">
                        <thead>
                            <tr>
                                <th>Destino</th>
                                <th>Coste de Envío</th>
                                <th>Envío Gratuito</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>España peninsular</td>
                                <td>4,95€</td>
                                <td>Para pedidos superiores a 50€</td>
                            </tr>
                            <tr>
                                <td>Islas Baleares</td>
                                <td>7,95€</td>
                                <td>Para pedidos superiores a 70€</td>
                            </tr>
                            <tr>
                                <td>Islas Canarias, Ceuta y Melilla</td>
                                <td>14,95€</td>
                                <td>Para pedidos superiores a 100€</td>
                            </tr>
                            <tr>
                                <td>Unión Europea</td>
                                <td>Desde 9,95€*</td>
                                <td>Para pedidos superiores a 100€</td>
                            </tr>
                            <tr>
                                <td>Resto del mundo</td>
                                <td>Desde 19,95€*</td>
                                <td>No disponible</td>
                            </tr>
                        </tbody>
                    </table>

                    <p><small>* El coste exacto para envíos internacionales depende del país de destino y el peso del paquete. El precio final se mostrará durante el proceso de compra antes de confirmar el pedido.</small></p>

                    <div class="shipping-note">
                        <p><strong>Nota sobre impuestos y aduanas:</strong> Para envíos fuera de la Unión Europea, los impuestos, aranceles y tasas aduaneras no están incluidos en el precio y correrán a cargo del cliente. Estos cargos varían según el país de destino y RetroGames Store no tiene control sobre ellos.</p>
                    </div>
                </div>

                <div class="shipping-section">
                    <h2>Plazos de Entrega</h2>
                    <p>Procesamos todos los pedidos en un plazo de 1-2 días laborables tras recibir el pago. Los plazos de entrega estimados una vez enviado el pedido son los siguientes:</p>

                    <table class="shipping-table">
                        <thead>
                            <tr>
                                <th>Destino</th>
                                <th>Plazo de Entrega Estimado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>España peninsular</td>
                                <td>24-48 horas laborables</td>
                            </tr>
                            <tr>
                                <td>Islas Baleares</td>
                                <td>2-3 días laborables</td>
                            </tr>
                            <tr>
                                <td>Islas Canarias, Ceuta y Melilla</td>
                                <td>3-6 días laborables</td>
                            </tr>
                            <tr>
                                <td>Unión Europea</td>
                                <td>3-7 días laborables</td>
                            </tr>
                            <tr>
                                <td>Resto del mundo</td>
                                <td>7-15 días laborables</td>
                            </tr>
                        </tbody>
                    </table>

                    <p>Ten en cuenta que estos plazos son estimativos y pueden verse afectados por circunstancias externas como condiciones meteorológicas adversas, festividades, huelgas o retrasos en aduanas para envíos internacionales.</p>
                </div>

                <div class="shipping-section">
                    <h2>Empresas de Transporte</h2>
                    <p>Trabajamos con transportistas de confianza para asegurar que tus productos retro lleguen en perfecto estado:</p>

                    <ul>
                        <li><strong>Envíos nacionales:</strong> SEUR, GLS y Correos Express</li>
                        <li><strong>Envíos internacionales:</strong> DHL y FedEx</li>
                    </ul>

                    <p>Seleccionamos la empresa de transporte más adecuada según el destino y las características del pedido para garantizar la mejor experiencia de entrega.</p>
                </div>

                <div class="shipping-section">
                    <h2>Seguimiento de Pedidos</h2>
                    <p>Una vez que tu pedido haya sido enviado, recibirás un email de confirmación con la siguiente información:</p>

                    <ul>
                        <li>Número de seguimiento</li>
                        <li>Enlace a la web del transportista para seguir tu envío</li>
                        <li>Información de contacto del transportista</li>
                    </ul>

                    <p>También puedes consultar el estado de tus pedidos en cualquier momento iniciando sesión en tu cuenta y accediendo a la sección "Mis Pedidos".</p>
                </div>

                <div class="shipping-section">
                    <h2>Embalaje Especial</h2>
                    <p>En RetroGames Store sabemos lo importantes que son los productos retro para los coleccionistas. Por eso, prestamos especial atención al embalaje:</p>

                    <ul>
                        <li>Utilizamos materiales de protección específicos para cada tipo de producto</li>
                        <li>Las consolas se embalan con protección adicional en las esquinas y zonas sensibles</li>
                        <li>Los videojuegos y revistas se protegen con envoltorios rígidos para evitar daños</li>
                        <li>Los productos coleccionables y ediciones especiales se embalan individualmente</li>
                    </ul>
                </div>

                <div class="shipping-section">
                    <h2>Incidencias en la Entrega</h2>
                    <p>Si experimentas algún problema con la entrega de tu pedido:</p>

                    <ol>
                        <li>Comprueba el estado del envío a través del número de seguimiento proporcionado</li>
                        <li>Verifica que la dirección de entrega proporcionada sea correcta</li>
                        <li>Si el transportista indica que ha intentado la entrega, contacta directamente con ellos usando la información facilitada en el email de seguimiento</li>
                        <li>Si no puedes resolver la incidencia, contacta con nuestro servicio de atención al cliente lo antes posible a través de nuestro <a href="contact.php">formulario de contacto</a> o por teléfono</li>
                    </ol>

                    <p>Es importante que inspecciones el paquete en el momento de la entrega. Si detectas daños externos evidentes, anótalo en el albarán de entrega y contacta con nosotros inmediatamente.</p>
                </div>
            </div>

            <div class="shipping-cta">
                <p>¿Tienes alguna pregunta sobre nuestras políticas de envío?</p>
                <a href="contact.php" class="btn-primary">Contacta con nosotros</a>
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
        });
    </script>
</body>

</html>