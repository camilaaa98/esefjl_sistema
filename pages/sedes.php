<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/InventoryController.php';

$db = Database::getInstance();
$sedes = $db->query("SELECT * FROM sedes ORDER BY nombre ASC")->fetchAll();
$vencidos_count = count(InventoryController::getExpiredInventory());

$selected_sede_id = $_GET['sede_id'] ?? null;
$selected_sede_data = null;
$sede_inventory = [];

if ($selected_sede_id) {
    $stmt = $db->prepare("SELECT * FROM sedes WHERE id = ?");
    $stmt->execute([$selected_sede_id]);
    $selected_sede_data = $stmt->fetch();
    $sede_inventory = InventoryController::getInventoryBySede($selected_sede_id);
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedes Municipales - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col items-center justify-center text-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Directorio de Sedes Municipales</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">Seleccione una IPS para visualizar su estado de stock y solicitudes</p>
                </div>
            </header>

            <!-- Grid de Sedes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <?php foreach ($sedes as $s): ?>
                <a href="?sede_id=<?= $s['id'] ?>" class="group p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border <?= $selected_sede_id == $s['id'] ? 'border-medical-500 ring-2 ring-medical-500/20' : 'border-gray-100 dark:border-slate-700 hover:border-medical-200' ?> transition-all text-center flex flex-col items-center gap-3">
                    <span class="text-3xl grayscale group-hover:grayscale-0 transition-all opacity-50 group-hover:opacity-100 italic font-black text-medical-500">
                        <?= substr($s['nombre'], 0, 1) ?>
                    </span>
                    <div>
                        <p class="font-black text-xs text-gray-800 dark:text-white uppercase tracking-tighter italic"><?= $s['nombre'] ?></p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest"><?= $s['tipo'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selected_sede_data): ?>
            <!-- Inventario de la Sede Seleccionada -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden animate-in fade-in slide-in-from-bottom-4">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700/50 flex flex-col md:flex-row items-center justify-between bg-slate-50/50 dark:bg-slate-900/50 gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic text-sm">📍 Inventario Local: <?= $selected_sede_data['nombre'] ?></h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic"><?= count($sede_inventory) ?> Medicamentos Registrados</p>
                    </div>
                    <button class="px-6 py-2 bg-slate-900 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg shadow-slate-900/20">Solicitar Auditoría</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-slate-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Insumo / Medicamento</th>
                                <th class="px-8 py-4 text-center">Stock Actual</th>
                                <th class="px-8 py-4 text-center">Stock Mínimo</th>
                                <th class="px-8 py-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php foreach ($sede_inventory as $i): 
                                $isCritical = $i['stock_actual'] < $i['stock_minimo'];
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="text-[11px] font-black text-gray-800 dark:text-white uppercase italic"><?= $i['nombre_generico'] ?></div>
                                    <div class="text-[9px] text-gray-400 font-bold">LAB: <?= $i['laboratorio'] ?></div>
                                </td>
                                <td class="px-8 py-4 text-center tabular-nums font-black text-sm <?= $isCritical ? 'text-red-500' : 'text-slate-700' ?>">
                                    <?= $i['stock_actual'] ?>
                                </td>
                                <td class="px-8 py-4 text-center tabular-nums font-bold text-xs text-gray-400">
                                    <?= $i['stock_minimo'] ?>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $isCritical ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' ?>">
                                        <?= $isCritical ? 'Bajo Stock' : 'Suficiente' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
