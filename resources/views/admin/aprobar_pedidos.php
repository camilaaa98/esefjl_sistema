<?php
/**
 * Vista de Aprobación de Pedidos - Regente de Farmacia
 * Permite aprobar/rechazar pedidos de municipios y gestionar el stock del CEDIS
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

// Verificar que sea regente o administrador
$rol = $_SESSION['rol'] ?? '';
if (!in_array($rol, ['Regente Farmacia', 'Administrador', 'Gerente'])) {
    header('Location: inicio');
    exit();
}

require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/Controllers/RequestController.php';
require_once __DIR__ . '/../../../app/Controllers/InventoryController.php';

$db = Database::getInstance();
$florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();

$mensaje = '';
$error = '';

// Procesar aprobación/rechazo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aprobar_pedido'])) {
        $pedido_id = $_POST['pedido_id'];
        $result = RequestController::approveOrder($pedido_id);
        if ($result['success']) {
            $mensaje = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
    
    if (isset($_POST['rechazar_pedido'])) {
        $pedido_id = $_POST['pedido_id'];
        $motivo = $_POST['motivo_rechazo'] ?? 'Sin motivo especificado';
        try {
            $db->prepare("UPDATE pedidos_municipios SET estado = 'RECHAZADO', observaciones = ? WHERE id = ?")
               ->execute([$motivo, $pedido_id]);
            $mensaje = "Pedido #{$pedido_id} rechazado: {$motivo}";
        } catch (Exception $e) {
            $error = "Error al rechazar: " . $e->getMessage();
        }
    }
}

// Obtener pedidos pendientes
$pedidosPendientes = $db->query("
    SELECT pm.id, pm.fecha_solicitud, pm.estado, s.nombre as sede_solicitante,
           COUNT(dpm.id) as total_items, SUM(dpm.cantidad) as total_unidades
    FROM pedidos_municipios pm
    JOIN sedes s ON pm.sede_solicitante_id = s.id
    LEFT JOIN detalles_pedido_municipio dpm ON pm.id = dpm.pedido_id
    WHERE pm.estado = 'PENDIENTE'
    GROUP BY pm.id
    ORDER BY pm.fecha_solicitud DESC
")->fetchAll();

// Obtener pedidos recientes (aprobados/rechazados)
$pedidosRecientes = $db->query("
    SELECT pm.id, pm.fecha_solicitud, pm.estado, pm.observaciones, s.nombre as sede_solicitante,
           COUNT(dpm.id) as total_items, SUM(dpm.cantidad) as total_unidades
    FROM pedidos_municipios pm
    JOIN sedes s ON pm.sede_solicitante_id = s.id
    LEFT JOIN detalles_pedido_municipio dpm ON pm.id = dpm.pedido_id
    WHERE pm.estado IN ('DESPACHADO', 'RECHAZADO')
    GROUP BY pm.id
    ORDER BY pm.fecha_solicitud DESC
    LIMIT 10
")->fetchAll();

// Stock del CEDIS (Florencia)
$stockCedis = $db->query("
    SELECT i.id, p.nombre_generico, p.laboratorio, i.stock_actual, i.stock_minimo, i.lote, i.fecha_vencimiento
    FROM inventario i
    JOIN productos p ON i.producto_id = p.id
    WHERE i.sede_id = {$florencia_id}
    ORDER BY i.stock_actual ASC
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobación de Pedidos - Regente | ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
    <style>
        .pedido-card { transition: all 0.3s ease; }
        .pedido-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../../../resources/views/partials/sidebar.php'; ?>
        
        <main class="content-area ">
            <!-- Header -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-black text-[#111111] uppercase tracking-tight">Aprobación de Pedidos</h1>
                    <p class="text-[#d4af37] text-xs font-bold uppercase tracking-widest mt-1">Regente de Farmacia — CEDIS Florencia</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200">
                    <span class="text-xs text-slate-400 uppercase font-bold">Stock CEDIS:</span>
                    <span class="text-lg font-black text-[#111111] ml-2"><?= number_format($db->query("SELECT SUM(stock_actual) FROM inventario WHERE sede_id = {$florencia_id}")->fetchColumn()) ?></span>
                    <span class="text-xs text-slate-400 uppercase font-bold ml-1">unidades</span>
                </div>
            </header>
            
            <!-- Mensajes -->
            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-2xl flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-green-800 font-bold text-sm"><?= htmlspecialchars($mensaje) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-2xl flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-red-800 font-bold text-sm"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Grid Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Pedidos Pendientes -->
                <div class="lg:col-span-2 space-y-6">
                    <h2 class="text-lg font-black text-[#111111] uppercase tracking-tight flex items-center gap-2">
                        <span class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></span>
                        Pedidos Pendientes (<?= count($pedidosPendientes) ?>)
                    </h2>
                    
                    <?php if (empty($pedidosPendientes)): ?>
                        <div class="bg-white rounded-2xl p-8 text-center border-2 border-slate-100">
                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-slate-500 font-bold">No hay pedidos pendientes de aprobación</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php foreach ($pedidosPendientes as $pedido): 
                        // Obtener detalles del pedido
                        $detalles = $db->prepare("
                            SELECT dpm.*, p.nombre_generico, p.laboratorio,
                                   (SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = dpm.producto_id) as stock_cedis
                            FROM detalles_pedido_municipio dpm
                            JOIN productos p ON dpm.producto_id = p.id
                            WHERE dpm.pedido_id = ?
                        ");
                        $detalles->execute([$florencia_id, $pedido['id']]);
                        $items = $detalles->fetchAll();
                        
                        $puedeAprobar = true;
                        foreach ($items as $item) {
                            if (($item['stock_cedis'] ?? 0) < $item['cantidad']) {
                                $puedeAprobar = false;
                                break;
                            }
                        }
                    ?>
                        <div class="pedido-card bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                            <!-- Header del Pedido -->
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                                <div>
                                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Pedido #<?= $pedido['id'] ?></span>
                                    <h3 class="text-lg font-black text-[#111111]"><?= htmlspecialchars($pedido['sede_solicitante']) ?></h3>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-black uppercase rounded-full">PENDIENTE</span>
                                    <p class="text-xs text-slate-400 mt-1"><?= date('d/m/Y H:i', strtotime($pedido['fecha_solicitud'])) ?></p>
                                </div>
                            </div>
                            
                            <!-- Detalles -->
                            <div class="p-6">
                                <table class="w-full text-sm mb-4">
                                    <thead class="text-xs font-black text-slate-400 uppercase tracking-wider">
                                        <tr>
                                            <th class="text-left py-2">Producto</th>
                                            <th class="text-center py-2">Solicitado</th>
                                            <th class="text-center py-2">Stock CEDIS</th>
                                            <th class="text-center py-2">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($items as $item): 
                                            $suficiente = ($item['stock_cedis'] ?? 0) >= $item['cantidad'];
                                        ?>
                                            <tr>
                                                <td class="py-3">
                                                    <p class="font-bold text-[#111111]"><?= htmlspecialchars($item['nombre_generico']) ?></p>
                                                    <p class="text-xs text-slate-400"><?= htmlspecialchars($item['laboratorio']) ?></p>
                                                </td>
                                                <td class="text-center py-3 font-black text-slate-800"><?= $item['cantidad'] ?></td>
                                                <td class="text-center py-3 <?= $suficiente ? 'text-green-600' : 'text-red-600' ?> font-bold">
                                                    <?= $item['stock_cedis'] ?? 0 ?>
                                                </td>
                                                <td class="text-center py-3">
                                                    <?php if ($suficiente): ?>
                                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-black rounded">DISPONIBLE</span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-black rounded">INSUFICIENTE</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                                <!-- Acciones -->
                                <div class="flex gap-3 pt-4 border-t border-slate-100">
                                    <?php if ($puedeAprobar): ?>
                                        <form method="POST" class="flex-1">
                                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                            <button type="submit" name="aprobar_pedido" 
                                                    class="w-full py-3 bg-green-600 text-white font-black rounded-xl hover:bg-green-700 transition-all uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Aprobar y Despachar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button disabled class="w-full py-3 bg-slate-200 text-slate-400 font-black rounded-xl cursor-not-allowed uppercase text-xs tracking-widest">
                                            Stock Insuficiente en CEDIS
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="button" onclick="mostrarRechazo(<?= $pedido['id'] ?>)" 
                                            class="py-3 px-6 bg-red-50 text-red-600 font-black rounded-xl hover:bg-red-100 transition-all uppercase text-xs tracking-widest">
                                        Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulario de Rechazo (oculto) -->
                        <div id="rechazo-<?= $pedido['id'] ?>" class="hidden bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                            <form method="POST">
                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                <label class="block text-sm font-bold text-red-800 mb-2">Motivo del Rechazo:</label>
                                <textarea name="motivo_rechazo" required class="w-full p-3 border border-red-200 rounded-xl mb-3 text-sm" rows="2" placeholder="Ej: Stock insuficiente, pedido duplicado, etc."></textarea>
                                <div class="flex gap-2">
                                    <button type="submit" name="rechazar_pedido" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg text-sm">Confirmar Rechazo</button>
                                    <button type="button" onclick="ocultarRechazo(<?= $pedido['id'] ?>)" class="px-4 py-2 bg-white text-slate-600 font-bold rounded-lg text-sm">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Pedidos Recientes -->
                    <?php if (!empty($pedidosRecientes)): ?>
                        <h2 class="text-lg font-black text-[#111111] uppercase tracking-tight mt-8 mb-4">Historial Reciente</h2>
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 text-xs font-black text-slate-400 uppercase tracking-wider">
                                    <tr>
                                        <th class="text-left px-6 py-3">Pedido</th>
                                        <th class="text-center px-6 py-3">Sede</th>
                                        <th class="text-center px-6 py-3">Estado</th>
                                        <th class="text-center px-6 py-3">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($pedidosRecientes as $p): ?>
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-6 py-3 font-bold text-[#111111]">#<?= $p['id'] ?></td>
                                            <td class="px-6 py-3 text-center"><?= htmlspecialchars($p['sede_solicitante']) ?></td>
                                            <td class="px-6 py-3 text-center">
                                                <?php if ($p['estado'] === 'DESPACHADO'): ?>
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-black rounded">DESPACHADO</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-black rounded">RECHAZADO</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-3 text-center text-slate-400 text-xs"><?= date('d/m/Y H:i', strtotime($p['fecha_solicitud'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Stock CEDIS -->
                <div class="space-y-6">
                    <h2 class="text-lg font-black text-[#111111] uppercase tracking-tight">Stock CEDIS Florencia</h2>
                    
                    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-4">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-400 uppercase">Stock Crítico</span>
                            <?php
                            $criticos = $db->query("
                                SELECT COUNT(*) FROM inventario 
                                WHERE sede_id = {$florencia_id} AND stock_actual < stock_minimo
                            ")->fetchColumn();
                            ?>
                            <span class="px-2 py-1 <?= $criticos > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?> text-xs font-black rounded">
                                <?= $criticos ?> items
                            </span>
                        </div>
                        
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            <?php 
                            $bajoStock = $db->query("
                                SELECT i.stock_actual, i.stock_minimo, p.nombre_generico, p.laboratorio
                                FROM inventario i
                                JOIN productos p ON i.producto_id = p.id
                                WHERE i.sede_id = {$florencia_id}
                                ORDER BY (i.stock_actual - i.stock_minimo) ASC
                                LIMIT 15
                            ")->fetchAll();
                            
                            foreach ($bajoStock as $item): 
                                $esCritico = $item['stock_actual'] < $item['stock_minimo'];
                                $porcentaje = min(100, ($item['stock_actual'] / max(1, $item['stock_minimo'])) * 100);
                            ?>
                                <div class="p-3 rounded-xl <?= $esCritico ? 'bg-red-50 border border-red-100' : 'bg-slate-50' ?>">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-xs font-bold text-[#111111] truncate pr-2"><?= htmlspecialchars($item['nombre_generico']) ?></p>
                                        <span class="text-xs font-black <?= $esCritico ? 'text-red-600' : 'text-green-600' ?>">
                                            <?= $item['stock_actual'] ?>
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-2"><?= htmlspecialchars($item['laboratorio']) ?> • Mín: <?= $item['stock_minimo'] ?></p>
                                    <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full <?= $esCritico ? 'bg-red-500' : 'bg-green-500' ?> rounded-full" style="width: <?= $porcentaje ?>"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function mostrarRechazo(id) {
            document.getElementById('rechazo-' + id).classList.remove('hidden');
        }
        
        function ocultarRechazo(id) {
            document.getElementById('rechazo-' + id).classList.add('hidden');
        }
    </script>
</body>
</html>

