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
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="flex-1 p-8 overflow-y-auto animate-fade-in">
        <header class="mb-10">
            <h1 class="text-4xl font-black text-slate-800 tracking-tighter italic">Stock Maestro <span class="text-primary-main">FJL</span></h1>
            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mt-2">Visibilidad Total de la Red Hospitalaria</p>
        </header>

        <section class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white shadow-2xl p-8">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-[11px] uppercase tracking-widest font-black border-b border-slate-100">
                        <th class="pb-6">Medicamento</th>
                        <th class="pb-6">Sede</th>
                        <th class="pb-6 text-center">Stock</th>
                        <th class="pb-6">Vencimiento</th>
                        <th class="pb-6">Lote</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($stock as $item): ?>
                    <tr class="group hover:bg-slate-50/50 transition-all cursor-pointer">
                        <td class="py-6 pr-4">
                            <span class="font-black text-slate-700 block"><?= $item['nombre'] ?></span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase"><?= $item['presentacion'] ?></span>
                        </td>
                        <td class="py-6">
                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-black"><?= $item['sede_nombre'] ?></span>
                        </td>
                        <td class="py-6 text-center">
                            <span class="text-xl font-black <?= $item['stock'] < 10 ? 'text-red-500' : 'text-slate-800' ?>"><?= $item['stock'] ?></span>
                        </td>
                        <td class="py-6">
                            <span class="font-bold text-slate-600"><?= $item['fecha_vencimiento'] ?></span>
                        </td>
                        <td class="py-6">
                            <code class="text-[10px] font-mono bg-slate-900 text-white px-2 py-1 rounded-md"><?= $item['lote'] ?></code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
