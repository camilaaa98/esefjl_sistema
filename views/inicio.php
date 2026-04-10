<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Controllers/InventoryController.php';
require_once __DIR__ . '/../core/ViewHelper.php';
require_once __DIR__ . '/../core/Database.php';

$sede_id = (int)$_SESSION['sede_id'];
$rol = $_SESSION['rol'] ?? '';
$db = Database::getInstance();

// ROLES DIRECTIVOS - solo ellos ven el monitoreo global e IPS
$isRegenteOrGerente = in_array($rol, [
    'Gerente',
    'Regente Farmacia',
    'Subgerente de Servicios de Salud',
    'Subgerente Administrativa y Financiera',
    'Administrador'
]);
// Usuario de sede IPS municipal
$isIPS = ($rol === 'IPS (Municipio)');
$inventoryCtrl = new InventoryController();
$inventory = $inventoryCtrl->getInventoryBySede($sede_id);
$all_ips_data = $isRegenteOrGerente ? $inventoryCtrl->getAllIPSInventory() : [];

// Paginación Manual para IPS Inventory
$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;
$total_ips_items = count($all_ips_data);
$ips_inventory = array_slice($all_ips_data, $offset, $limit);
$total_ips_pages = ceil($total_ips_items / $limit);

$vencidos_count = count($inventoryCtrl->getExpiredInventory());
$can_supply_all = $inventoryCtrl->canSupplyAllIPS();
$alerts = []; // AlertController::getInactivityAlerts();

$stockCritico = 0;
foreach($inventory as $item) {
    if($item['stock_actual'] < $item['stock_minimo']) $stockCritico++;
}
?>
<!DOCTYPE html>
    <title>Farmacia ESEFJL - Inicio Operacional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-base min-h-screen font-inter">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content-area">
            <header class="flex flex-col md:flex-row items-center justify-between mb-12 animate-fade-up">
                <div>
                    <h2 class="text-4xl text-gradient font-black uppercase italic tracking-tighter">Administrador</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4rem] mt-1">Sede Central: FLORENCIA (PRINCIPAL)</p>
                </div>
                <div class="flex gap-4 mt-6 md:mt-0">
                    <span class="status-pill pill-green flex items-center gap-2 shadow-sm">
                         <span class="w-2 h-2 bg-status-green rounded-full animate-pulse"></span>
                         Sincronización Cloud ✅
                    </span>
                </div>
            </header>

            <!-- Elite 4.0 Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-<?= $isRegenteOrGerente ? '4' : '3' ?> gap-8 mb-12">
                <div class="card-elite animate-fade-up" style="animation-delay: 0.1s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-emerald-50 rounded-2xl text-2xl">📦</div>
                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full">Global</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stock de mi Sede</p>
                    <p class="text-5xl font-black text-primary-color tabular-nums tracking-tighter"><?= count($inventory) ?></p>
                    <div class="w-full bg-slate-100 h-1.5 mt-8 rounded-full overflow-hidden">
                        <div class="bg-primary-main h-full w-[85%] transition-all duration-1000"></div>
                    </div>
                </div>

                <div class="card-elite animate-fade-up" style="animation-delay: 0.2s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-red-50 rounded-2xl text-2xl">⌛</div>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-red-50 px-3 py-1 rounded-full">Vencidos</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Alertas Sanitarias</p>
                    <p class="text-5xl font-black text-status-red tabular-nums tracking-tighter"><?= $vencidos_count ?></p>
                    <p class="text-[9px] text-red-400 font-bold mt-6 uppercase italic flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></span>
                        Acción Requerida
                    </p>
                </div>

                <div class="card-elite animate-fade-up" style="animation-delay: 0.3s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-orange-50 rounded-2xl text-2xl">⚠️</div>
                        <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest bg-orange-50 px-3 py-1 rounded-full">Déficit</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stock Bajo Mínimo</p>
                    <p class="text-5xl font-black text-status-orange tabular-nums tracking-tighter"><?= $stockCritico ?></p>
                    <div class="flex gap-1.5 mt-8">
                        <div class="h-1.5 flex-1 bg-status-orange rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-slate-100 rounded-full"></div>
                        <div class="h-1.5 flex-1 bg-slate-100 rounded-full"></div>
                    </div>
                </div>

                <?php if ($isRegenteOrGerente): ?>
                <div class="card-elite animate-fade-up" style="animation-delay: 0.4s">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-indigo-50 rounded-2xl text-2xl">📡</div>
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-3 py-1 rounded-full">Red Regional</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Municipios (IPS)</p>
                    <p class="text-5xl font-black text-indigo-600 tabular-nums tracking-tighter">5</p>
                    <p class="text-[9px] text-indigo-400 font-bold mt-6 uppercase italic">Sincronización Regional</p>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($isRegenteOrGerente && !empty($ips_inventory)): ?>
            <!-- Monitoreo Regional Elite -->
            <div class="table-elite-wrapper animate-fade-up shadow-premium" style="animation-delay: 0.6s">
                <div class="px-10 py-8 bg-slate-900 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-white text-xl font-black italic tracking-tighter uppercase leading-none mb-1">📡 Gestión Regional de Suministros</h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-[0.3em]">Tablero Consolidado (IPS Municipios) — Página <?= $current_page_num ?> de <?= $total_ips_pages ?></p>
                    </div>
                    <div class="flex gap-4">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?p=<?= $current_page_num - 1 ?>" class="px-6 py-2.5 bg-white/5 text-white border border-white/10 rounded-2xl text-[10px] font-black uppercase transition-all hover:bg-white/10 flex items-center gap-2 italic">◀ ANTERIOR</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_ips_pages): ?>
                            <a href="?p=<?= $current_page_num + 1 ?>" class="px-6 py-2.5 bg-primary-main text-white rounded-2xl text-[10px] font-black uppercase transition-all shadow-premium hover:scale-105 flex items-center gap-2 italic">SIGUIENTE ▶</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-elite">
                        <thead>
                            <tr>
                                <th class="w-20">Item</th>
                                <th>Municipio</th>
                                <th>Insumo Médico / Farmacológico</th>
                                <th>Laboratorio</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Estado Crítico</th>
                                <th class="text-right">Acción Sugerida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = $offset + 1;
                            foreach ($ips_inventory as $item): 
                                $diff = $item['stock_minimo'] - $item['stock_actual'];
                                $solicitada = ($diff > 0) ? ($item['stock_minimo'] * 1.5) : 0;
                            ?>
                            <tr class="ips-row opacity-0 group">
                                <td class="font-mono text-[10px] text-gray-300 font-black px-8"><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></td>
                                <td class="font-black text-primary-main uppercase text-[11px] tracking-tighter italic"><?= $item['sede_nombre'] ?></td>
                                <td class="font-black text-[12px] text-slate-800 uppercase italic leading-tight"><?= $item['nombre_generico'] ?></td>
                                <td class="text-[10px] font-bold text-gray-400 italic"><?= $item['laboratorio'] ?></td>
                                <td class="text-center font-black text-lg text-slate-900 tabular-nums"><?= ViewHelper::formatNumber($item['stock_actual']) ?></td>
                                <td class="text-center">
                                    <?php if ($diff > 0): ?>
                                        <span class="status-pill pill-red shadow-sm animate-pulse">Déficit: <?= ViewHelper::formatNumber($diff) ?></span>
                                    <?php else: ?>
                                        <span class="status-pill pill-green shadow-sm">Abastecido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right px-8">
                                    <?php if ($diff > 0): ?>
                                        <button class="btn-elite btn-primary-elite hover-lift active-shrink btn-primary-elite italic">Despachar: <?= ViewHelper::formatNumber(round($solicitada)) ?></button>
                                    <?php else: ?>
                                        <span class="text-[10px] text-gray-300 font-black uppercase italic tracking-widest">En Rango</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Strategic Section Elite -->
            <div class="mt-12 bg-primary-color rounded-[3rem] p-12 text-white shadow-premium flex flex-col md:flex-row items-center justify-between gap-12 relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                <div class="max-w-2xl relative z-10">
                    <h3 class="text-xs font-black text-emerald-300 uppercase tracking-[0.4rem] mb-4 italic">Estrategia de Abastecimiento Soberano</h3>
                    <h2 class="text-4xl font-black tracking-tight leading-tight mb-6 italic uppercase">Reposición Inteligente para Almacén Central</h2>
                    <p class="text-emerald-50 text-sm font-medium italic opacity-80 leading-relaxed">Farmacia ESEFJL analiza los niveles de cada sede y prioriza el suministro regional. El Regente garantiza la soberanía farmacéutica para toda la Red Hospitalaria de la E.S.E. Fabio Jaramillo Londoño.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-3xl p-8 rounded-[2rem] border border-white/20 w-full md:w-auto text-center relative z-10 shadow-2xl">
                    <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-4">Orden de Compra CEDIS</p>
                    <div class="flex items-center justify-center gap-4">
                        <span class="text-7xl font-black italic tracking-tighter">10</span>
                        <div class="text-left leading-none">
                            <p class="text-xl font-black italic text-emerald-100">PROVEEDORES</p>
                            <p class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest">Activos en Red</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
</body>
</html>
