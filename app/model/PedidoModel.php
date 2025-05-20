<?php
require_once "../config/database.php";

class PedidoModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Crear un nuevo pedido en la base de datos
     * 
     * @param string $numeroPedido Número único del pedido
     * @param int $idUsuario ID del usuario
     * @param float $total Monto total del pedido
     * @return int|bool ID del nuevo pedido o false si falla
     */
    public function createPedido($numeroPedido, $idUsuario, $total)
    {
        try {
            $fecha = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("INSERT INTO pedidos (numero_pedido, id_usuario, fecha, total) 
                                        VALUES (:numero_pedido, :id_usuario, :fecha, :total)");

            $stmt->bindParam(':numero_pedido', $numeroPedido);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':total', $total);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al crear pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar los detalles de cada producto en el pedido
     * 
     * @param int $idPedido ID del pedido
     * @param int $idProducto ID del producto (juego)
     * @param int $cantidad Cantidad comprada
     * @param float $precioUnitario Precio unitario del producto
     * @return bool Éxito de la operación
     */
    public function saveOrderDetail($idPedido, $idProducto, $cantidad, $precioUnitario)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO detallespedido (ID_Pedido, ID_J, cantidad, precioUnitario) 
                                        VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)");

            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':precio_unitario', $precioUnitario);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar detalle de pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener los detalles de un pedido específico
     * 
     * @param int $idPedido ID del pedido
     * @return array Detalles del pedido
     */
    public function getOrderDetails($idPedido)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT d.*, j.nombre 
                FROM detallespedido d
                JOIN juegos j ON d.ID_J = j.ID_J
                WHERE d.ID_Pedido = :id_pedido
            ");

            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener detalles de pedido: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los pedidos de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Pedidos del usuario
     */
    public function getUserOrders($idUsuario)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM pedidos
                WHERE id_usuario = :id_usuario
                ORDER BY fecha DESC
            ");

            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos de usuario: " . $e->getMessage());
            return [];
        }
    }
}
