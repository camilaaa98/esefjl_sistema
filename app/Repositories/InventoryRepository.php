<?php
require_once __DIR__ . '/BaseRepository.php';

class InventoryRepository extends BaseRepository {
    
    public function getFilteredStock($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT p.id, p.nombre_generico, p.nombre_comercial, p.concentracion_presentacion, p.laboratorio,
                       p.requiere_frio, p.es_delicado, p.imagen_url, p.valor_unitario, p.descripcion_breve,
                       i.stock_actual, i.stock_minimo, i.fecha_vencimiento, i.lote, s.nombre as sede_nombre, s.stock_minimo_referencia,
                       s.id as sede_id
                FROM productos p 
                JOIN inventario i ON p.id = i.producto_id 
                JOIN sedes s ON i.sede_id = s.id";
        
        $where = [];
        $params = [];

        if (!empty($filters['sede_id'])) {
            $where[] = "i.sede_id = ?";
            $params[] = $filters['sede_id'];
        }
        if (!empty($filters['laboratorio'])) {
            $where[] = "p.laboratorio LIKE ?";
            $params[] = "%" . $filters['laboratorio'] . "%";
        }
        if (!empty($filters['query'])) {
            $where[] = "(p.nombre_generico LIKE ? OR p.nombre_comercial LIKE ?)";
            $params[] = "%" . $filters['query'] . "%";
            $params[] = "%" . $filters['query'] . "%";
        }

        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $today = ($driver === 'sqlite') ? "DATE('now')" : "CURRENT_DATE";

        // EXCLUSIí“N AUTOMíTICA DE VENCIDOS (Regla ESEFJL)
        $where[] = "(i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= $today)";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY s.nombre ASC, p.nombre_generico ASC LIMIT $limit OFFSET $offset";
        
        return $this->query($sql, $params)->fetchAll();
    }

    public function getFilteredStockCount($filters = []) {
        $sql = "SELECT COUNT(*) FROM inventario i 
                JOIN productos p ON i.producto_id = p.id
                JOIN sedes s ON i.sede_id = s.id";
        $where = [];
        $params = [];
        if (!empty($filters['sede_id'])) {
            $where[] = "i.sede_id = ?";
            $params[] = $filters['sede_id'];
        }
        if (!empty($filters['laboratorio'])) {
            $where[] = "p.laboratorio LIKE ?";
            $params[] = "%" . $filters['laboratorio'] . "%";
        }
        if (!empty($filters['query'])) {
            $where[] = "(p.nombre_generico LIKE ? OR p.nombre_comercial LIKE ?)";
            $params[] = "%" . $filters['query'] . "%";
            $params[] = "%" . $filters['query'] . "%";
        }
        
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $today = ($driver === 'sqlite') ? "DATE('now')" : "CURRENT_DATE";

        // EXCLUSIí“N AUTOMíTICA DE VENCIDOS
        $where[] = "(i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= $today)";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        return $this->query($sql, $params)->fetchColumn();
    }

    public function getExpiringSoon($days = 90, $sede_id = null) {
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        // Lógica de cálculo de días compatible con ambos motores
        if ($driver === 'sqlite') {
            $diffSql = "(julianday(i.fecha_vencimiento) - julianday('now'))";
        } else {
            // PostgreSQL
            $diffSql = "(i.fecha_vencimiento::date - CURRENT_DATE)";
        }

        $sql = "SELECT p.nombre_generico, i.lote, i.fecha_vencimiento, s.nombre as sede_nombre,
                       $diffSql as dias_restantes
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sedes s ON i.sede_id = s.id
                WHERE $diffSql <= ?";
        
        $params = [$days];
        if ($sede_id) {
            $sql .= " AND i.sede_id = ?";
            $params[] = $sede_id;
        }
        
        return $this->query($sql, $params)->fetchAll();
    }

    public function getInventoryBySede($sede_id) {
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $today = ($driver === 'sqlite') ? "DATE('now')" : "CURRENT_DATE";

        $sql = "SELECT i.*, p.nombre_generico, p.unidad_medida, c.nombre as categoria, p.laboratorio, p.concentracion_presentacion
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN categorias c ON p.categoria_id = c.id
                WHERE i.sede_id = ? AND (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= $today)
                ORDER BY i.fecha_vencimiento ASC";
        return $this->query($sql, [$sede_id])->fetchAll();
    }

    public function getExpired() {
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $today = ($driver === 'sqlite') ? "DATE('now')" : "CURRENT_DATE";

        $sql = "SELECT i.*, p.nombre_generico, p.laboratorio, s.nombre as sede_nombre, prov.razon_social as proveedor_nombre
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sedes s ON i.sede_id = s.id
                LEFT JOIN proveedores prov ON p.laboratorio = prov.razon_social
                WHERE i.fecha_vencimiento < $today
                ORDER BY i.fecha_vencimiento DESC";
        return $this->query($sql)->fetchAll();
    }

    public function getFaltantesMunicipales() {
        // Corrección de MAX() anidado: Usar CASE WHEN para compatibilidad universal
        $sql = "SELECT i.producto_id, 
                       SUM(CASE WHEN i.stock_actual < i.stock_minimo THEN i.stock_minimo - i.stock_actual ELSE 0 END) as total_faltante
                FROM inventario i
                JOIN sedes s ON i.sede_id = s.id
                WHERE s.tipo = 'MUNICIPIO'
                GROUP BY i.producto_id
                HAVING SUM(CASE WHEN i.stock_actual < i.stock_minimo THEN i.stock_minimo - i.stock_actual ELSE 0 END) > 0";
        return $this->query($sql)->fetchAll();
    }

    public function deleteItem($id) {
        $sql = "DELETE FROM inventario WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}

