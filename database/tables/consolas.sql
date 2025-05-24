CREATE TABLE `consolas` (
  `ID_Consola` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fabricante` varchar(100) NOT NULL,
  `año_lanzamiento` int(4) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `precio` decimal(8,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_Consola`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;