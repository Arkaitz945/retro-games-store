<?php
require_once "../model/PedidoModel.php";
require_once "../config/dbConnection.php";

class PedidoController
{
    private $pedidoModel;
    private $conn;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->conn = getDBConnection();

        if (!$this->conn) {
            error_log("PedidoController: Error crítico - No se pudo establecer conexión con la base de datos");
        }
    }

    /**
     * Guardar un pedido completo en la base de datos
     * 
     * @param int $idUsuario ID del usuario
     * @param string $numeroPedido Número de pedido generado
     * @param array $cartItems Productos del carrito
     * @param float $total Total del pedido
     * @return array Resultado de la operación
     */
    public function savePedido($idUsuario, $numeroPedido, $cartItems, $total)
    {
        try {
            // Verificar que tenemos modelo y conexión antes de proceder
            if (!$this->pedidoModel) {
                throw new Exception("Error: Modelo de pedido no inicializado");
            }

            // Obtener conexión para manejar transacción
            $db = $this->conectarDB();

            if (!$db) {
                throw new Exception("No se pudo conectar a la base de datos");
            }

            $db->beginTransaction();

            // 1. Crear el pedido principal
            $idPedido = $this->pedidoModel->createPedido($numeroPedido, $idUsuario, $total);

            if (!$idPedido) {
                throw new Exception("No se pudo crear el pedido principal");
            }

            // 2. Guardar cada producto en detallespedido
            foreach ($cartItems as $item) {
                error_log("PedidoController::savePedido - Procesando item: " . print_r($item, true));

                if (!isset($item['producto_id'])) {
                    error_log("PedidoController::savePedido - Falta producto_id en el item del carrito");
                    throw new Exception("Datos de producto incompletos");
                }

                $result = $this->pedidoModel->saveOrderDetail(
                    $idPedido,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio']
                );

                if (!$result) {
                    throw new Exception("Error al guardar el producto " . ($item['nombre'] ?? 'Desconocido') . " en el pedido");
                }
            }

            // Confirmar transacción
            $db->commit();
            error_log("PedidoController::savePedido - Pedido guardado correctamente: ID=$idPedido, Número=$numeroPedido");

            return [
                'success' => true,
                'message' => 'Pedido guardado correctamente',
                'idPedido' => $idPedido,
                'numeroPedido' => $numeroPedido
            ];
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            error_log("PedidoController::savePedido - Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al guardar el pedido: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener los pedidos de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Pedidos del usuario
     */
    public function getUserOrders($idUsuario)
    {
        return $this->pedidoModel->getUserOrders($idUsuario);
    }

    /**
     * Obtener un pedido específico por ID y usuario
     * 
     * @param int $idUsuario ID del usuario
     * @param int $idPedido ID del pedido
     * @return array|bool Datos del pedido o false si no existe
     */
    public function getUserOrderById($idUsuario, $idPedido)
    {
        return $this->pedidoModel->getUserOrderById($idUsuario, $idPedido);
    }

    /**
     * Obtener los detalles de un pedido
     * 
     * @param int $idPedido ID del pedido
     * @return array Detalles del pedido
     */
    public function getOrderDetails($idPedido)
    {
        return $this->pedidoModel->getOrderDetails($idPedido);
    }

    /**
     * Establece conexión con la base de datos
     * @return PDO Objeto de conexión PDO
     */
    private function conectarDB()
    {
        return $this->conn ?: getDBConnection();
    }
}
