<?php
/**
 * Controlador de Inventario - ESE Fabio Jaramillo
 */
require_once __DIR__ . '/Database.php';

class InventoryController {
    
    public static function getInventoryBySede($sede_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.*, p.nombre_generico, p.unidad_medida, c.nombre as categoria
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            JOIN categorias c ON p.categoria_id = c.id
            WHERE i.sede_id = ?
            ORDER BY i.fecha_vencimiento ASC
        ");
        $stmt->execute([$sede_id]);
        return $stmt->fetchAll();
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
            return '<span class="badge sema-red">STOCK CRíTICO</span>';
        } elseif ($current < $min) {
            return '<span class="badge sema-yellow">STOCK BAJO</span>';
        } else {
            return '<span class="badge sema-green">ÓPTIMO</span>';
        }
    }

    public static function seedInitialProducts() {
        // ... (Este método ya fue ejecutado o se reemplaza por mega_seed.php)
    }
}
?>
