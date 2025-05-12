-- Eliminar la tabla si existe
DROP TABLE IF EXISTS carrito;

-- Crear la tabla con la nueva estructura pero sin la restricción de clave foránea
CREATE TABLE carrito (
  ID_Carrito BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ID_U BIGINT UNSIGNED NOT NULL,
  tipo_producto ENUM('juego', 'consola', 'revista', 'accesorio') NOT NULL,
  producto_id BIGINT UNSIGNED NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  PRIMARY KEY (ID_Carrito),
  INDEX idx_tipo_producto (tipo_producto),
  INDEX idx_producto_id (producto_id),
  INDEX idx_usuario (ID_U),
  UNIQUE KEY unique_product_in_cart (ID_U, tipo_producto, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
