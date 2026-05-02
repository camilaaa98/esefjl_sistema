<?php
/**
 * Controlador de Pedidos y Suministro IPS - FARMACIA ESEFJL
 */
require_once __DIR__ . '/../config/Database.php';

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
            $stmt = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado) VALUES (?, 'PENDIENTE')");
            $stmt->execute([$sede_id]);
            $pedido_id = $db->lastInsertId();

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

            $stmt = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado) VALUES (?, 'PENDIENTE')");
            $stmt->execute([$sede_id]);
            $pedido_id = $db->lastInsertId();

            $stmtDet = $db->prepare("INSERT INTO detalles_pedido_municipio (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
            $stmtDet->execute([$pedido_id, $producto_id, $cantidad]);

            $db->commit();
            return ['success' => true, 'message' => 'Solicitud manual registrada exitosamente.'];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    public static function approveOrder($pedido_id) {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            // 1. Obtener ID de la Sede Administrativa (Florencia)
            $florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
            
            // 2. Obtener detalles del pedido
            $stmt = $db->prepare("SELECT producto_id, cantidad FROM detalles_pedido_municipio WHERE pedido_id = ?");
            $stmt->execute([$pedido_id]);
            $detalles = $stmt->fetchAll();

            // 3. Verificar stock en Florencia para CADA item
            foreach ($detalles as $item) {
                $stmtStock = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
                $stmtStock->execute([$florencia_id, $item['producto_id']]);
                $stockFlorencia = $stmtStock->fetchColumn() ?: 0;

                if ($stockFlorencia < $item['cantidad']) {
                    $db->rollBack();
                    return [
                        'success' => false, 
                        'message' => "ERROR: El CEDIS (Florencia) no tiene stock suficiente para este despacho. Faltan unidades del Producto ID: {$item['producto_id']}"
                    ];
                }
            }

            // 4. Descontar stock de Florencia
            foreach ($detalles as $item) {
                $db->prepare("UPDATE inventario SET stock_actual = stock_actual - ? WHERE sede_id = ? AND producto_id = ?")
                   ->execute([$item['cantidad'], $florencia_id, $item['producto_id']]);
            }

            // 5. Actualizar estado del pedido
            $db->prepare("UPDATE pedidos_municipios SET estado = 'DESPACHADO' WHERE id = ?")->execute([$pedido_id]);

            $db->commit();
            return ['success' => true, 'message' => 'Despacho autorizado. El stock ha sido descontado del CEDIS Florencia.'];

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

