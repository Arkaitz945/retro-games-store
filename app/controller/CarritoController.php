<?php

require_once "../model/CarritoModel.php";
require_once "../model/JuegosModel.php";

class CarritoController
{
    private $carritoModel;
    private $juegosModel;

    public function __construct()
    {
        $this->carritoModel = new CarritoModel();
        $this->juegosModel = new JuegosModel();
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
        // Por ahora, solo manejamos juegos ya que es el único modelo que tenemos implementado
        if ($tipoProducto != 'juego') {
            return [
                'success' => false,
                'message' => 'Actualmente solo se pueden añadir juegos al carrito'
            ];
        }

        // Verificar que el juego existe y tiene stock
        $producto = $this->juegosModel->getJuegoById($productoId);

        if (!$producto) {
            return [
                'success' => false,
                'message' => 'El juego no existe'
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
        $result = $this->carritoModel->clearCart($idUsuario);

        if ($result) {
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
}
