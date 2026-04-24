<?php
/**
 * Controlador de Pedidos y Suministro IPS - SISFARMA PRO
 */
require_once __DIR__ . '/../Infrastructure/Database.php';

class RequestController {

    public static function createAutomaticOrder($sede_id) {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            // 1. Identificar productos bajo el stock mínimo
            $stmt = $db->prepare("
                SELECT producto_id, stock_actual, stock_minimo 
                FROM inventario 
                WHERE sede_id = ? AND stock_actual < stock_minimo
            ");
            $stmt->execute([$sede_id]);
            $items = $stmt->fetchAll();

            if (empty($items)) {
                $db->rollBack();
                return ['success' => false, 'message' => 'El inventario está en niveles óptimos. No se requiere pedido automático.'];
            }

            // 2. Crear cabecera de pedido
            $stmt = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado) VALUES (?, 'PENDIENTE') RETURNING id");
            $stmt->execute([$sede_id]);
            $pedido_id = $stmt->fetchColumn();

            // 3. Crear detalles (Pedir hasta completar el doble del mínimo para asegurar stock)
            $stmtDet = $db->prepare("INSERT INTO detalles_pedido_municipio (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $cantidad_pedir = ($item['stock_minimo'] * 2) - $item['stock_actual'];
                $stmtDet->execute([$pedido_id, $item['producto_id'], $cantidad_pedir]);
            }

            $db->commit();
            return ['success' => true, 'message' => 'Se ha generado un pedido automático de ' . count($items) . ' insumos críticos al CEDIS.'];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public static function createManualOrder($sede_id, $producto_id, $cantidad) {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado) VALUES (?, 'PENDIENTE') RETURNING id");
            $stmt->execute([$sede_id]);
            $pedido_id = $stmt->fetchColumn();

            $stmtDet = $db->prepare("INSERT INTO detalles_pedido_municipio (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
            $stmtDet->execute([$pedido_id, $producto_id, $cantidad]);

            $db->commit();
            return ['success' => true, 'message' => 'Solicitud manual registrada exitosamente.'];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
