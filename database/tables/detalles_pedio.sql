CREATE TABLE `detalles_pedido` (
  `ID_Detalle` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pedido` bigint(20) UNSIGNED NOT NULL,
  `tipo_producto` ENUM('juego', 'consola', 'revista', 'accesorio') NOT NULL,
  `id_producto` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(8,2) NOT NULL,
  PRIMARY KEY (`ID_Detalle`),
  KEY `id_pedido` (`id_pedido`),
  CONSTRAINT `detalles_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`ID_Pedido`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;