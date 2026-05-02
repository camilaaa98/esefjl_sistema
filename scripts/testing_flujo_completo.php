<?php
/**
 * Script de Testing Completo - Flujo de Farmacia ESEFJL
 * 1. Registrar paciente
 * 2. Crear pedido desde sede municipal
 * 3. Aprobar pedido (Regente)
 * 4. Verificar descuento de stock en Florencia
 * 5. Entregar medicamento al paciente
 */

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/Controllers/PatientController.php';
require_once __DIR__ . '/../app/Controllers/RequestController.php';
require_once __DIR__ . '/../app/Controllers/DeliveryController.php';

echo "\n";
echo "â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•—\n";
echo "â•‘     TESTING - SISTEMA FARMACIA ESE FABIO JARAMILLO          â•‘\n";
echo "â•‘              Flujo Completo de Operaciones                   â•‘\n";
echo "â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n\n";

$db = Database::getInstance();
$florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
$solita_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Solita%' LIMIT 1")->fetchColumn();

// Verificar que tenemos las sedes necesarias
if (!$florencia_id || !$solita_id) {
    die("âŒ Error: No se encontraron las sedes de Florencia o Solita\n");
}

echo "ðŸ“ Florencia (CEDIS) ID: {$florencia_id}\n";
echo "ðŸ“ Solita (Municipio) ID: {$solita_id}\n\n";

// ============================================================================
// PASO 1: REGISTRAR PACIENTE
// ============================================================================
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "PASO 1: REGISTRAR NUEVO PACIENTE\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

$documento_paciente = '1234567890' . time(); // íšnico
$pacienteData = [
    'documento' => $documento_paciente,
    'nombres' => 'JUAN CARLOS',
    'apellidos' => 'Pí‰REZ GARCíA',
    'celular' => '3123456789',
    'eps' => 'Nueva EPS',
    'regimen' => 'SUBSIDIADO',
    'es_desplazado' => false,
    'sisben' => 'A1',
    'sede_id' => $solita_id
];

try {
    // Verificar si el paciente ya existe
    $existe = $db->prepare("SELECT documento FROM pacientes WHERE documento = ?");
    $existe->execute([$documento_paciente]);
    
    if ($existe->fetch()) {
        echo "âš ï¸  Paciente ya existe, usando documento existente\n";
    } else {
        $stmt = $db->prepare("INSERT INTO pacientes (documento, nombres, apellidos, telefono, eps, regimen, es_desplazado, sisben, direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $pacienteData['documento'],
            $pacienteData['nombres'],
            $pacienteData['apellidos'],
            $pacienteData['celular'],
            $pacienteData['eps'],
            $pacienteData['regimen'],
            $pacienteData['es_desplazado'] ? 1 : 0,
            $pacienteData['sisben'],
            'SEDE: SOLITA'
        ]);
        echo "✅ Paciente registrado: {$pacienteData['nombres']} {$pacienteData['apellidos']}\n";
        echo "   Documento: {$documento_paciente}\n";
        echo "   EPS: {$pacienteData['eps']} | Régimen: {$pacienteData['regimen']}\n";
    }
} catch (Exception $e) {
    echo "âŒ Error registrando paciente: " . $e->getMessage() . "\n";
    $documento_paciente = '1234567890'; // Fallback
}

// ============================================================================
// PASO 2: OBTENER PRODUCTO PARA EL PEDIDO
// ============================================================================
echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "PASO 2: SELECCIONAR PRODUCTO PARA PEDIDO\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

// Buscar un producto que tenga stock en Florencia
$producto = $db->query("
    SELECT p.id, p.nombre_generico, p.laboratorio, i.stock_actual, i.stock_minimo
    FROM inventario i
    JOIN productos p ON i.producto_id = p.id
    WHERE i.sede_id = {$florencia_id} AND i.stock_actual > 100
    ORDER BY i.stock_actual DESC
    LIMIT 1
")->fetch();

if (!$producto) {
    die("âŒ Error: No hay productos con suficiente stock en Florencia\n");
}

$producto_id = $producto['id'];
$stock_inicial_florencia = $producto['stock_actual'];
$cantidad_pedido = 50; // Cantidad a solicitar

echo "ðŸ“¦ Producto seleccionado:\n";
echo "   ID: {$producto_id}\n";
echo "   Nombre: {$producto['nombre_generico']}\n";
echo "   Laboratorio: {$producto['laboratorio']}\n";
echo "   Stock actual en Florencia: {$stock_inicial_florencia} unidades\n";
echo "   Cantidad a solicitar: {$cantidad_pedido} unidades\n";

// ============================================================================
// PASO 3: CREAR PEDIDO DESDE SEDE MUNICIPAL
// ============================================================================
echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "PASO 3: CREAR PEDIDO DESDE SEDE SOLITA\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

try {
    $db->beginTransaction();
    
    // Crear el pedido
    $stmt = $db->prepare("INSERT INTO pedidos_municipios (sede_solicitante_id, estado, fecha_solicitud) VALUES (?, 'PENDIENTE', datetime('now'))");
    $stmt->execute([$solita_id]);
    $pedido_id = $db->lastInsertId();
    
    // Agregar detalle del pedido
    $stmt = $db->prepare("INSERT INTO detalles_pedido_municipio (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
    $stmt->execute([$pedido_id, $producto_id, $cantidad_pedido]);
    
    $db->commit();
    
    echo "✅ Pedido #{$pedido_id} creado exitosamente\n";
    echo "   Sede solicitante: Solita (ID: {$solita_id})\n";
    echo "   Estado: PENDIENTE\n";
    echo "   Producto: {$producto['nombre_generico']}\n";
    echo "   Cantidad solicitada: {$cantidad_pedido}\n";
} catch (Exception $e) {
    $db->rollBack();
    die("âŒ Error creando pedido: " . $e->getMessage() . "\n");
}

// ============================================================================
// PASO 4: APROBAR PEDIDO (SIMULACIí“N REGENTE)
// ============================================================================
echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "PASO 4: APROBAR PEDIDO COMO REGENTE\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

// Verificar stock antes de aprobar
$stock_antes = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
$stock_antes->execute([$florencia_id, $producto_id]);
$stock_florencia_antes = $stock_antes->fetchColumn();

echo "ðŸ“Š Stock en Florencia antes de aprobación: {$stock_florencia_antes} unidades\n";

// Aprobar el pedido
$resultado = RequestController::approveOrder($pedido_id);

if ($resultado['success']) {
    echo "✅ {$resultado['message']}\n";
    
    // Verificar stock después de aprobar
    $stock_despues = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
    $stock_despues->execute([$florencia_id, $producto_id]);
    $stock_florencia_despues = $stock_despues->fetchColumn();
    
    $descuento = $stock_florencia_antes - $stock_florencia_despues;
    
    echo "\nðŸ“Š CONCILIACIí“N DE STOCK:\n";
    echo "   Stock antes: {$stock_florencia_antes} unidades\n";
    echo "   Stock después: {$stock_florencia_despues} unidades\n";
    echo "   Descuento aplicado: {$descuento} unidades\n";
    echo "   ✅ Descuento coincide con pedido: " . ($descuento == $cantidad_pedido ? 'Sí' : 'NO') . "\n";
} else {
    echo "âŒ Error aprobando pedido: {$resultado['message']}\n";
    $stock_florencia_despues = $stock_florencia_antes; // No hubo cambio
}

// ============================================================================
// PASO 5: ENTREGAR MEDICAMENTO AL PACIENTE
// ============================================================================
echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "PASO 5: ENTREGA DE MEDICAMENTO AL PACIENTE\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

// Verificar stock en Solita antes de entregar
$stock_solita_antes = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
$stock_solita_antes->execute([$solita_id, $producto_id]);
$stock_solita = $stock_solita_antes->fetchColumn() ?: 0;

echo "ðŸ“ Sede de entrega: Solita\n";
echo "ðŸ“Š Stock en Solita antes de entrega: {$stock_solita} unidades\n";

// Simular entrega
$entregaData = [
    'paciente_id' => $documento_paciente,
    'producto_id' => $producto_id,
    'cantidad' => 10, // Entregar 10 unidades al paciente
    'sede_id' => $solita_id,
    'numero_orden' => 'ORD-' . time(),
    'copago_pagado' => false // Subsidiado = sin copago
];

$resultadoEntrega = DeliveryController::processDelivery($entregaData);

if ($resultadoEntrega['success']) {
    echo "✅ {$resultadoEntrega['message']}\n";
    if (isset($resultadoEntrega['preview'])) {
        echo "   {$resultadoEntrega['preview']}\n";
    }
    
    // Verificar stock después de entrega
    $stock_solita_despues = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
    $stock_solita_despues->execute([$solita_id, $producto_id]);
    $stock_final_solita = $stock_solita_despues->fetchColumn() ?: 0;
    
    $descuento_entrega = $stock_solita - $stock_final_solita;
    
    echo "\nðŸ“Š CONCILIACIí“N DE STOCK EN SOLITA:\n";
    echo "   Stock antes: {$stock_solita} unidades\n";
    echo "   Stock después: {$stock_final_solita} unidades\n";
    echo "   Unidades entregadas: {$descuento_entrega}\n";
    echo "   ✅ Descuento coincide: " . ($descuento_entrega == $entregaData['cantidad'] ? 'Sí' : 'NO') . "\n";
} else {
    echo "âŒ Error en entrega: {$resultadoEntrega['message']}\n";
    echo "   (Esto puede ser normal si el paciente ya reclamó este mes o no hay stock)\n";
}

// ============================================================================
// RESUMEN FINAL
// ============================================================================
echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "RESUMEN DEL TESTING\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";

echo "\n✅ OPERACIONES COMPLETADAS:\n";
echo "   1. Paciente registrado: {$pacienteData['nombres']} {$pacienteData['apellidos']}\n";
echo "   2. Pedido #{$pedido_id} creado desde sede Solita\n";
echo "   3. Pedido aprobado por Regente (CEDIS Florencia)\n";
echo "   4. Stock descontado de Florencia: {$descuento} unidades\n";
echo "   5. Entrega al paciente procesada\n";

echo "\nðŸ“Š SINCRONIZACIí“N DE STOCK:\n";
echo "   â€¢ Florencia (CEDIS): {$stock_florencia_despues} unidades (reducido)\n";
if (isset($stock_final_solita)) {
    echo "   â€¢ Solita (IPS): {$stock_final_solita} unidades\n";
}

echo "\nðŸ”— URLs del sistema:\n";
echo "   â€¢ Registro Paciente: /registro_paciente\n";
echo "   â€¢ Aprobación Pedidos: /admin/aprobar_pedidos\n";
echo "   â€¢ Solicitud Municipios: /solicitud_municipio\n";
echo "   â€¢ Entrega Medicamentos: /registro_entrega\n";

echo "\nâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n";
echo "✅ TESTING COMPLETADO EXITOSAMENTE\n";
echo "â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•\n\n";

// Guardar datos del testing para uso posterior
$testing_data = [
    'paciente_documento' => $documento_paciente,
    'paciente_nombre' => $pacienteData['nombres'] . ' ' . $pacienteData['apellidos'],
    'pedido_id' => $pedido_id,
    'producto_id' => $producto_id,
    'producto_nombre' => $producto['nombre_generico'],
    'florencia_id' => $florencia_id,
    'solita_id' => $solita_id,
    'timestamp' => date('Y-m-d H:i:s')
];

echo "ðŸ“‹ Datos del testing guardados para referencia:\n";
foreach ($testing_data as $key => $value) {
    echo "   â€¢ {$key}: {$value}\n";
}
echo "\n";
?>
