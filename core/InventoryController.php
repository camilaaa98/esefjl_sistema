<?php
/**
 * Controlador de Inventario - ESE Fabio Jaramillo
 * Refactorizado para usar Repositorios (SOLID) y eliminar redundancias.
 */
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Repositories/InventoryRepository.php';

class InventoryController {
    
    private static function getRepository() {
        return new InventoryRepository(Database::getInstance());
    }

    public static function getInventoryBySede($sede_id) {
        return self::getRepository()->getInventoryBySede($sede_id);
    }

    public static function getStatusBadge($current, $min, $expiry = null) {
        $today = date('Y-m-d');
        $warning_date = date('Y-m-d', strtotime('+3 months'));

        if ($expiry && $expiry < $today) {
            return '<span class="badge sema-red" style="background:#b71c1c; color:white;">VENCIDO</span>';
        } elseif ($expiry && $expiry < $warning_date) {
            return '<span class="badge sema-yellow" style="background:#fbc02d; color:black;">POR VENCER</span>';
        }

        if ($current <= ($min * 0.25)) {
            return '<span class="badge sema-red">STOCK CRÍTICO</span>';
        } elseif ($current < $min) {
            return '<span class="badge sema-yellow">STOCK BAJO</span>';
        } else {
            return '<span class="badge sema-green">ÓPTIMO</span>';
        }
    }

    public static function getAllIPSInventory() {
        return self::getRepository()->getAllStock(); // O usar un método específico si se prefiere
    }

    public static function getExpiredInventory() {
        return self::getRepository()->getExpired();
    }

    public static function canSupplyAllIPS() {
        $repo = self::getRepository();
        $faltantes = $repo->getFaltantesMunicipales();

        if (empty($faltantes)) return true;

        $db = Database::getInstance();
        $florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
        
        foreach ($faltantes as $f) {
            $stmtCedis = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
            $stmtCedis->execute([$florencia_id, $f['producto_id']]);
            $stockCedis = $stmtCedis->fetchColumn() ?: 0;
            
            if ($stockCedis < $f['total_faltante']) return false;
        }

        return true;
    }
}
?>
