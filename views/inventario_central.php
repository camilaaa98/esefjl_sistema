<?php
session_start();
require_once '../core/Database.php';
require_once '../core/Repositories/InventoryRepository.php';
require_once '../core/Services/PharmacyService.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$inventoryRepo = new InventoryRepository($db);
$pharmacyService = new PharmacyService($inventoryRepo);

$sede_id = ($_SESSION['rol'] == 'Administrador') ? null : $_SESSION['sede_id'];
$stock = $inventoryRepo->getAllStock($sede_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Maestro - Farmacia ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-slate-50 flex overflow-hidden">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="content-area fade-in-institutional">
            <header class="mb-12">
                <h2 class="text-3xl font-black text-[#111111] tracking-tighter italic uppercase">Stock Maestro <span class="text-[#d4af37]">Ã‰lite</span></h2>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em] mt-2">Visibilidad Total de la Red Hospitalaria ESEFJL</p>
            </header>
    
            <section class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl p-8 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr class="text-slate-400 text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-5">Insumo / Medicamento</th>
                                <th class="px-6 py-5">Sede Operativa</th>
                                <th class="px-6 py-5 text-center">Stock Ã‰lite</th>
                                <th class="px-6 py-5">Vencimiento</th>
                                <th class="px-6 py-5 text-right">Identificador de Lote</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($stock as $item): ?>
                            <tr class="group hover:bg-slate-50 transition-all cursor-pointer">
                                <td class="px-6 py-6 pr-4">
                                    <span class="font-black text-[#111111] block uppercase italic leading-none mb-1"><?= $item['nombre'] ?></span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter"><?= $item['presentacion'] ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="text-[10px] font-black text-slate-500 uppercase italic border-b border-[#d4af37]/30"><?= $item['sede_nombre'] ?></span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="text-xl font-black <?= $item['stock'] < 10 ? 'text-red-600' : 'text-[#111111]' ?> tabular-nums"><?= $item['stock'] ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="font-bold text-slate-600 text-[11px] uppercase tracking-tighter"><?= date('d/M/Y', strtotime($item['fecha_vencimiento'])) ?></span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <code class="text-[10px] font-mono bg-[#111111] text-[#d4af37] px-3 py-1.5 rounded-lg border border-[#d4af37]/20 shadow-sm"><?= $item['lote'] ?></code>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
