<?php

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
require_once __DIR__ . '/../../../app/config/Database.php';
require_once __DIR__ . '/../../../app/Controllers/InventoryController.php';
require_once __DIR__ . '/../../../app/Controllers/RequestController.php';

$mensaje_res = "";
$sede_id = $_SESSION['sede_id'];
$db = Database::getInstance();

// Procesar Pedidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnManualRequest'])) {
        $res = RequestController::createAutomaticOrder($sede_id);
        $mensaje_res = $res['message'];
    }
    
    if (isset($_POST['btnSolicitudManual'])) {
        $prod_id = $_POST['producto_id'];
        $cant = $_POST['cantidad'];
        $res = RequestController::createManualOrder($sede_id, $prod_id, $cant);
        $mensaje_res = $res['message'];
    }
}

$inventory = InventoryController::getInstance()->getInventoryBySede($sede_id);
$productos_todos = $db->query("SELECT * FROM productos ORDER BY nombre_generico ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística IPS - FARMACIA ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <script src="<?= BASE_URL ?>/public/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="content-area  fade-in-institutional">
            <?php if ($mensaje_res): ?>
                <div class="mb-8 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 p-4 rounded-2xl shadow-xl font-bold text-center animate-bounce uppercase text-[10px] tracking-widest">
                    ✅ <?= $mensaje_res ?>
                </div>
            <?php endif; ?>

            <header class="flex flex-col items-center justify-center text-center gap-3 relative overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 text-white p-8 rounded-2xl mb-8 shadow-xl">
                <div class="relative z-10">
                    <span class="inline-block px-4 py-1.5 bg-[#d4af37] text-slate-900 text-xs font-bold rounded-full uppercase tracking-wider mb-3">Operación Municipal Central</span>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight mb-2 uppercase">Logística de <span class="text-[#d4af37]">Suministro IPS</span></h2>
                    <p class="text-slate-300 text-sm font-medium mb-6">Sede: <span class="text-[#d4af37] font-bold"><?= strtoupper($_SESSION['sede'] ?? 'SEDE NO DEFINIDA') ?></span></p>
                    
                    <form method="POST">
                        <button type="submit" name="btnManualRequest" class="group flex items-center gap-3 px-6 py-3 bg-white text-slate-900 font-bold rounded-xl shadow-lg transition-all hover:bg-[#d4af37] hover:scale-105 active:scale-95 uppercase text-xs tracking-wider">
                            🚪€ Abastecimiento Automático al CEDIS
                            <span class="group-hover:translate-x-1 transition-transform">â†’</span>
                        </button>
                    </form>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabla de Stock Local -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Stock Operativo Local</h3>
                        <span class="text-xs text-slate-500 font-medium">Sincronizado: <?= date('H:i') ?></span>
                    </div>
                    <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left">Insumo / Laboratorio</th>
                                    <th class="px-4 py-3 text-center">Stock</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($inventory as $i): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-semibold text-slate-800 uppercase"><?= strtoupper($i['nombre_generico']) ?></p>
                                        <p class="text-xs text-slate-500"><?= $i['laboratorio'] ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-base font-bold text-slate-800 tabular-nums"><?= $i['stock_actual'] ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <?= InventoryController::getStockBadge($i) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario de Solicitud -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200">
                    <div class="mb-6 border-l-4 border-l-[#d4af37] pl-4">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Solicitud Manual</h3>
                        <p class="text-slate-500 text-xs mt-1">Requerimientos extraordinarios</p>
                    </div>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Insumo del Catálogo</label>
                            <select name="producto_id" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm text-slate-800 cursor-pointer" required>
                                <option value="">Seleccione un producto...</option>
                                <?php foreach ($productos_todos as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= strtoupper($p['nombre_generico']) ?> (<?= $p['laboratorio'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Cantidad</label>
                            <input type="number" name="cantidad" min="1" placeholder="Ej: 50" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm text-slate-800 placeholder:text-slate-400" required>
                        </div>

                        <button type="submit" name="btnSolicitudManual" class="w-full py-3 bg-slate-800 text-white font-semibold rounded-lg shadow-md transition-all hover:bg-slate-700 hover:scale-[1.02] active:scale-95 uppercase text-xs tracking-wider">
                            📦 Enviar Solicitud al CEDIS
                        </button>
                    </form>

                    <div class="mt-6 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-700 font-medium">
                            <strong>Nota:</strong> Las solicitudes ingresan a auditoría del CEDIS. Tiempo estimado: 24-72h hábiles.
                        </p>
                    </div>
                </div>
            </div>
            
            <footer class="mt-20 pt-8 border-t border-slate-100 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center pb-12 italic">
                CONTROL DE LOGíSTICA MUNICIPAL — SISFARMA Central v9.5
            </footer>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
    <script src="<?= BASE_URL ?>/public/js/theme-toggle.js"></script>
    <script src="<?= BASE_URL ?>/public/js/animations.js" defer></script>
</body>
</html>

