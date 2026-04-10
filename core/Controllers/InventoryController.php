<?php
/**
 * Controlador de Inventario - ESE Fabio Jaramillo
 * Siguiendo el principio de Responsabilidad Única (SRP).
 * Delegando el acceso a datos al Modelo.
 */
require_once __DIR__ . '/../Models/InventoryModel.php';

class InventoryController {
    
    private $model;

    public function __construct() {
        $this->model = new InventoryModel();
    }

    public function getInventoryBySede($sede_id) {
        return $this->model->getBySede($sede_id);
    }

    public function getAllIPSInventory() {
        return $this->model->getAllIPS();
    }

    public function getExpiredInventory() {
        return $this->model->getExpired();
    }

    /**
     * Lógica de Disponibilidad Centralizada
     */
    public function canSupplyAllIPS() {
        $faltantes = $this->model->getFaltantesMuncipales();
        if (empty($faltantes)) return true;

        $florencia_id = $this->model->getFlorenciaId();
        
        foreach ($faltantes as $f) {
            $stockCedis = $this->model->getStockAtSede($florencia_id, $f['producto_id']);
            if ($stockCedis < $f['total_faltante']) return false;
        }

        return true;
    }
}
