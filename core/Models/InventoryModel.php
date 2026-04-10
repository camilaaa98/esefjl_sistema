<?php
/**
 * Modelo de Inventario - ESE Fabio Jaramillo
 * Siguiendo el principio de Responsabilidad Única (SRP).
 */
require_once __DIR__ . '/../Database.php';

class InventoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getBySede($sede_id) {
        $stmt = $this->db->prepare("
            SELECT i.*, p.nombre_generico, p.unidad_medida, c.nombre as categoria, p.laboratorio, p.concentracion_presentacion
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            JOIN categorias c ON p.categoria_id = c.id
            WHERE i.sede_id = ? AND i.fecha_vencimiento >= CURRENT_DATE
            ORDER BY i.fecha_vencimiento ASC
        ");
        $stmt->execute([$sede_id]);
        return $stmt->fetchAll();
    }

    public function getAllIPS() {
        $stmt = $this->db->query("
            SELECT s.nombre as sede_nombre, p.nombre_generico, p.laboratorio, i.stock_actual, i.stock_minimo, i.fecha_vencimiento
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            JOIN sedes s ON i.sede_id = s.id
            WHERE s.tipo = 'MUNICIPIO' AND i.fecha_vencimiento >= CURRENT_DATE
            ORDER BY s.nombre, p.nombre_generico
        ");
        return $stmt->fetchAll();
    }

    public function getExpired() {
        $stmt = $this->db->query("
            SELECT i.*, p.nombre_generico, p.laboratorio, s.nombre as sede_nombre, prov.razon_social as proveedor_nombre
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            JOIN sedes s ON i.sede_id = s.id
            LEFT JOIN proveedores prov ON p.laboratorio = prov.razon_social
            WHERE i.fecha_vencimiento < CURRENT_DATE
            ORDER BY i.fecha_vencimiento DESC
        ");
        return $stmt->fetchAll();
    }

    public function getFaltantesMuncipales() {
        $stmt = $this->db->query("
            SELECT i.producto_id, SUM(MAX(0, i.stock_minimo - i.stock_actual)) as total_faltante
            FROM inventario i
            JOIN sedes s ON i.sede_id = s.id
            WHERE s.tipo = 'MUNICIPIO'
            GROUP BY i.producto_id
            HAVING SUM(MAX(0, i.stock_minimo - i.stock_actual)) > 0
        ");
        return $stmt->fetchAll();
    }

    public function getStockAtSede($sede_id, $producto_id) {
        $stmt = $this->db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
        $stmt->execute([$sede_id, $producto_id]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getFlorenciaId() {
        return $this->db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
    }
}
