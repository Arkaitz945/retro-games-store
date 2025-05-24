<?php
require_once "../config/database.php";

class PedidoModel
{
    private $conn;

    public function __construct()
    {
        // Inicializar la conexión a la base de datos
        $this->conn = getDBConnection();

        // Verificar si la conexión se estableció correctamente
        if (!$this->conn) {
            error_log("PedidoModel: Error crítico - No se pudo establecer conexión con la base de datos");
        } else {
            error_log("PedidoModel: Conexión establecida correctamente");
        }
    }

    /**
     * Crear un nuevo pedido
     * 
     * @param string $numeroPedido Número de pedido único
     * @param int $idUsuario ID del usuario
     * @param float $total Total del pedido
     * @return int|bool ID del pedido creado o false en caso de error
     */
    public function createPedido($numeroPedido, $idUsuario, $total)
    {
        try {
            // Verificar que tenemos conexión antes de proceder
            if (!$this->conn) {
                error_log("PedidoModel::createPedido - No hay conexión a la base de datos");
                return false;
            }

            // Mostrar la estructura actual de la tabla para depuración
            try {
                $tablesQuery = $this->conn->query("SHOW TABLES");
                $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
                error_log("Tablas disponibles: " . implode(", ", $tables));

                if (in_array("pedidos", $tables)) {
                    $columnsQuery = $this->conn->query("DESCRIBE pedidos");
                    $columns = $columnsQuery->fetchAll(PDO::FETCH_COLUMN);
                    error_log("Columnas en pedidos: " . implode(", ", $columns));
                }
            } catch (PDOException $e) {
                error_log("Error al obtener estructura de la tabla: " . $e->getMessage());
            }

            // Construir la consulta para insertar un nuevo pedido
            // Adaptamos los nombres de columnas según la estructura real de la tabla
            $query = "INSERT INTO pedidos (numero_pedido, id_usuario, total, fecha, estado) 
                      VALUES (:numeroPedido, :idUsuario, :total, NOW(), 'pendiente')";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':numeroPedido', $numeroPedido);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':total', $total);

            $result = $stmt->execute();

            if ($result) {
                $idPedido = $this->conn->lastInsertId();
                error_log("PedidoModel::createPedido - Pedido creado exitosamente con ID: " . $idPedido);
                return $idPedido;
            } else {
                error_log("PedidoModel::createPedido - Error al ejecutar: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PedidoModel::createPedido - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar un detalle de pedido
     * 
     * @param int $idPedido ID del pedido
     * @param int $productoId ID del producto
     * @param int $cantidad Cantidad
     * @param float $precio Precio unitario
     * @return bool True si se guardó correctamente
     */
    public function saveOrderDetail($idPedido, $productoId, $cantidad, $precio)
    {
        try {
            // Verificar que tenemos conexión antes de proceder
            if (!$this->conn) {
                error_log("PedidoModel::saveOrderDetail - No hay conexión a la base de datos");
                return false;
            }

            // Consulta adaptada a la estructura real de la tabla detalles_pedido
            $query = "INSERT INTO detalles_pedido (id_pedido, tipo_producto, id_producto, cantidad, precio) 
                      VALUES (:idPedido, 'juego', :productoId, :cantidad, :precio)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':productoId', $productoId, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $precio);

            $result = $stmt->execute();

            if (!$result) {
                error_log("PedidoModel::saveOrderDetail - Error al ejecutar: " . print_r($stmt->errorInfo(), true));
            } else {
                error_log("PedidoModel::saveOrderDetail - Detalle guardado correctamente para pedido: $idPedido, producto: $productoId");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("PedidoModel::saveOrderDetail - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los pedidos de un usuario
     * 
     * @param int $idUsuario ID del usuario
     * @return array Lista de pedidos
     */
    public function getUserOrders($idUsuario)
    {
        try {
            if (!$this->conn) {
                error_log("PedidoModel::getUserOrders - No hay conexión a la base de datos");
                return [];
            }

            $query = "SELECT p.*, 
                     (SELECT COUNT(*) FROM detalles_pedido dp WHERE dp.id_pedido = p.id_pedido) as num_productos
                     FROM pedidos p 
                     WHERE p.id_usuario = :idUsuario 
                     ORDER BY p.fecha DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PedidoModel::getUserOrders - Error: " . $e->getMessage());
            return [];
        }
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
        try {
            if (!$this->conn) {
                error_log("PedidoModel::getUserOrderById - No hay conexión a la base de datos");
                return false;
            }

            $query = "SELECT p.* FROM pedidos p 
                     WHERE p.id_pedido = :idPedido AND p.id_usuario = :idUsuario";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (PDOException $e) {
            error_log("PedidoModel::getUserOrderById - Error: " . $e->getMessage());
            return false;
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
        try {
            if (!$this->conn) {
                error_log("PedidoModel::getOrderDetails - No hay conexión a la base de datos");
                return [];
            }

            $query = "SELECT * FROM detalles_pedido 
                     WHERE id_pedido = :idPedido";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PedidoModel::getOrderDetails - Error: " . $e->getMessage());
            return [];
        }
    }
}
