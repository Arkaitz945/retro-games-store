<?php
require_once "../model/PedidoModel.php";
require_once "../config/database.php";

class PedidoController
{
    private $pedidoModel;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
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
            // Obtener conexión para manejar transacción
            $db = $this->conectarDB();
            $db->beginTransaction();

            // 1. Crear el pedido principal
            $idPedido = $this->pedidoModel->createPedido($numeroPedido, $idUsuario, $total);

            if (!$idPedido) {
                throw new Exception("No se pudo crear el pedido principal");
            }

            // 2. Guardar cada producto en detallespedido
            foreach ($cartItems as $item) {
                $result = $this->pedidoModel->saveOrderDetail(
                    $idPedido,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio']
                );

                if (!$result) {
                    throw new Exception("Error al guardar el producto " . $item['nombre'] . " en el pedido");
                }
            }

            // Confirmar transacción
            $db->commit();

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

            error_log("Error al guardar pedido: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al guardar el pedido: ' . $e->getMessage()
            ];
        }
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
     * Establece conexión con la base de datos
     * @return PDO Objeto de conexión PDO
     */
    private function conectarDB()
    {
        try {
            $host = 'localhost';
            $dbname = 'retro_games_db';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            return new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a la BD: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos. Por favor, inténtelo de nuevo más tarde.");
        }
    }
}
