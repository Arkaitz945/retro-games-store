CREATE TABLE `pedidos` (
  `ID_Pedido` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_pedido` varchar(50) NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  PRIMARY KEY (`ID_Pedido`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`ID_U`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;