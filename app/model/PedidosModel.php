<?php

require_once __DIR__ . "/../../config/dbConnection.php";

class PedidosModel
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
     * Obtener todos los pedidos
     * 
     * @param array $filtros Filtros a aplicar
     * @return array Array con los pedidos
     */
    public function getAllPedidos($filtros = [])
    {
        try {
            $query = "SELECT p.*, u.nombre, u.apellidos, u.email 
                     FROM pedidos p 
                     JOIN usuarios u ON p.id_usuario = u.id";

            $condiciones = [];
            $params = [];

            // Aplicar filtros si existen
            if (!empty($filtros)) {
                // Filtro por estado
                if (isset($filtros['estado']) && !empty($filtros['estado'])) {
                    $condiciones[] = "p.estado = :estado";
                    $params[':estado'] = $filtros['estado'];
                }

                // Filtro por fecha desde
                if (isset($filtros['fecha_desde']) && !empty($filtros['fecha_desde'])) {
                    $condiciones[] = "p.fecha >= :fecha_desde";
                    $params[':fecha_desde'] = $filtros['fecha_desde'];
                }

                // Filtro por fecha hasta
                if (isset($filtros['fecha_hasta']) && !empty($filtros['fecha_hasta'])) {
                    $condiciones[] = "p.fecha <= :fecha_hasta";
                    $params[':fecha_hasta'] = $filtros['fecha_hasta'];
                }

                // Filtro por usuario
                if (isset($filtros['id_usuario']) && !empty($filtros['id_usuario'])) {
                    $condiciones[] = "p.id_usuario = :id_usuario";
                    $params[':id_usuario'] = $filtros['id_usuario'];
                }

                // Añadir condiciones a la consulta
                if (!empty($condiciones)) {
                    $query .= " WHERE " . implode(" AND ", $condiciones);
                }
            }

            // Ordenar por fecha descendente (más recientes primero)
            $query .= " ORDER BY p.fecha DESC";

            $stmt = $this->conn->prepare($query);

            // Bind params
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo pedidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un pedido por su ID
     * 
     * @param int $idPedido ID del pedido
     * @return mixed Array con la información del pedido o false si no existe
     */
    public function getPedidoById($idPedido)
    {
        try {
            $query = "SELECT p.*, u.nombre, u.apellidos, u.email, u.telefono, u.direccion 
                     FROM pedidos p 
                     JOIN usuarios u ON p.id_usuario = u.id 
                     WHERE p.id = :idPedido";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idPedido", $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error obteniendo pedido por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener detalles de un pedido
     * 
     * @param int $idPedido ID del pedido
     * @return array Array con los detalles del pedido
     */
    public function getDetallesPedido($idPedido)
    {
        try {
            $query = "SELECT dp.*, 
                      COALESCE(j.nombre, c.nombre, r.titulo) as nombre_producto,
                      CASE 
                        WHEN j.ID_J IS NOT NULL THEN 'juego'
                        WHEN c.ID_Consola IS NOT NULL THEN 'consola'
                        WHEN r.ID_Revista IS NOT NULL THEN 'revista'
                      END as tipo_producto
                      FROM detalles_pedido dp
                      LEFT JOIN juegos j ON dp.id_producto = j.ID_J AND dp.tipo_producto = 'juego'
                      LEFT JOIN consolas c ON dp.id_producto = c.ID_Consola AND dp.tipo_producto = 'consola'
                      LEFT JOIN revistas r ON dp.id_producto = r.ID_Revista AND dp.tipo_producto = 'revista'
                      WHERE dp.id_pedido = :idPedido";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idPedido", $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo detalles del pedido: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualizar el estado de un pedido
     * 
     * @param int $idPedido ID del pedido
     * @param string $estado Nuevo estado del pedido
     * @return bool Resultado de la operación
     */
    public function updateEstadoPedido($idPedido, $estado)
    {
        try {
            $query = "UPDATE pedidos SET estado = :estado WHERE id = :idPedido";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":idPedido", $idPedido, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando estado del pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener estados únicos de pedidos
     * 
     * @return array Array con los estados
     */
    public function getEstadosPedidos()
    {
        try {
            $query = "SELECT DISTINCT estado FROM pedidos ORDER BY estado";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo estados de pedidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de ventas por periodo
     * 
     * @param string $periodo Periodo de tiempo (dia, mes, anio)
     * @return array Datos de estadísticas
     */
    public function getEstadisticasVentas($periodo = 'mes')
    {
        try {
            if ($periodo === 'dia') {
                $query = "SELECT DATE_FORMAT(fecha, '%Y-%m-%d') as periodo, 
                          SUM(total) as ventas, 
                          COUNT(*) as num_pedidos 
                          FROM pedidos 
                          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                          GROUP BY DATE_FORMAT(fecha, '%Y-%m-%d') 
                          ORDER BY periodo";
            } else if ($periodo === 'mes') {
                $query = "SELECT DATE_FORMAT(fecha, '%Y-%m') as periodo, 
                          SUM(total) as ventas, 
                          COUNT(*) as num_pedidos 
                          FROM pedidos 
                          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                          GROUP BY DATE_FORMAT(fecha, '%Y-%m') 
                          ORDER BY periodo";
            } else {
                $query = "SELECT DATE_FORMAT(fecha, '%Y') as periodo, 
                          SUM(total) as ventas, 
                          COUNT(*) as num_pedidos 
                          FROM pedidos 
                          GROUP BY DATE_FORMAT(fecha, '%Y') 
                          ORDER BY periodo";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas de ventas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener productos más vendidos
     * 
     * @param int $limit Límite de resultados
     * @return array Productos más vendidos
     */
    public function getProductosMasVendidos($limit = 10)
    {
        try {
            $query = "SELECT dp.tipo_producto, dp.id_producto, 
                      COALESCE(j.nombre, c.nombre, r.titulo) as nombre_producto,
                      SUM(dp.cantidad) as total_vendido,
                      SUM(dp.precio * dp.cantidad) as ingresos
                      FROM detalles_pedido dp
                      LEFT JOIN juegos j ON dp.id_producto = j.ID_J AND dp.tipo_producto = 'juego'
                      LEFT JOIN consolas c ON dp.id_producto = c.ID_Consola AND dp.tipo_producto = 'consola'
                      LEFT JOIN revistas r ON dp.id_producto = r.ID_Revista AND dp.tipo_producto = 'revista'
                      GROUP BY dp.tipo_producto, dp.id_producto
                      ORDER BY total_vendido DESC
                      LIMIT :limit";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo productos más vendidos: " . $e->getMessage());
            return [];
        }
    }
}
