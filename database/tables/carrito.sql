CREATE TABLE `carrito` (
  `ID_Carrito` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_U` bigint(20) UNSIGNED NOT NULL,
  `tipo_producto` ENUM('juego', 'consola', 'revista', 'accesorio') NOT NULL,
  `producto_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID_Carrito`),
  INDEX `idx_tipo_producto` (`tipo_producto`),
  INDEX `idx_producto_id` (`producto_id`),
  INDEX `idx_usuario` (`ID_U`),
  UNIQUE KEY `unique_product_in_cart` (`ID_U`, `tipo_producto`, `producto_id`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`ID_U`) REFERENCES `usuarios` (`ID_U`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;