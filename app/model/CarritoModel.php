<?php

require_once "../../config/dbConnection.php";

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
            // Consultamos primero los items del carrito
            $query = "SELECT c.ID_Carrito, c.tipo_producto, c.producto_id, c.cantidad 
                      FROM carrito c 
                      WHERE c.ID_U = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];

            // Para cada item, obtenemos la información del producto (actualmente solo juegos)
            foreach ($cartItems as $item) {
                if ($item['tipo_producto'] == 'juego') {
                    // Obtener detalles del juego
                    $queryJuego = "SELECT j.nombre, j.plataforma, j.precio, j.imagen, j.stock 
                                   FROM juegos j 
                                   WHERE j.ID_J = :productoId";

                    $stmtJuego = $this->conn->prepare($queryJuego);
                    $stmtJuego->bindParam(":productoId", $item['producto_id'], PDO::PARAM_INT);
                    $stmtJuego->execute();

                    if ($stmtJuego->rowCount() > 0) {
                        $juego = $stmtJuego->fetch(PDO::FETCH_ASSOC);
                        $result[] = array_merge($item, $juego);
                    }
                }
                // Aquí se pueden añadir más casos para otros tipos de productos cuando se implementen
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error obteniendo items del carrito: " . $e->getMessage());
            return [];
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

                return $stmt->execute();
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
     * Vaciar todo el carrito de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return bool True si se vació correctamente
     */
    public function clearCart($idUsuario)
    {
        try {
            $query = "DELETE FROM carrito WHERE ID_U = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error vaciando el carrito: " . $e->getMessage());
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
