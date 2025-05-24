CREATE TABLE `juegos` (
  `ID_J` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `plataforma` varchar(50) NOT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `año_lanzamiento` int(4) DEFAULT NULL,
  `desarrollador` varchar(100) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Usado',
  `precio` decimal(8,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `region` varchar(20) DEFAULT NULL,
  `incluye_caja` tinyint(1) DEFAULT 0,
  `incluye_manual` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`ID_J`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;