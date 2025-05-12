-- 1. Primero, eliminamos los datos existentes del carrito para evitar inconsistencias
TRUNCATE TABLE carrito;

-- 2. Modificamos la estructura de la tabla 
ALTER TABLE carrito 
DROP COLUMN ID_J,
ADD COLUMN tipo_producto ENUM('juego', 'consola', 'revista', 'accesorio') NOT NULL AFTER ID_U,
ADD COLUMN producto_id BIGINT UNSIGNED NOT NULL AFTER tipo_producto,
ADD INDEX idx_tipo_producto (tipo_producto),
ADD INDEX idx_producto_id (producto_id);

-- 3. Añadimos una restricción para el par (ID_U, tipo_producto, producto_id) sea único
-- para evitar duplicados del mismo producto en el carrito de un usuario
ALTER TABLE carrito
ADD UNIQUE KEY unique_product_in_cart (ID_U, tipo_producto, producto_id);
