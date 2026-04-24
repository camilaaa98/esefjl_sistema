<?php
/**
 * Controlador de Municipios - ESE Fabio Jaramillo
 * Gestiona el flujo de información "a un clic" desde municipios a sede principal.
 */
require_once __DIR__ . '/../Infrastructure/Database.php';

class MunicipalityController {

    public static function createAutoRequest($sede_id) {
        $db = Database::getInstance();
        
        // 1. Obtener productos por debajo del tope
        $stmt = $db->prepare("
            SELECT producto_id, stock_actual, stock_minimo 
            FROM inventario 
            WHERE sede_id = ? AND stock_actual < stock_minimo
        ");
        $stmt->execute([$sede_id]);
        $faltantes = $stmt->fetchAll();

        if (empty($faltantes)) {
            return ['success' => false, 'message' => 'Stock completo en esta sede'];
        }

        try {
            $db->beginTransaction();
            
            // 2. Crear solicitud principal
            $stmtPedido = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado) VALUES (?, 'PENDIENTE')");
            $stmtPedido->execute([$sede_id]);
            $pedido_id = $db->lastInsertId();

            // 3. (Opcional) Guardar detalles de productos solicitados
            // Aquí podríamos crear una tabla 'detalles_pedidos' para mayor profesionalismo

            $db->commit();
            return ['success' => true, 'message' => 'Solicitud enviada automáticamente al Regente', 'id' => $pedido_id];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
