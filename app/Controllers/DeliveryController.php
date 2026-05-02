<?php
/**
 * Controlador de Entregas - ESE Fabio Jaramillo
 * CORREGIDO: Firma de método unificada, validación de copago y reclamación cruzada.
 */
require_once __DIR__ . '/../config/Database.php';

class DeliveryController {
    
    public static function processDelivery($data) {
        $db = Database::getInstance();
        
        $paciente_id = $data['paciente_id'] ?? null;
        $producto_id = $data['producto_id'] ?? null;
        $cantidad    = $data['cantidad']    ?? 0;
        $sede_id     = $data['sede_id']     ?? null;
        $numero_orden = $data['numero_orden'] ?? 'ORD-' . time();
        $copago_pagado = isset($data['copago_pagado']) && $data['copago_pagado'] == '1';

        if (!$paciente_id || !$producto_id || $cantidad <= 0 || !$sede_id) {
            return ['success' => false, 'message' => 'Datos incompletos para procesar la entrega.'];
        }

        try {
            $db->beginTransaction();

            // 1. VALIDACIí“N DE RECLAMACIí“N CRUZADA Y ORDEN íšNICA
            // Se bloquea si el paciente ya reclamó el producto este mes O si el número de orden ya fue usado.
            $stmtCheck = $db->prepare("
                SELECT s.nombre as sede_donde_reclamo, e.fecha_entrega, e.numero_orden
                FROM entregas e 
                JOIN sedes s ON e.sede_id = s.id
                WHERE (e.paciente_id = ? AND e.producto_id = ? AND strftime('%Y-%m', e.fecha_entrega) = strftime('%Y-%m', 'now'))
                OR (e.numero_orden = ?)
                LIMIT 1
            ");
            $stmtCheck->execute([$paciente_id, $producto_id, $numero_orden]);
            $prev = $stmtCheck->fetch();
            
            if ($prev) {
                if ($prev['numero_orden'] === $numero_orden) {
                    return ['success' => false, 'message' => "BLOQUEO: Esta orden médica (#$numero_orden) ya fue procesada anteriormente."];
                }
                return [
                    'success' => false, 
                    'message' => "BLOQUEO: El paciente ya reclamó este medicamento este mes en la sede {$prev['sede_donde_reclamo']} el día {$prev['fecha_entrega']}."
                ];
            }

            // 2. VALIDACIí“N DE COPAGO / EXENCIí“N (Ley 1448)
            $stmtP = $db->prepare("SELECT nombres, apellidos, regimen, es_desplazado FROM pacientes WHERE documento = ?");
            $stmtP->execute([$paciente_id]);
            $paciente = $stmtP->fetch();
            
            if (!$paciente) {
                return ['success' => false, 'message' => 'El paciente no está registrado en el sistema.'];
            }

            $monto_copago = 0;
            $motivo_exencion = '';
            
            if ($paciente['es_desplazado']) {
                $motivo_exencion = 'Exento por Ley 1448 (Víctima del conflicto)';
            } elseif ($paciente['regimen'] === 'SUBSIDIADO') {
                $motivo_exencion = 'Exento por Régimen Subsidiado';
            } else {
                // Cálculo de copago simplificado para contributivo
                $monto_copago = 4500.00; // Valor base simulación
            }

            // Control de Copago: Si requiere copago y no se marcó como pagado, alertar o registrar como pendiente
            $estado_copago = $copago_pagado ? 'PAGADO' : 'PENDIENTE';

            // 3. VERIFICAR STOCK
            $stmtS = $db->prepare("SELECT stock_actual, lote FROM inventario WHERE sede_id = ? AND producto_id = ?");
            $stmtS->execute([$sede_id, $producto_id]);
            $inv = $stmtS->fetch();

            if (!$inv || $inv['stock_actual'] < $cantidad) {
                return ['success' => false, 'message' => 'Stock insuficiente en esta sede para completar la entrega.'];
            }

            // 4. REGISTRAR COPAGO SI APLICA
            if ($monto_copago > 0) {
                $stmtC = $db->prepare("INSERT INTO copagos (paciente_id, monto, estado, fecha_registro) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
                $stmtC->execute([$paciente_id, $monto_copago, $estado_copago]);
            }

            // 5. DESCONTAR STOCK
            $new_stock = $inv['stock_actual'] - $cantidad;
            $stmtU = $db->prepare("UPDATE inventario SET stock_actual = ? WHERE sede_id = ? AND producto_id = ?");
            $stmtU->execute([$new_stock, $sede_id, $producto_id]);

            // 6. REGISTRAR ENTREGA
            $stmtE = $db->prepare("
                INSERT INTO entregas (paciente_id, producto_id, cantidad, sede_id, fecha_entrega, numero_orden, estado) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?, 'ENTREGADO')
            ");
            $stmtE->execute([$paciente_id, $producto_id, $cantidad, $sede_id, $numero_orden]);

            $db->commit();

            $msg = "Entrega exitosa. " . ($monto_copago > 0 ? "COPAGO $estado_copago: $" . number_format($monto_copago, 0) : "EXENTO: $motivo_exencion");

            return [
                'success' => true, 
                'message' => $msg,
                'preview' => "Notificación enviada a {$paciente['nombres']}: Lote {$inv['lote']} entregado con orden $numero_orden."
            ];

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            return ['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()];
        }
    }
}
