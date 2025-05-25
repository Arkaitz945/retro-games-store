<?php

require_once __DIR__ . "/../../model/PedidosModel.php";

class PedidosAdminController
{
    private $pedidosModel;

    public function __construct()
    {
        $this->pedidosModel = new PedidosModel();
    }

    /**
     * Obtiene todos los pedidos
     */
    public function getAllPedidos($filtros = [])
    {
        $pedidos = $this->pedidosModel->getAllPedidos($filtros);

        // Depurar en el controlador
        error_log("Controlador: Recibidos " . count($pedidos) . " pedidos del modelo");

        return $pedidos;
    }

    /**
     * Contar total de pedidos según filtros
     */
    public function countPedidos($filtros = [])
    {
        return $this->pedidosModel->countPedidos($filtros);
    }

    /**
     * Obtiene la lista de clientes que han realizado pedidos
     */
    public function getClientesConPedidos()
    {
        return $this->pedidosModel->getClientesConPedidos();
    }

    /**
     * Obtiene un pedido por su ID
     */
    public function getPedidoById($id)
    {
        // Validar que el ID es un número
        if (!is_numeric($id)) {
            error_log("ID de pedido no válido: " . $id);
            return false;
        }

        $id = intval($id); // Asegurar que es un entero
        error_log("Buscando pedido con ID (controlador): " . $id);

        $pedido = $this->pedidosModel->getPedidoById($id);

        if ($pedido) {
            error_log("Pedido encontrado en controlador para ID: " . $id);
        } else {
            error_log("Pedido NO encontrado en controlador para ID: " . $id);

            // Depuración adicional - verificar si el pedido existe directamente
            try {
                $conn = getDBConnection();
                $query = "SELECT COUNT(*) as total FROM pedidos WHERE ID_Pedido = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                error_log("Verificación directa: Existen " . $result['total'] . " pedidos con ID " . $id);
            } catch (Exception $e) {
                error_log("Error en verificación directa: " . $e->getMessage());
            }
        }

        return $pedido;
    }

    /**
     * Obtiene los detalles de un pedido
     */
    public function getDetallesPedido($id)
    {
        return $this->pedidosModel->getDetallesPedido($id);
    }

    /**
     * Actualiza el estado de un pedido
     */
    public function updateEstadoPedido($id, $estado)
    {
        // Validar estado
        $estadosValidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];

        if (!in_array(strtolower($estado), $estadosValidos)) {
            error_log("Estado no válido: $estado");
            return [
                'success' => false,
                'message' => 'Estado no válido'
            ];
        }

        // Convertir el ID a entero
        $id = intval($id);

        // Validar que el ID existe
        if (!$this->getPedidoById($id)) {
            error_log("Pedido no encontrado para actualizar estado. ID: $id");
            return [
                'success' => false,
                'message' => 'Pedido no encontrado'
            ];
        }

        // Intentar actualizar el estado
        $result = $this->pedidosModel->updateEstadoPedido($id, $estado);

        if ($result) {
            error_log("Estado del pedido actualizado correctamente. ID: $id, Estado: $estado");
            return [
                'success' => true,
                'message' => 'Estado del pedido actualizado correctamente'
            ];
        } else {
            error_log("Error al actualizar el estado del pedido. ID: $id, Estado: $estado");
            return [
                'success' => false,
                'message' => 'Error al actualizar el estado del pedido'
            ];
        }
    }

    /**
     * Obtiene los estados de pedidos
     */
    public function getEstadosPedidos()
    {
        return $this->pedidosModel->getEstadosPedidos();
    }

    /**
     * Obtiene estadísticas de ventas
     */
    public function getEstadisticasVentas($periodo = 'mes')
    {
        return $this->pedidosModel->getEstadisticasVentas($periodo);
    }

    /**
     * Obtiene los productos más vendidos
     */
    public function getProductosMasVendidos($limit = 10)
    {
        return $this->pedidosModel->getProductosMasVendidos($limit);
    }
}
