<?php
require_once __DIR__ . '/BaseRepository.php';

class DeliveryRepository extends BaseRepository {
    
    public function logDelivery($data) {
        $sql = "INSERT INTO entregas (paciente_id, producto_id, cantidad, lote, fecha_entrega, usuario_id, sede_id) 
                VALUES (?, ?, ?, ?, datetime('now'), ?, ?)";
        return $this->query($sql, [
            $data['paciente_id'], $data['producto_id'], 
            $data['cantidad'], $data['lote'], 
            $data['usuario_id'], $data['sede_id']
        ]);
    }

    public function getHistory($sede_id = null) {
        $sql = "SELECT e.*, p.nombres as paciente_nombre, pr.nombre as producto_nombre 
                FROM entregas e 
                JOIN pacientes p ON e.paciente_id = p.documento 
                JOIN productos pr ON e.producto_id = pr.id";
        
        if ($sede_id) {
            $sql .= " WHERE e.sede_id = ?";
            return $this->query($sql, [$sede_id])->fetchAll();
        }
        return $this->query($sql)->fetchAll();
    }
}
?>
