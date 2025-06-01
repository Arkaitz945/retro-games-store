<?php

require_once "../model/CarritoModel.php";
require_once "../model/JuegosModel.php";
require_once "../model/ConsolasModel.php";
require_once "../model/RevistasModel.php"; // Añadir modelo de revistas
require_once "../config/dbConnection.php"; // Actualizar a la ruta correcta

class CarritoController
{
    private $carritoModel;
    private $juegosModel;
    private $consolasModel;
    private $revistasModel; // Añadir propiedad para el modelo de revistas

    public function __construct()
    {
        $this->carritoModel = new CarritoModel();
        $this->juegosModel = new JuegosModel();
        $this->consolasModel = new ConsolasModel();
        $this->revistasModel = new RevistasModel(); // Inicializar modelo de revistas
    }

    /**
     * Obtener todos los items del carrito de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Items del carrito con información detallada
     */
    public function getCart($idUsuario)
    {
        return $this->carritoModel->getCartItems($idUsuario);
    }

    /**
     * Añadir un producto al carrito
     * 
     * @param int $idUsuario ID del usuario
     * @param string $tipoProducto Tipo de producto
     * @param int $productoId ID del producto
     * @param int $cantidad Cantidad a añadir
     * @return array Resultado de la operación
     */
    public function addToCart($idUsuario, $tipoProducto, $productoId, $cantidad = 1)
    {
        // Verificar qué tipo de producto es y obtener sus datos
        $producto = null;

        switch ($tipoProducto) {
            case 'juego':
                $producto = $this->juegosModel->getJuegoById($productoId);
                break;
            case 'consola':
                $producto = $this->consolasModel->getConsolaById($productoId);
                break;
            case 'revista':
                $producto = $this->revistasModel->getRevistaById($productoId);
                // Asegurar que el nombre del producto esté disponible para mensajes
                if ($producto && isset($producto['titulo'])) {
                    $producto['nombre'] = $producto['titulo'];
                }
                break;
            default:
                return [
                    'success' => false,
                    'message' => 'Tipo de producto no soportado'
                ];
        }

        if (!$producto) {
            return [
                'success' => false,
                'message' => 'El producto no existe'
            ];
        }

        if ($producto['stock'] < $cantidad) {
            return [
                'success' => false,
                'message' => 'Error: No hay suficiente stock disponible. Solo quedan ' . $producto['stock'] . ' unidad(es) de este producto.'
            ];
        }

        // Verificar la cantidad actual en el carrito
        $cartItemId = $this->carritoModel->itemExists($idUsuario, $tipoProducto, $productoId);
        if ($cartItemId) {
            $cartItems = $this->carritoModel->getCartItems($idUsuario);
            foreach ($cartItems as $item) {
                if ($item['tipo_producto'] == $tipoProducto && $item['producto_id'] == $productoId) {
                    $currentQty = $item['cantidad'];
                    // Verificar que la nueva cantidad total no exceda el stock
                    if (($currentQty + $cantidad) > $producto['stock']) {
                        $disponible = $producto['stock'] - $currentQty;

                        if ($disponible <= 0) {
                            return [
                                'success' => false,
                                'message' => 'Error: Ya tienes todas las unidades disponibles de este producto en tu carrito.'
                            ];
                        } else {
                            return [
                                'success' => false,
                                'message' => 'Error: Solo puedes añadir ' . $disponible . ' unidad(es) más. Actualmente tienes ' . $currentQty . ' en tu carrito y hay ' . $producto['stock'] . ' en stock.'
                            ];
                        }
                    }
                    break;
                }
            }
        }

        // Añadir al carrito
        $result = $this->carritoModel->addToCart($idUsuario, $tipoProducto, $productoId, $cantidad);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Producto añadido al carrito correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al añadir el producto al carrito'
            ];
        }
    }

    /**
     * Actualizar la cantidad de un item en el carrito
     * 
     * @param int $idUsuario ID del usuario
     * @param int $cartItemId ID del item en el carrito
     * @param int $cantidad Nueva cantidad
     * @return array Resultado de la operación
     */
    public function updateCartItemQuantity($idUsuario, $cartItemId, $cantidad)
    {
        // Primero obtenemos el item para comprobar el juego y verificar stock
        $cartItems = $this->carritoModel->getCartItems($idUsuario);
        $targetItem = null;

        foreach ($cartItems as $item) {
            if ($item['ID_Carrito'] == $cartItemId) {
                $targetItem = $item;
                break;
            }
        }

        if (!$targetItem) {
            return [
                'success' => false,
                'message' => 'Item no encontrado en el carrito'
            ];
        }

        // Verificar stock
        if ($cantidad > $targetItem['stock']) {
            return [
                'success' => false,
                'message' => 'Error: No hay suficiente stock disponible. Solo hay ' . $targetItem['stock'] . ' unidad(es) disponibles.'
            ];
        }

        // Actualizar cantidad
        $result = $this->carritoModel->updateCartItemQuantity($cartItemId, $cantidad, false);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cantidad actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar la cantidad'
            ];
        }
    }

    /**
     * Eliminar un item del carrito
     * 
     * @param int $idUsuario ID del usuario
     * @param int $cartItemId ID del item en el carrito
     * @return array Resultado de la operación
     */
    public function removeFromCart($idUsuario, $cartItemId)
    {
        $result = $this->carritoModel->removeFromCart($cartItemId, $idUsuario);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Item eliminado del carrito correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar el item del carrito'
            ];
        }
    }

    /**
     * Vaciar todo el carrito de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Resultado de la operación
     */
    public function clearCart($idUsuario)
    {
        try {
            // Verificar que el ID de usuario es válido
            if (!$idUsuario || !is_numeric($idUsuario)) {
                error_log("CarritoController: ID de usuario inválido para vaciar carrito");
                return [
                    'success' => false,
                    'message' => 'ID de usuario inválido'
                ];
            }

            // Registrar acción para depuración
            error_log("CarritoController: Intentando vaciar carrito para usuario ID: " . $idUsuario);

            // Llamar al método del modelo para vaciar el carrito
            $result = $this->carritoModel->clearCart($idUsuario);

            if ($result) {
                // También limpiar el carrito en la sesión si existe
                if (isset($_SESSION['carrito'])) {
                    unset($_SESSION['carrito']);
                    error_log("CarritoController: Carrito de sesión eliminado");
                }

                return [
                    'success' => true,
                    'message' => 'Carrito vaciado correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al vaciar el carrito'
                ];
            }
        } catch (Exception $e) {
            error_log("CarritoController: Excepción al vaciar carrito - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al vaciar el carrito: ' . $e->getMessage()
            ];
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
        return $this->carritoModel->countCartItems($idUsuario);
    }

    /**
     * Calcular el total del carrito
     * 
     * @param int $idUsuario ID del usuario
     * @return float Total del carrito
     */
    public function getCartTotal($idUsuario)
    {
        $cartItems = $this->carritoModel->getCartItems($idUsuario);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return $total;
    }

    /**
     * Establece conexión con la base de datos
     * @return PDO Objeto de conexión PDO
     */
    private function conectarDB()
    {
        return getDBConnection();
    }

    /**
     * Reduce el stock de los productos en el carrito
     * @param int $idUsuario ID del usuario
     * @return array Resultado de la operación
     */
    public function reduceStock($idUsuario)
    {
        try {
            $db = $this->conectarDB();

            // Obtener los productos en el carrito
            $cartItems = $this->getCart($idUsuario);

            if (empty($cartItems)) {
                return ['success' => false, 'message' => 'No hay productos en el carrito'];
            }

            // Iniciar transacción
            $db->beginTransaction();

            foreach ($cartItems as $item) {
                // Determinar la tabla según el tipo de producto
                $tabla = '';
                $idColumn = '';

                switch ($item['tipo_producto']) {
                    case 'juego':
                        $tabla = 'juegos';
                        $idColumn = 'ID_J';
                        break;
                    case 'consola':
                        $tabla = 'consolas';
                        $idColumn = 'ID_Consola';
                        break;
                    case 'revista':
                        $tabla = 'revistas';
                        $idColumn = 'ID_Revista';
                        break;
                    case 'accesorio':
                        $tabla = 'accesorios';
                        $idColumn = 'ID_Accesorio';
                        break;
                    default:
                        throw new Exception("Tipo de producto desconocido: " . $item['tipo_producto']);
                }

                // Identificar el campo correcto que contiene el ID del producto
                // Este es el problema principal - estamos usando el campo incorrecto
                $productoId = $item['producto_id']; // En lugar de 'ID_Producto'

                error_log("Actualizando stock para: Tipo={$item['tipo_producto']}, ID={$productoId}, Tabla={$tabla}, Columna={$idColumn}");

                // Verificar stock actual
                $stmt = $db->prepare("SELECT stock FROM $tabla WHERE $idColumn = :id");
                $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
                $stmt->execute();
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    throw new Exception("Producto no encontrado: Tipo={$item['tipo_producto']}, ID={$productoId}, Tabla={$tabla}");
                }

                $stockActual = $producto['stock'];
                $cantidadPedida = $item['cantidad'];

                if ($stockActual < $cantidadPedida) {
                    throw new Exception("Stock insuficiente para el producto " . $item['nombre']);
                }

                // Actualizar stock
                $nuevoStock = $stockActual - $cantidadPedida;
                $stmt = $db->prepare("UPDATE $tabla SET stock = :stock WHERE $idColumn = :id");
                $stmt->bindParam(':stock', $nuevoStock, PDO::PARAM_INT);
                $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
                $result = $stmt->execute();

                if (!$result) {
                    throw new Exception("Error al actualizar el stock del producto " . $item['nombre']);
                }

                // Registrar en log la reducción de stock
                error_log("Stock reducido para producto ID: " . $productoId .
                    ", Tipo: " . $item['tipo_producto'] .
                    ", Cantidad: " . $cantidadPedida .
                    ", Nuevo stock: " . $nuevoStock);
            }

            // Confirmar transacción
            $db->commit();

            return ['success' => true, 'message' => 'Stock actualizado correctamente'];
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error en reduceStock: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar el stock: ' . $e->getMessage()];
        }
    }
}
