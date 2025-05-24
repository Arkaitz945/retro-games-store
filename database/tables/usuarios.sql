CREATE TABLE `usuarios` (
  `ID_U` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL UNIQUE,
  `contraseña` varchar(255) NOT NULL,
  `esAdmin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID_U`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;