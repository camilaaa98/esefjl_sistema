<?php
/**
 * Controlador de Entregas - ESE Fabio Jaramillo
 */
require_once __DIR__ . '/../Infrastructure/Database.php';

class DeliveryController {
    
    public static function processDelivery($paciente_id, $producto_id, $cantidad, $sede_id) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            // 1. Verificar stock actual
            $stmt = $db->prepare("SELECT stock_actual, lote FROM inventario WHERE sede_id = ? AND producto_id = ?");
            $stmt->execute([$sede_id, $producto_id]);
            $inv = $stmt->fetch();

            if (!$inv || $inv['stock_actual'] < $cantidad) {
                return ['success' => false, 'message' => 'Stock insuficiente en esta sede.'];
            }

            // 2. Descontar del inventario
            $new_stock = $inv['stock_actual'] - $cantidad;
            $stmt = $db->prepare("UPDATE inventario SET stock_actual = ? WHERE sede_id = ? AND producto_id = ?");
            $stmt->execute([$new_stock, $sede_id, $producto_id]);

            // 3. Registrar entrega
            $stmt = $db->prepare("INSERT INTO entregas (paciente_id, producto_id, cantidad, sede_id, fecha_entrega, estado) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, 'ENTREGADO')");
            $stmt->execute([$paciente_id, $producto_id, $cantidad, $sede_id]);

            // 4. Simulación de Notificación / Riesgos
            $paciente = $db->prepare("SELECT nombres, celular FROM pacientes WHERE documento = ?");
            $paciente->execute([$paciente_id]);
            $p_info = $paciente->fetch();
            
            $sms_preview = "ESE FJL: Hola {$p_info['nombres']}, se ha entregado satisfactoriamente su medicamento. Lote: {$inv['lote']}.";
            
            $db->commit();

            // Lógica de Riesgos: Si el modo contingencia está activo (vía sesión sim)
            if (isset($_SESSION['modo_contingencia']) && $_SESSION['modo_contingencia'] === true) {
                return [
                    'success' => true, 
                    'message' => 'Entrega registrada localmente. (API FALLANDO - Almacenado en Cola)',
                    'preview' => "[MODO CONTINGENCIA ACTIVO] '{$sms_preview}' guardado para envío diferido."
                ];
            }

            return [
                'success' => true, 
                'message' => 'Medicamento entregado y paciente notificado.',
                'preview' => "MENSAJE ENVIADO: '{$sms_preview}'"
            ];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
