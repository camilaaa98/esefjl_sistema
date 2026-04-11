<?php
require_once __DIR__ . '/BaseRepository.php';

class InventoryRepository extends BaseRepository {
    
    public function getAllStock($sede_id = null) {
        $sql = "SELECT p.*, i.stock, i.fecha_vencimiento, i.lote, s.nombre as sede_nombre 
                FROM productos p 
                JOIN inventario i ON p.id = i.producto_id 
                JOIN sedes s ON i.sede_id = s.id";
        
        $params = [];
        if ($sede_id) {
            $sql .= " WHERE i.sede_id = ?";
            $params = [$sede_id];
        }
        
        return $this->query($sql, $params)->fetchAll();
    }

    public function getExpiringSoon($days = 90, $sede_id = null) {
        $sql = "SELECT p.nombre, i.lote, i.fecha_vencimiento, s.nombre as sede_nombre,
                       (julianday(i.fecha_vencimiento) - julianday('now')) as dias_restantes
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sedes s ON i.sede_id = s.id
                WHERE (julianday(i.fecha_vencimiento) - julianday('now')) <= ?";
        
        $params = [$days];
        if ($sede_id) {
            $sql .= " AND i.sede_id = ?";
            $params[] = $sede_id;
        }
        
        return $this->query($sql, $params)->fetchAll();
    }
}
?>
