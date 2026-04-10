<?php
/**
 * Controlador de Entregas - ESE Fabio Jaramillo
 * Lógica de negocio para dispensación (SRP).
 */
require_once __DIR__ . '/../Models/DeliveryModel.php';
require_once __DIR__ . '/../Models/InventoryModel.php';

class DeliveryController {
    
    private $model;
    private $inventoryModel;

    public function __construct() {
        $this->model = new DeliveryModel();
        $this->inventoryModel = new InventoryModel();
    }

    public function processDelivery($data) {
        // Validar Stock
        $currentStock = $this->inventoryModel->getStockAtSede($data['sede_id'], $data['producto_id']);
        if ($currentStock < $data['cantidad']) {
            return ['status' => 'error', 'message' => 'Stock insuficiente para esta entrega.'];
        }

        // Lógica de Registro
        try {
            if ($this->model->record($data)) {
                return ['status' => 'success', 'message' => 'Dispensación registrada y stock actualizado.'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Falla en la transacción: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Error inesperado.'];
    }

    public function getHistory($sede_id) {
        return $this->model->getHistoryBySede($sede_id);
    }
}
