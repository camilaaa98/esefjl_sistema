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

// ROLES DIRECTIVOS
$isDirectivo = in_array($rol, [
    'Gerente',
    'Regente Farmacia',
    'Subgerente de Servicios de Salud',
    'Subgerente Administrativa y Financiera',
    'Administrador'
]);

$inventoryCtrl = new InventoryController();
$inventory = $inventoryCtrl->getInventoryBySede($sede_id);
$all_ips_data = $isDirectivo ? $inventoryCtrl->getAllIPSInventory() : [];
$vencidos_count = count($inventoryCtrl->getExpiredInventory());

$stockCritico = 0;
foreach($inventory as $item) {
    if($item['stock_actual'] < $item['stock_minimo']) $stockCritico++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content-area">
            <!-- Header Institucional ESEFJL 7.0 -->
            <header class="flex flex-col lg:flex-row items-center justify-between mb-12 fade-in-institutional">
                <div class="flex items-center gap-6 text-center lg:text-left">
                    <img src="../img/logoesefjl.jpg" alt="Logo ESEFJL" class="w-16 h-16 rounded-2xl shadow-lg border border-slate-100">
                    <div>
                        <h2 class="text-3xl font-black text-[#111111] tracking-tight uppercase">Panel de Gestión Administrativa</h2>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.3em] flex items-center justify-center lg:justify-start gap-2">
                             SEDE CENTRAL: FLORENCIA <span class="text-slate-200">|</span> SISFARMA ÉLITE v7.5 PREMIUM
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 lg:mt-0 flex gap-4">
                    <div class="bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div class="w-3 h-3 bg-[#d4af37] rounded-full animate-pulse shadow-[0_0_10px_rgba(212,175,55,0.6)]"></div>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sincronización Regional Activa</span>
                    </div>
                </div>
            </header>

            <!-- Resumen Ejecutivo de Alta Gerencia -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12 fade-in-institutional" style="animation-delay: 0.1s">
                <div class="lg:col-span-2 card-clinical flex flex-col md:flex-row items-center gap-10 bg-gradient-to-br from-white to-slate-50 border-l-4 border-l-[#d4af37]">
                    <div class="flex-1">
                        <h3 class="text-xs font-black text-[#111111] uppercase tracking-[0.4rem] mb-3">Intelligence Report</h3>
                        <h2 class="text-2xl font-bold text-slate-800 leading-tight mb-4">Estado General del Suministro <span class="text-[#d4af37]">Farmacéutico</span></h2>
                        <p class="text-slate-500 text-sm leading-relaxed font-medium">
                            El sistema reporta un cumplimiento del <span class="font-bold text-[#111111]">98.4%</span> en la trazabilidad de insumos. Actualmente se monitorean los niveles de stock en la sede Florencia y la red de IPS territoriales subordinadas.
                        </p>
                    </div>
                    <div class="w-full md:w-auto p-6 bg-white rounded-3xl shadow-soft border border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Índice de Eficiencia</p>
                        <p class="text-5xl font-black text-[#111111] italic tracking-tighter">98.4</p>
                        <p class="text-[9px] font-bold text-[#d4af37] uppercase mt-2">Nivel de Excelencia</p>
                    </div>
                </div>

                <div class="card-clinical bg-[#111111] text-white flex flex-col justify-center relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    <h3 class="text-[10px] font-black text-[#d4af37] uppercase tracking-widest mb-4">Métricas Globales</h3>
                    <div class="flex items-center gap-4 mb-2">
                        <span class="text-5xl font-black italic tracking-tighter text-[#d4af37]">05</span>
                        <div class="leading-none">
                            <p class="text-lg font-bold">IPS ACTIVAS</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Monitoreo Regional</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-12 fade-in-institutional" style="animation-delay: 0.2s">
                <!-- Card: Stock -->
                <div class="card-clinical">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-slate-50 rounded-2xl text-2xl">📦</div>
                        <span class="status-pill bg-slate-900 text-white text-[9px] font-black py-1 px-3 rounded-full border border-slate-800 uppercase tracking-widest">Disponible</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Stock Maestro Sede</p>
                    <p class="text-5xl font-black text-slate-800 tabular-nums tracking-tighter"><?= count($inventory) ?></p>
                    <div class="w-full bg-slate-100 h-2 mt-8 rounded-full overflow-hidden">
                        <div class="bg-[#d4af37] h-full w-[85%] rounded-full shadow-lg shadow-[#d4af37]/20"></div>
                    </div>
                </div>

                <!-- Card: Expired -->
                <div class="card-clinical">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-red-50 rounded-2xl text-2xl">⌛</div>
                        <span class="status-pill <?= $vencidos_count > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' ?> text-[9px] font-black py-1 px-3 rounded-full border border-red-100 uppercase tracking-widest">Alertas</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Farmacovigilancia</p>
                    <p class="text-5xl font-black <?= $vencidos_count > 0 ? 'text-red-600' : 'text-slate-800' ?> tabular-nums tracking-tighter"><?= $vencidos_count ?></p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-8 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full <?= $vencidos_count > 0 ? 'bg-red-500 animate-ping' : 'bg-slate-300' ?>"></span>
                        Productos en fecha de riesgo
                    </p>
                </div>

                <!-- Card: Deficit -->
                <div class="card-clinical">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-slate-900 rounded-2xl text-2xl">⚠️</div>
                        <span class="status-pill bg-amber-50 text-amber-700 text-[9px] font-black py-1 px-3 rounded-full border border-amber-100 uppercase tracking-widest">Suministro</span>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Déficit Crítico</p>
                    <p class="text-5xl font-black text-slate-800 tabular-nums tracking-tighter"><?= $stockCritico ?></p>
                    <div class="flex gap-1.5 mt-8">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="h-2 flex-1 <?= ($i < $stockCritico) ? 'bg-[#d4af37]' : 'bg-slate-100' ?> rounded-full"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Regional Table -->
            <?php if ($isDirectivo): ?>
            <section class="fade-in-institutional" style="animation-delay: 0.3s">
                <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight italic uppercase">Monitoreo Regional de Insumos</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sedes IPS Municipales — Red ESEFJL</p>
                    </div>
                    <button class="btn-institutional">Descargar Reporte Ejecutivo</button>
                </div>

                <div class="table-clinical-wrapper">
                    <div class="overflow-x-auto">
                        <table class="table-clinical">
                            <thead>
                                <tr>
                                    <th>Municipio / IPS</th>
                                    <th>Insumo Médico / Farmacológico</th>
                                    <th>Laboratorio</th>
                                    <th class="text-right">Stock Act.</th>
                                    <th class="text-center">Estado Crítico</th>
                                    <th class="text-right">Acción Gerencial</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($all_ips_data as $item): 
                                    $diff = $item['stock_minimo'] - $item['stock_actual'];
                                ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-5">
                                        <span class="text-[11px] font-black text-[#111111] uppercase italic border-b border-[#d4af37]"><?= $item['sede_nombre'] ?></span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-bold text-slate-700 uppercase italic leading-none mb-1"><?= $item['nombre_generico'] ?></p>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?= $item['fecha_vencimiento'] ?></span>
                                    </td>
                                    <td class="px-6 py-5 text-[10px] font-bold text-slate-500 uppercase italic"><?= $item['laboratorio'] ?></td>
                                    <td class="px-6 py-5 text-right tabular-nums">
                                        <span class="text-lg font-black text-slate-800"><?= $item['stock_actual'] ?></span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <?php if ($diff > 0): ?>
                                            <span class="px-3 py-1 bg-red-50 text-red-600 text-[9px] font-black uppercase rounded-full border border-red-100">Déficit: <?= $diff ?></span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black uppercase rounded-full border border-slate-800">Suficiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <button class="px-4 py-2 border-2 border-[#111111] text-[#111111] text-[9px] font-black uppercase tracking-widest hover:bg-[#111111] hover:text-white transition-all rounded-xl italic">Despacho Central</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <footer class="mt-20 pt-8 border-t border-slate-100 flex justify-between items-center text-[9px] font-bold text-slate-300 uppercase tracking-[0.4em] italic pb-12">
                <div>E.S.E. FABIO JARAMILLO LONDOÑO — "Revive la Salud ¡Luchando de Corazón!"</div>
                <div>Caquetá, Colombia</div>
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
