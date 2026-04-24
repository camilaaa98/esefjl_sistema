<?php
require_once __DIR__ . '/BaseRepository.php';

class InventoryRepository extends BaseRepository {
    
    public function getAllStock($sede_id = null) {
        $sql = "SELECT p.id, p.nombre_generico, p.nombre_comercial, p.concentracion_presentacion, 
                       i.stock_actual, i.fecha_vencimiento, i.lote, s.nombre as sede_nombre 
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
        $sql = "SELECT p.nombre_generico, i.lote, i.fecha_vencimiento, s.nombre as sede_nombre,
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

    public function getInventoryBySede($sede_id) {
        $sql = "SELECT i.*, p.nombre_generico, p.unidad_medida, c.nombre as categoria, p.laboratorio, p.concentracion_presentacion
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN categorias c ON p.categoria_id = c.id
                WHERE i.sede_id = ? AND (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= DATE('now'))
                ORDER BY i.fecha_vencimiento ASC";
        return $this->query($sql, [$sede_id])->fetchAll();
    }

    public function getExpired() {
        $sql = "SELECT i.*, p.nombre_generico, p.laboratorio, s.nombre as sede_nombre, prov.razon_social as proveedor_nombre
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sedes s ON i.sede_id = s.id
                LEFT JOIN proveedores prov ON p.laboratorio = prov.razon_social
                WHERE i.fecha_vencimiento < DATE('now')
                ORDER BY i.fecha_vencimiento DESC";
        return $this->query($sql)->fetchAll();
    }

    public function getFaltantesMunicipales() {
        $sql = "SELECT i.producto_id, SUM(MAX(0, i.stock_minimo - i.stock_actual)) as total_faltante
                FROM inventario i
                JOIN sedes s ON i.sede_id = s.id
                WHERE s.tipo = 'MUNICIPIO'
                GROUP BY i.producto_id
                HAVING SUM(MAX(0, i.stock_minimo - i.stock_actual)) > 0";
        return $this->query($sql)->fetchAll();
    }
}
?>
