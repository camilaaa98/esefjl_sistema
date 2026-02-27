<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/AlertController.php';
require_once __DIR__ . '/../core/Database.php';

$sede_id = $_SESSION['sede_id'];
$rol = $_SESSION['rol'];
$db = Database::getInstance();

$isRegenteOrGerente = in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente de Servicios de Salud']);
$inventory = InventoryController::getInventoryBySede($sede_id);
$all_ips_data = $isRegenteOrGerente ? InventoryController::getAllIPSInventory() : [];

// Paginación Manual para IPS Inventory
$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;
$total_ips_items = count($all_ips_data);
$ips_inventory = array_slice($all_ips_data, $offset, $limit);
$total_ips_pages = ceil($total_ips_items / $limit);

$vencidos_count = count(InventoryController::getExpiredInventory());
$can_supply_all = InventoryController::canSupplyAllIPS();
$alerts = AlertController::getInactivityAlerts();

$stockCritico = 0;
foreach($inventory as $item) {
    if($item['stock_actual'] < $item['stock_minimo']) $stockCritico++;
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisfarma Pro - Dashboard Gerencial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .alert-pulse { animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col items-center justify-center text-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase"><?php echo $rol; ?></h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">Sede de Operación Central: <?= strtoupper($_SESSION['sede']) ?></p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-tighter">Sincronización Cloud</span>
                    <?php if ($can_supply_all): ?>
                        <span class="px-3 py-1 bg-medical-500 text-white text-[10px] font-black rounded-full uppercase tracking-tighter shadow-lg shadow-medical-500/20 italic">Soberanía de Stock Garantizada ✅</span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-amber-500 text-white text-[10px] font-black rounded-full uppercase tracking-tighter italic">Alerta: Stock Central Limitado ⚡</span>
                    <?php endif; ?>
                    <a href="../core/logout.php" class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-full uppercase tracking-tighter">Desconectar</a>
                </div>
            </header>

            <!-- Stats Dynamic -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-<?= $isRegenteOrGerente ? '4' : '3' ?> gap-6">
                <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl shadow-slate-900/20">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stock de mi Sede</p>
                    <p class="text-4xl font-black tabular-nums"><?= count($inventory) ?></p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <p class="text-[9px] font-bold text-red-400 uppercase tracking-widest mb-1">Items Vencidos</p>
                    <p class="text-4xl font-black text-red-500 tabular-nums">0</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <p class="text-[9px] font-bold text-orange-400 uppercase tracking-widest mb-1">Stock Bajo Mínimo</p>
                    <p class="text-4xl font-black text-orange-500 tabular-nums"><?= $stockCritico ?></p>
                </div>
                <?php if ($isRegenteOrGerente): ?>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <p class="text-[9px] font-bold text-medical-400 uppercase tracking-widest mb-1">Municipios (IPS)</p>
                    <p class="text-4xl font-black text-medical-500 tabular-nums">5</p>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($isRegenteOrGerente && !empty($ips_inventory)): ?>
            <!-- Monitoreo Consolidado IPS (SOLO REGENTE/GERENTE) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700/50 flex flex-col md:flex-row items-center justify-between bg-gray-50/30 gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic text-xs">📡 Tablero de Control de Inventario Regional (IPS Municipios)</h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic mt-1">Página <?= $current_page_num ?> de <?= $total_ips_pages ?> — <?= $total_ips_items ?> requerimientos activos</p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?p=<?= $current_page_num - 1 ?>" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-100 dark:border-slate-600 rounded-xl text-[9px] font-black uppercase hover:bg-gray-50 transition-all shadow-sm italic">Anterior</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_ips_pages): ?>
                            <a href="?p=<?= $current_page_num + 1 ?>" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase hover:scale-105 transition-all shadow-lg italic">Siguiente</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-inter">
                        <thead class="bg-gray-50 dark:bg-slate-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16">Item</th>
                                <th class="px-6 py-4">Municipio</th>
                                <th class="px-6 py-4">Insumo</th>
                                <th class="px-6 py-4">Laboratorio</th>
                                <th class="px-6 py-4 text-center">Cant. Físico</th>
                                <th class="px-6 py-4 text-center">Faltante</th>
                                <th class="px-6 py-4 text-right">Sugerencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php 
                            $counter = $offset + 1;
                            foreach ($ips_inventory as $item): 
                                $diff = $item['stock_minimo'] - $item['stock_actual'];
                                $solicitada = ($diff > 0) ? ($item['stock_minimo'] * 1.5) : 0;
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-gray-300 font-mono"><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-slate-500 uppercase"><?= $item['sede_nombre'] ?></span>
                                </td>
                                <td class="px-6 py-4 font-bold text-[11px] text-gray-800 dark:text-white uppercase"><?= $item['nombre_generico'] ?></td>
                                <td class="px-6 py-4 text-[9px] font-black text-gray-400 italic"><?= $item['laboratorio'] ?></td>
                                <td class="px-6 py-4 text-center font-black text-sm text-slate-800 tabular-nums"><?= number_format($item['stock_actual'],0) ?></td>
                                <td class="px-6 py-4 text-center font-black text-xs <?= $diff > 0 ? 'text-red-500 anim-pulse' : 'text-green-500' ?>">
                                    <?= $diff > 0 ? number_format($diff,0) : '0' ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($diff > 0): ?>
                                        <span class="inline-block px-3 py-1 bg-slate-900 text-white text-[10px] font-black rounded-lg uppercase tracking-tighter italic shadow-md">Despachar: <?= number_format(round($solicitada),0) ?></span>
                                    <?php else: ?>
                                        <span class="text-[9px] text-gray-300 font-bold uppercase italic">Abastecido</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección de Requerimientos al Proveedor (Regente/Gerente) -->
            <div class="bg-indigo-900 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-900/30 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl">
                    <h3 class="text-xs font-black text-indigo-300 uppercase tracking-[0.2em] mb-3 italic">Estrategia de Adquisición CEDIS</h3>
                    <h2 class="text-3xl font-black tracking-tight leading-none mb-4 italic uppercase">Sugerencias de Pedido a Proveedores</h2>
                    <p class="text-indigo-200 text-sm italic font-medium">Análisis de reposición estratégica para el Almacén Central de Florencia. El stock del Regente debe ser superior al de las 5 IPS para garantizar soberanía sanitaria regional.</p>
                </div>
                <div class="bg-white/10 backdrop-blur-xl p-6 rounded-3xl border border-white/20 w-full md:w-auto">
                    <p class="text-[10px] font-black text-indigo-200 uppercase mb-2">Orden de Compra Proyectada</p>
                    <div class="flex items-end gap-2">
                        <span class="text-5xl font-black italic tracking-tighter">10</span>
                        <span class="text-xs font-bold text-indigo-300 mb-2 italic">Proveedores Calificados</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($rol == 'IPS (Municipio)'): ?>
            <!-- Sección Exclusiva para IPS: Solicitudes de Pacientes -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700/50 flex flex-col md:flex-row items-center justify-between bg-medical-50/30 gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic text-xs">📋 Pacientes en Espera de Suministro (IPS <?= $_SESSION['sede'] ?>)</h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic mt-1">Gestión local de entrega de medicamentos a usuarios</p>
                    </div>
                    <button class="px-6 py-2 bg-medical-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-medical-500/20">Registrar Entrega a Paciente</button>
                </div>
                <div class="p-12 text-center">
                    <div class="text-4xl mb-4">🩺</div>
                    <p class="text-gray-400 text-xs italic font-medium">No hay pacientes con fórmulas pendientes en este municipio hoy.</p>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
