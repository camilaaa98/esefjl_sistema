<?php
/**
 * Modelo de Entregas - ESE Fabio Jaramillo
 * Siguiendo SRP: Encapsulación de lógica de dispensación de medicamentos.
 */
require_once __DIR__ . '/../Database.php';

class DeliveryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function record($data) {
        $this->db->beginTransaction();
        try {
            // 1. Registrar la entrega
            $stmt = $this->db->prepare("
                INSERT INTO entregas (
                    paciente_id, inventario_id, cantidad_entregada, 
                    fecha_entrega, usuario_id, sede_id
                ) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?)
            ");
            $stmt->execute([
                $data['paciente_id'], $data['inventario_id'], 
                $data['cantidad'], $data['usuario_id'], $data['sede_id']
            ]);

            // 2. Descontar del inventario (Stock)
            $stmtUpdate = $this->db->prepare("
                UPDATE inventario 
                SET stock_actual = stock_actual - ? 
                WHERE id = ?
            ");
            $stmtUpdate->execute([$data['cantidad'], $data['inventario_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getHistoryBySede($sede_id) {
        $stmt = $this->db->prepare("
            SELECT e.*, p.nombre_completo as paciente, prod.nombre_generico as medicamento
            FROM entregas e
            JOIN pacientes p ON e.paciente_id = p.id
            JOIN inventario i ON e.inventario_id = i.id
            JOIN productos prod ON i.producto_id = prod.id
            WHERE e.sede_id = ?
            ORDER BY e.fecha_entrega DESC
            LIMIT 50
        ");
        $stmt->execute([$sede_id]);
        return $stmt->fetchAll();
    }
}
