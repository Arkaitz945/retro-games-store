<?php

require_once __DIR__ . "/../../config/dbConnection.php";

class CarritoModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();

        if (!$this->conn) {
            die("Error: No se pudo conectar a la base de datos");
        }
    }

    /**
     * Obtener todos los items del carrito de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Array con los items del carrito
     */
    public function getCartItems($idUsuario)
    {
        try {
            // Verificar que el ID de usuario es válido
            if (!$idUsuario || !is_numeric($idUsuario)) {
                error_log("CarritoModel: ID de usuario inválido para obtener carrito");
                return [];
            }

            // Log para depuración
            error_log("Obteniendo items del carrito para usuario ID: " . $idUsuario);

            // Consultamos primero los items del carrito
            $query = "SELECT c.ID_Carrito, c.tipo_producto, c.producto_id, c.cantidad 
                      FROM carrito c 
                      WHERE c.ID_U = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log para depuración
            error_log("Items encontrados en el carrito: " . count($cartItems));

            if (empty($cartItems)) {
                error_log("No se encontraron items en el carrito para el usuario ID: " . $idUsuario);
                return [];
            }

            $result = [];

            // Para cada item, obtenemos la información del producto según su tipo
            foreach ($cartItems as $item) {
                $producto = $this->getProductInfo($item['tipo_producto'], $item['producto_id']);

                if ($producto) {
                    // Si encontramos información del producto, lo añadimos al resultado
                    $result[] = array_merge($item, $producto);
                    error_log("Producto añadido al resultado del carrito: " . json_encode($producto));
                } else {
                    error_log("No se pudo obtener información del producto: tipo=" . $item['tipo_producto'] . ", id=" . $item['producto_id']);
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error obteniendo items del carrito: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener información detallada de un producto según su tipo
     * 
     * @param string $tipoProducto Tipo de producto (juego, consola, revista)
     * @param int $productoId ID del producto
     * @return array|false Información del producto o false si no se encuentra
     */
    private function getProductInfo($tipoProducto, $productoId)
    {
        try {
            $query = "";
            $params = [":productoId" => $productoId];

            switch ($tipoProducto) {
                case 'juego':
                    $query = "SELECT nombre, plataforma, precio, imagen, stock 
                              FROM juegos 
                              WHERE ID_J = :productoId";
                    break;

                case 'consola':
                    $query = "SELECT nombre, fabricante AS plataforma, precio, imagen, stock 
                              FROM consolas 
                              WHERE ID_Consola = :productoId";
                    break;

                case 'revista':
                    $query = "SELECT nombre, editorial AS plataforma, precio, imagen, stock 
                              FROM revistas 
                              WHERE ID_Revista = :productoId";
                    break;

                default:
                    error_log("Tipo de producto no soportado: " . $tipoProducto);
                    return false;
            }

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                error_log("No se encontró el producto: tipo=" . $tipoProducto . ", id=" . $productoId);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo información del producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un producto ya está en el carrito del usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param string $tipoProducto Tipo de producto (juego, consola, etc.)
     * @param int $productoId ID del producto
     * @return mixed ID_Carrito si existe, false en caso contrario
     */
    public function itemExists($idUsuario, $tipoProducto, $productoId)
    {
        try {
            $query = "SELECT ID_Carrito FROM carrito 
                     WHERE ID_U = :idUsuario 
                     AND tipo_producto = :tipoProducto 
                     AND producto_id = :productoId";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":tipoProducto", $tipoProducto, PDO::PARAM_STR);
            $stmt->bindParam(":productoId", $productoId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['ID_Carrito'];
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error verificando item en carrito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Añadir un producto al carrito
     * 
     * @param int $idUsuario ID del usuario
     * @param string $tipoProducto Tipo de producto
     * @param int $productoId ID del producto
     * @param int $cantidad Cantidad a añadir
     * @return bool True si se añadió correctamente
     */
    public function addToCart($idUsuario, $tipoProducto, $productoId, $cantidad = 1)
    {
        try {
            // Verificar si el producto ya está en el carrito
            $cartItemId = $this->itemExists($idUsuario, $tipoProducto, $productoId);

            if ($cartItemId) {
                // Ya existe, actualizar cantidad
                return $this->updateCartItemQuantity($cartItemId, $cantidad, true);
            } else {
                // No existe, insertar nuevo item
                $query = "INSERT INTO carrito (ID_U, tipo_producto, producto_id, cantidad) 
                          VALUES (:idUsuario, :tipoProducto, :productoId, :cantidad)";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
                $stmt->bindParam(":tipoProducto", $tipoProducto, PDO::PARAM_STR);
                $stmt->bindParam(":productoId", $productoId, PDO::PARAM_INT);
                $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);

                $result = $stmt->execute();

                // Log para depuración
                if ($result) {
                    error_log("Producto añadido al carrito: Usuario=$idUsuario, Tipo=$tipoProducto, ID=$productoId, Cantidad=$cantidad");
                } else {
                    error_log("Error al añadir producto al carrito: " . json_encode($stmt->errorInfo()));
                }

                return $result;
            }
        } catch (PDOException $e) {
            error_log("Error añadiendo item al carrito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar la cantidad de un item en el carrito
     * 
     * @param int $cartItemId ID del item en el carrito
     * @param int $cantidad Nueva cantidad o incremento
     * @param bool $increment Si es true, incrementa la cantidad actual
     * @return bool True si se actualizó correctamente
     */
    public function updateCartItemQuantity($cartItemId, $cantidad, $increment = false)
    {
        try {
            if ($increment) {
                $query = "UPDATE carrito SET cantidad = cantidad + :cantidad
                          WHERE ID_Carrito = :cartItemId";
            } else {
                $query = "UPDATE carrito SET cantidad = :cantidad
                          WHERE ID_Carrito = :cartItemId";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(":cartItemId", $cartItemId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando cantidad en carrito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un item del carrito
     * 
     * @param int $cartItemId ID del item en el carrito
     * @param int $idUsuario ID del usuario (seguridad)
     * @return bool True si se eliminó correctamente
     */
    public function removeFromCart($cartItemId, $idUsuario)
    {
        try {
            $query = "DELETE FROM carrito WHERE ID_Carrito = :cartItemId AND ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":cartItemId", $cartItemId, PDO::PARAM_INT);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error eliminando item del carrito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vacía el carrito de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return bool Resultado de la operación
     */
    public function clearCart($idUsuario)
    {
        try {
            // Primero verificar que la conexión existe
            if (!$this->conn) {
                error_log("CarritoModel: No se puede vaciar el carrito - La conexión a la base de datos es nula");
                return false;
            }

            // Corregido el nombre de la columna - debe ser ID_U en lugar de ID_Usuario
            $query = "DELETE FROM carrito WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Registrar el resultado para depuración
            if ($result) {
                error_log("CarritoModel: Carrito vaciado correctamente para el usuario ID: " . $idUsuario);
            } else {
                error_log("CarritoModel: Error al vaciar el carrito para el usuario ID: " . $idUsuario);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("CarritoModel: Error al vaciar el carrito - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar items en el carrito
     * 
     * @param int $idUsuario ID del usuario
     * @return int Número de items en el carrito
     */
    public function countCartItems($idUsuario)
    {
        try {
            $query = "SELECT SUM(cantidad) as total FROM carrito WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error contando items del carrito: " . $e->getMessage());
            return 0;
        }
    }
}
