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
        return $this->pedidosModel->getAllPedidos($filtros);
    }

    /**
     * Obtiene un pedido por su ID
     */
    public function getPedidoById($id)
    {
        return $this->pedidosModel->getPedidoById($id);
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

        if (!in_array($estado, $estadosValidos)) {
            return [
                'success' => false,
                'message' => 'Estado no válido'
            ];
        }

        $result = $this->pedidosModel->updateEstadoPedido($id, $estado);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Estado del pedido actualizado correctamente'
            ];
        } else {
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
