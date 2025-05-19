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
    <title>Sobre Nosotros - RetroGames Store</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .about-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .about-hero {
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .about-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(46, 41, 78, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
        }

        .hero-overlay h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-overlay p {
            font-size: 1.2rem;
            max-width: 700px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .about-section {
            margin-bottom: 60px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .about-section h2 {
            color: #2e294e;
            font-size: 1.8rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
        }

        .about-section h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: #2e294e;
        }

        .about-section p {
            margin-bottom: 15px;
            line-height: 1.7;
            color: #555;
        }

        .about-story {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            height: calc(100% - 10px);
            width: 2px;
            background-color: #2e294e;
        }

        .timeline-item {
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #2e294e;
            border: 2px solid white;
        }

        .timeline-year {
            font-weight: 700;
            color: #2e294e;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .timeline-content {
            padding-bottom: 10px;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .mission-card,
        .vision-card {
            padding: 25px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .mission-card h3,
        .vision-card h3 {
            color: #2e294e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 1.4rem;
        }

        .mission-card h3 i,
        .vision-card h3 i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .values-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .value-item {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .value-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(46, 41, 78, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .value-icon i {
            font-size: 24px;
            color: #2e294e;
        }

        .value-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2e294e;
            margin-bottom: 10px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .team-member {
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .member-photo {
            height: 200px;
            overflow: hidden;
        }

        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info {
            padding: 20px;
        }

        .member-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2e294e;
            margin-bottom: 5px;
        }

        .member-role {
            color: #666;
            margin-bottom: 10px;
            font-style: italic;
        }

        .social-member {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .social-member a {
            color: #2e294e;
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-member a:hover {
            color: #3d366a;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 40px;
            text-align: center;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2e294e;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
        }

        .about-cta {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            background-color: #2e294e;
            color: white;
            border-radius: 10px;
        }

        .about-cta h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .about-cta p {
            margin-bottom: 20px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-white {
            display: inline-block;
            background-color: white;
            color: #2e294e;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-white:hover {
            background-color: #f1f1f1;
            transform: translateY(-3px);
        }

        @media (max-width: 992px) {
            .mission-vision {
                grid-template-columns: 1fr;
            }

            .stats-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero-overlay h1 {
                font-size: 2rem;
            }

            .hero-overlay p {
                font-size: 1rem;
            }

            .stats-section {
                grid-template-columns: 1fr;
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
        <div class="about-container">
            <div class="about-header">
                <div class="about-hero">
                    <img src="img/about/store-hero.jpg" alt="RetroGames Store">
                    <div class="hero-overlay">
                        <h1>Sobre RetroGames Store</h1>
                        <p>Reviviendo la nostalgia de los videojuegos clásicos desde 2010</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2>Quiénes Somos</h2>
                <p>RetroGames Store es una tienda especializada en videojuegos, consolas y revistas retro fundada por un grupo de apasionados coleccionistas y jugadores que comparten un profundo amor por la historia del videojuego.</p>
                <p>Nos dedicamos a preservar, restaurar y compartir la cultura del videojuego clásico, ofreciendo productos de calidad desde los primeros sistemas de los años 70 hasta las plataformas de principios del 2000.</p>
                <p>Nuestra tienda comenzó como un pequeño local en el centro de la ciudad y ha crecido hasta convertirse en un referente nacional para los aficionados al retrogaming, manteniendo siempre nuestra filosofía original: autenticidad, calidad y pasión por los clásicos.</p>
            </div>

            <div class="about-section">
                <h2>Nuestra Historia</h2>
                <div class="about-story">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-year">2010</div>
                            <div class="timeline-content">
                                <p>RetroGames Store nace como un pequeño local de 30m² en el centro de la ciudad. Los fundadores, Carlos y María, comienzan vendiendo parte de su colección personal.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-year">2012</div>
                            <div class="timeline-content">
                                <p>Tras dos años de crecimiento, nos trasladamos a un local más amplio y lanzamos nuestra primera web, permitiendo ventas a nivel nacional.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-year">2015</div>
                            <div class="timeline-content">
                                <p>Incorporamos al equipo a especialistas en restauración de hardware y comenzamos a ofrecer servicios de reparación de consolas y modificaciones.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-year">2017</div>
                            <div class="timeline-content">
                                <p>Organizamos el primer "RetroGames Festival", un evento que reúne a cientos de aficionados y se convierte en una cita anual imprescindible.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-year">2020</div>
                            <div class="timeline-content">
                                <p>Celebramos nuestro 10° aniversario con la apertura de nuestra tienda actual de 300m² que incluye una zona de exposición y un área para jugar a clásicos.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-year">2023</div>
                            <div class="timeline-content">
                                <p>Lanzamos nuestra nueva plataforma online completamente renovada, ampliamos el catálogo a más de 5.000 productos y comenzamos envíos internacionales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2>Misión, Visión y Valores</h2>

                <div class="mission-vision">
                    <div class="mission-card">
                        <h3><i class="fas fa-bullseye"></i> Nuestra Misión</h3>
                        <p>Preservar y compartir el legado de los videojuegos clásicos, ofreciendo productos de alta calidad junto con el conocimiento y la pasión que merecen, para que nuevas generaciones puedan descubrir los orígenes de la cultura del videojuego.</p>
                    </div>

                    <div class="vision-card">
                        <h3><i class="fas fa-eye"></i> Nuestra Visión</h3>
                        <p>Convertirnos en el referente internacional en la conservación y distribución de videojuegos retro, creando una comunidad vibrante donde coleccionistas, jugadores y curiosos puedan conectar con la historia interactiva de los videojuegos.</p>
                    </div>
                </div>

                <div class="values-list">
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="value-title">Autenticidad</div>
                        <p>Garantizamos la autenticidad de todos nuestros productos, verificando meticulosamente cada artículo.</p>
                    </div>

                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="value-title">Pasión</div>
                        <p>Trabajamos con pasión genuina por los videojuegos retro y transmitimos ese entusiasmo en todo lo que hacemos.</p>
                    </div>

                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="value-title">Calidad</div>
                        <p>Nos comprometemos a ofrecer productos en el mejor estado posible y describir con total transparencia su condición.</p>
                    </div>

                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="value-title">Comunidad</div>
                        <p>Fomentamos la creación de una comunidad activa que comparta el amor por los juegos clásicos.</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2>Nuestro Equipo</h2>
                <p>Detrás de RetroGames Store hay un equipo de apasionados por los videojuegos clásicos, cada uno con especialidades y conocimientos únicos que aportan valor a nuestra tienda.</p>

                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-photo">
                            <img src="img/about/team-1.jpg" alt="Carlos Martínez">
                        </div>
                        <div class="member-info">
                            <div class="member-name">Carlos Martínez</div>
                            <div class="member-role">Fundador y Director</div>
                            <p>Coleccionista desde los 90, especialista en consolas japonesas.</p>
                            <div class="social-member">
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="team-member">
                        <div class="member-photo">
                            <img src="img/about/team-2.jpg" alt="María López">
                        </div>
                        <div class="member-info">
                            <div class="member-name">María López</div>
                            <div class="member-role">Co-fundadora y Resp. de Adquisiciones</div>
                            <p>Experta en valoración de productos retro y ediciones especiales.</p>
                            <div class="social-member">
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="team-member">
                        <div class="member-photo">
                            <img src="img/about/team-3.jpg" alt="Javier Sánchez">
                        </div>
                        <div class="member-info">
                            <div class="member-name">Javier Sánchez</div>
                            <div class="member-role">Técnico de Restauración</div>
                            <p>Ingeniero electrónico especializado en reparación de hardware vintage.</p>
                            <div class="social-member">
                                <a href="#"><i class="fab fa-youtube"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="team-member">
                        <div class="member-photo">
                            <img src="img/about/team-4.jpg" alt="Laura Gómez">
                        </div>
                        <div class="member-info">
                            <div class="member-name">Laura Gómez</div>
                            <div class="member-role">Responsable de Atención al Cliente</div>
                            <p>Garantiza que cada cliente reciba un servicio excepcional.</p>
                            <div class="social-member">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2>RetroGames Store en Números</h2>

                <div class="stats-section">
                    <div class="stat-item">
                        <div class="stat-value">13</div>
                        <div class="stat-label">Años de experiencia</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value">5.000+</div>
                        <div class="stat-label">Productos en catálogo</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value">20.000+</div>
                        <div class="stat-label">Clientes satisfechos</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value">15</div>
                        <div class="stat-label">Apasionados en el equipo</div>
                    </div>
                </div>
            </div>

            <div class="about-cta">
                <h3>¿Quieres ser parte de nuestra comunidad retro?</h3>
                <p>Visítanos en nuestra tienda física o contáctanos para cualquier consulta sobre videojuegos, consolas o accesorios clásicos. ¡Estamos deseando compartir nuestra pasión contigo!</p>
                <a href="contact.php" class="btn-white">Contacta con nosotros</a>
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