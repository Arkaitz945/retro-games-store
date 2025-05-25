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
            // Actualizado para usar los nombres correctos de las columnas
            $query = "SELECT p.ID_Pedido, p.numero_pedido, p.id_usuario, p.fecha, p.total, p.estado, 
                      u.nombre, u.apellidos, u.correo as email 
                      FROM pedidos p 
                      JOIN usuarios u ON p.id_usuario = u.ID_U";

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
            // Añadir depuración
            error_log("Buscando pedido con ID: " . $idPedido);

            // Consulta simplificada para mejor depuración
            $query = "SELECT p.*, u.nombre, u.apellidos, u.correo as email 
                     FROM pedidos p 
                     LEFT JOIN usuarios u ON p.id_usuario = u.ID_U 
                     WHERE p.ID_Pedido = :idPedido";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idPedido", $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Pedido encontrado: " . print_r($pedido, true));
                return $pedido;
            }

            error_log("No se encontró ningún pedido con ID: " . $idPedido);
            return false;
        } catch (PDOException $e) {
            error_log("Error obteniendo pedido por ID: " . $e->getMessage());
            error_log("Traza: " . $e->getTraceAsString());
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
            // Actualizado para usar los nombres correctos
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
     * @param string $estado Nuevo estado
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function updateEstadoPedido($idPedido, $estado)
    {
        try {
            // Registrar la acción para depuración
            error_log("Intentando actualizar pedido ID: $idPedido al estado: $estado");

            // Validar estado permitido
            $estadosPermitidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];
            if (!in_array(strtolower($estado), $estadosPermitidos)) {
                error_log("Error: Estado no válido ($estado)");
                return false;
            }

            // Actualizar usando el nombre correcto de la columna (ID_Pedido)
            $query = "UPDATE pedidos SET estado = :estado WHERE ID_Pedido = :idPedido";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);

            $result = $stmt->execute();

            // Verificar si hubo cambios
            if ($result && $stmt->rowCount() > 0) {
                error_log("Pedido ID: $idPedido actualizado correctamente al estado: $estado");
                return true;
            } else {
                error_log("No se actualizó el pedido. ID: $idPedido, Estado: $estado, Resultado: " . ($result ? 'true' : 'false') . ", Filas afectadas: " . $stmt->rowCount());

                // Verificar si el pedido existe
                $checkQuery = "SELECT COUNT(*) as total FROM pedidos WHERE ID_Pedido = :idPedido";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
                $checkStmt->execute();
                $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($checkResult['total'] == 0) {
                    error_log("El pedido ID: $idPedido no existe en la base de datos");
                } else {
                    error_log("El pedido existe pero no se actualizó. Posiblemente ya tiene el mismo estado.");
                }

                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar estado del pedido: " . $e->getMessage());
            error_log("Traza: " . $e->getTraceAsString());
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

    /**
     * Contar el número total de pedidos según filtros
     * 
     * @param array $filtros Filtros a aplicar
     * @return int Número total de pedidos
     */
    public function countPedidos($filtros = [])
    {
        try {
            $query = "SELECT COUNT(*) as total FROM pedidos p JOIN usuarios u ON p.id_usuario = u.id";

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
                if (isset($filtros['cliente']) && !empty($filtros['cliente'])) {
                    $condiciones[] = "p.id_usuario = :id_usuario";
                    $params[':id_usuario'] = $filtros['cliente'];
                }

                // Filtro por ID de pedido
                if (isset($filtros['id_pedido']) && !empty($filtros['id_pedido'])) {
                    $condiciones[] = "p.id = :id_pedido";
                    $params[':id_pedido'] = $filtros['id_pedido'];
                }

                // Añadir condiciones a la consulta
                if (!empty($condiciones)) {
                    $query .= " WHERE " . implode(" AND ", $condiciones);
                }
            }

            $stmt = $this->conn->prepare($query);

            // Bind params
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Error contando pedidos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener la lista de clientes que han realizado pedidos
     * 
     * @return array Lista de clientes
     */
    public function getClientesConPedidos()
    {
        try {
            $query = "SELECT DISTINCT u.id, u.nombre, u.apellidos
                     FROM usuarios u
                     JOIN pedidos p ON u.id = p.id_usuario
                     ORDER BY u.nombre, u.apellidos";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo clientes con pedidos: " . $e->getMessage());
            return [];
        }
    }
}
