CREATE TABLE `direccion` (
  `ID_Direccion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `calle` varchar(255) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `codigoPostal` varchar(10) NOT NULL,
  `idUsuario` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_Direccion`),
  KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `direccion_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`ID_U`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;