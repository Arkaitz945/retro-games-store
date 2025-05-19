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
    <title>Preguntas Frecuentes - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .faq-header h1 {
            color: #2e294e;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .faq-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .faq-categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .category-btn {
            background-color: #f5f5f5;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-btn:hover,
        .category-btn.active {
            background-color: #2e294e;
            color: white;
        }

        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            background-color: #f9f9f9;
            padding: 15px 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question:hover {
            background-color: #f1f1f1;
        }

        .faq-question i {
            transition: transform 0.3s ease;
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-question {
            background-color: #e9e9e9;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }

        .faq-category {
            display: none;
        }

        .faq-category.active {
            display: block;
        }

        .faq-contact {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }

        .faq-contact h3 {
            margin-bottom: 15px;
            color: #2e294e;
        }

        .btn-contact {
            display: inline-block;
            background-color: #2e294e;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .btn-contact:hover {
            background-color: #3d366a;
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
        <div class="faq-container">
            <div class="faq-header">
                <h1>Preguntas Frecuentes</h1>
                <p>Encuentra respuestas a las preguntas más comunes sobre nuestra tienda y productos retro</p>
            </div>

            <div class="faq-categories">
                <button class="category-btn active" data-category="general">General</button>
                <button class="category-btn" data-category="productos">Productos</button>
                <button class="category-btn" data-category="pedidos">Pedidos y Pagos</button>
                <button class="category-btn" data-category="envios">Envíos</button>
                <button class="category-btn" data-category="devoluciones">Devoluciones</button>
            </div>

            <!-- Preguntas generales -->
            <div class="faq-category active" id="general">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué es RetroGames Store? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>RetroGames Store es una tienda especializada en videojuegos, consolas y revistas retro. Nos dedicamos a ofrecer productos nostálgicos de diversas épocas, principalmente desde los años 80 hasta principios de los 2000. Nuestro objetivo es preservar la historia del videojuego y permitir a los aficionados revivir o descubrir los clásicos.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cómo puedo contactar con el servicio de atención al cliente? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Puedes contactar con nuestro equipo de atención al cliente de varias formas:</p>
                        <ul>
                            <li>Por teléfono: +34 923 456 789 (Lunes a Viernes, 9:00 - 18:00)</li>
                            <li>Por email: info@retrogamesstore.com</li>
                            <li>A través del <a href="contact.php">formulario de contacto</a> en nuestra web</li>
                            <li>Visitando nuestra tienda física en Calle Retro, 123, Ciudad</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Tenéis tienda física? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, contamos con una tienda física ubicada en Calle Retro, 123, en el centro de la ciudad. Nuestro horario de apertura es de lunes a viernes de 10:00 a 20:00 y sábados de 10:00 a 14:00. Te invitamos a visitarnos para ver nuestro amplio catálogo de productos retro en persona.</p>
                    </div>
                </div>
            </div>

            <!-- Preguntas sobre productos -->
            <div class="faq-category" id="productos">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cómo se determina el estado de los productos? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Clasificamos el estado de nuestros productos en varias categorías:</p>
                        <ul>
                            <li><strong>Excelente:</strong> Producto en condiciones casi perfectas, con mínimos signos de uso.</li>
                            <li><strong>Muy bueno:</strong> Producto en buen estado con algunos signos leves de uso.</li>
                            <li><strong>Bueno:</strong> Producto funcional con signos evidentes de uso pero sin daños importantes.</li>
                            <li><strong>Aceptable:</strong> Producto funcional con signos claros de uso y posibles defectos estéticos.</li>
                        </ul>
                        <p>Todos nuestros productos son probados antes de ponerlos a la venta para garantizar su funcionamiento.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Los juegos incluyen manual y caja original? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>En la descripción de cada producto indicamos si incluye caja original y/o manual de instrucciones. Muchos de nuestros juegos vienen completos con ambos elementos, pero algunos pueden venir solo con la caja o únicamente el cartucho/disco. Siempre especificamos esta información para que puedas tomar una decisión informada.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Las consolas vienen con cables y mandos? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, todas nuestras consolas incluyen al menos los cables necesarios para su funcionamiento (alimentación y video) y un mando. En la descripción de cada consola detallamos exactamente lo que incluye el pack. Si buscas mandos adicionales, puedes adquirirlos por separado en nuestra sección de accesorios.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Ofrecéis garantía en los productos? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Ofrecemos una garantía de 3 meses en todas nuestras consolas y dispositivos electrónicos que cubre defectos de funcionamiento. Esta garantía no cubre daños por mal uso o desgaste natural debido a la antigüedad de los productos. Para los videojuegos y revistas, ofrecemos 15 días de garantía por defectos no declarados en la descripción.</p>
                    </div>
                </div>
            </div>

            <!-- Preguntas sobre pedidos y pagos -->
            <div class="faq-category" id="pedidos">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué métodos de pago aceptáis? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Aceptamos los siguientes métodos de pago:</p>
                        <ul>
                            <li>Tarjetas de crédito/débito (Visa, Mastercard, American Express)</li>
                            <li>PayPal</li>
                            <li>Transferencia bancaria</li>
                            <li>Bizum (solo para clientes en España)</li>
                        </ul>
                        <p>El pago debe realizarse por completo antes del envío de los productos.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cómo puedo hacer seguimiento de mi pedido? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Una vez que tu pedido haya sido enviado, recibirás un email con un número de seguimiento y un enlace a la web de la empresa de transporte. También puedes seguir el estado de tu pedido iniciando sesión en tu cuenta y visitando la sección "Mis Pedidos" donde encontrarás el historial completo y estado actual.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Puedo modificar o cancelar mi pedido? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Puedes modificar o cancelar tu pedido siempre que aún no haya sido procesado. Para hacerlo, contacta con nuestro servicio de atención al cliente lo antes posible indicando tu número de pedido. Una vez que el pedido pasa al estado "Procesando" o "Enviado", ya no es posible realizar modificaciones.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Emitís factura por las compras? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, emitimos factura por todas las compras. La factura electrónica se envía automáticamente al email que hayas proporcionado durante el proceso de compra. Si necesitas una factura con datos fiscales específicos, puedes solicitarlo durante el proceso de compra o contactar con nuestro servicio de atención al cliente.</p>
                    </div>
                </div>
            </div>

            <!-- Preguntas sobre envíos -->
            <div class="faq-category" id="envios">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cuáles son los costes de envío? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Los costes de envío varían según el destino y el peso del paquete:</p>
                        <ul>
                            <li>España peninsular: 4,95€ (gratis para pedidos superiores a 50€)</li>
                            <li>Baleares: 7,95€ (gratis para pedidos superiores a 70€)</li>
                            <li>Canarias, Ceuta y Melilla: 14,95€ (gratis para pedidos superiores a 100€)</li>
                            <li>Unión Europea: desde 9,95€ (gratis para pedidos superiores a 100€)</li>
                            <li>Resto del mundo: desde 19,95€</li>
                        </ul>
                        <p>Puedes consultar el coste exacto durante el proceso de compra antes de confirmar el pedido.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cuánto tiempo tarda en llegar mi pedido? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Los tiempos de entrega estimados son:</p>
                        <ul>
                            <li>España peninsular: 24-48 horas laborables</li>
                            <li>Baleares: 2-3 días laborables</li>
                            <li>Canarias, Ceuta y Melilla: 3-6 días laborables</li>
                            <li>Unión Europea: 3-7 días laborables</li>
                            <li>Resto del mundo: 7-15 días laborables</li>
                        </ul>
                        <p>Estos plazos pueden variar en periodos de alta demanda o por circunstancias ajenas a nuestro control.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Realizáis envíos internacionales? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sí, realizamos envíos a la mayoría de países. No obstante, para algunos destinos puede haber restricciones dependiendo de las normativas aduaneras. Los gastos de aduana e impuestos adicionales corren a cargo del cliente. Te recomendamos consultar la normativa de importación de tu país antes de realizar el pedido.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué empresas de transporte utilizáis? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Trabajamos principalmente con SEUR, GLS y Correos Express para envíos nacionales. Para envíos internacionales utilizamos DHL y FedEx. Seleccionamos la empresa más adecuada según el destino y características del pedido para garantizar la mejor experiencia de entrega.</p>
                    </div>
                </div>
            </div>

            <!-- Preguntas sobre devoluciones -->
            <div class="faq-category" id="devoluciones">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cuál es la política de devoluciones? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Ofrecemos un periodo de 14 días naturales desde la recepción del pedido para realizar devoluciones. Los productos deben estar en el mismo estado en que se entregaron, con todos sus componentes y embalaje original. Para iniciar una devolución, debes contactar con nuestro servicio de atención al cliente indicando el número de pedido y el motivo de la devolución.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Quién cubre los gastos de envío en las devoluciones? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Si la devolución se debe a un defecto del producto o error por nuestra parte, nosotros cubriremos los gastos de envío. Si la devolución se realiza por cambio de opinión u otros motivos ajenos a nuestra responsabilidad, los gastos de envío correrán a cargo del cliente. En cualquier caso, te proporcionaremos instrucciones detalladas sobre cómo proceder con la devolución.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Cómo se realizan los reembolsos? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Los reembolsos se realizan utilizando el mismo método de pago que usaste para la compra original. El tiempo de procesamiento varía según el método de pago:</p>
                        <ul>
                            <li>Tarjetas de crédito/débito: 3-7 días hábiles</li>
                            <li>PayPal: 1-3 días hábiles</li>
                            <li>Transferencia bancaria: 3-5 días hábiles</li>
                            <li>Bizum: 1-3 días hábiles</li>
                        </ul>
                        <p>El reembolso se procesará una vez hayamos recibido y verificado el estado de los productos devueltos.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué hago si recibo un producto defectuoso? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Si recibes un producto defectuoso, contacta con nuestro servicio de atención al cliente dentro de las 48 horas siguientes a la recepción, adjuntando fotos o video que muestren el defecto. Evaluaremos el caso y, dependiendo de la situación, te ofreceremos un reemplazo, reparación o reembolso. En estos casos, nos haremos cargo de los gastos de envío para la devolución.</p>
                    </div>
                </div>
            </div>

            <div class="faq-contact">
                <h3>¿No has encontrado respuesta a tu pregunta?</h3>
                <p>Nuestro equipo de atención al cliente estará encantado de ayudarte con cualquier consulta adicional que puedas tener.</p>
                <a href="contact.php" class="btn-contact">Contactar con nosotros</a>
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

            // JavaScript para las preguntas desplegables
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');

                question.addEventListener('click', () => {
                    item.classList.toggle('active');
                });
            });

            // JavaScript para las categorías
            const categoryBtns = document.querySelectorAll('.category-btn');
            const categories = document.querySelectorAll('.faq-category');

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const category = btn.getAttribute('data-category');

                    // Activar botón seleccionado
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Mostrar categoría seleccionada
                    categories.forEach(cat => {
                        cat.classList.remove('active');
                        if (cat.id === category) {
                            cat.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>