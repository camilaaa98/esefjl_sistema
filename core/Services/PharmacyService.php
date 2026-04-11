<?php
/**
 * Servicio Central de Farmacia (SOLID - SRP)
 * Maneja la lógica de negocio técnica de la ESE.
 */
class PharmacyService {
    private $inventoryRepo;

    public function __construct(InventoryRepository $inventoryRepo) {
        $this->inventoryRepo = $inventoryRepo;
    }

    /**
     * Calcula el estado de alerta basado en fecha de vencimiento.
     */
    public function getStockStatus($item) {
        $days = $item['dias_restantes'] ?? null;
        
        if ($days === null) return 'green';
        if ($days < 90) return 'red';
        if ($days < 180) return 'orange';
        return 'green';
    }

    public function getGlobalAlerts($sede_id = null) {
        $expiring = $this->inventoryRepo->getExpiringSoon(180, $sede_id);
        
        $alerts = [
            'critical' => [],
            'warning' => []
        ];

        foreach ($expiring as $item) {
            $status = $this->getStockStatus($item);
            if ($status == 'red') $alerts['critical'][] = $item;
            else if ($status == 'orange') $alerts['warning'][] = $item;
        }

        return $alerts;
    }
}
?>
