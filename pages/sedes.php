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

$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;

$selected_sede_id = $_GET['sede_id'] ?? null;
$selected_sede_data = null;
$sede_inventory = [];
$total_items = 0;

$img_map = [
    'Florencia' => 'AdministrativaFlorencia.jpg',
    'Milán' => 'Milan.jpg',
    'San Antonio de Getucha' => 'SanAntonioGetucha.jpg',
    'Solano' => 'solano.jpg',
    'Solita' => 'solita.jpg',
    'Valparaíso' => 'valparaiso.jpg'
];

if ($selected_sede_id) {
    $stmt = $db->prepare("SELECT * FROM sedes WHERE id = ?");
    $stmt->execute([$selected_sede_id]);
    $selected_sede_data = $stmt->fetch();

    $stmtCount = $db->prepare("SELECT COUNT(*) FROM inventario WHERE sede_id = ?");
    $stmtCount->execute([$selected_sede_id]);
    $total_items = $stmtCount->fetchColumn();

    $stmtInv = $db->prepare("SELECT * FROM inventario WHERE sede_id = ? ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmtInv->execute([$selected_sede_id, $limit, $offset]);
    $sede_inventory = $stmtInv->fetchAll();
}

$total_pages = ceil($total_items / $limit);
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .sede-img { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .group:hover .sede-img { transform: scale(1.1); filter: brightness(0.7); }
    </style>
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

            <!-- Grid de Sedes con Imágenes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <?php foreach ($sedes as $s): 
                    $img = $img_map[$s['nombre']] ?? 'AdministrativaFlorencia.jpg';
                ?>
                <a href="?sede_id=<?= $s['id'] ?>" class="group relative overflow-hidden h-40 rounded-3xl shadow-sm border <?= $selected_sede_id == $s['id'] ? 'ring-4 ring-medical-500 ring-offset-4' : 'border-gray-100' ?> transition-all">
                    <img src="../img/sedes/<?= $img ?>" alt="<?= $s['nombre'] ?>" class="absolute inset-0 w-full h-full object-cover sede-img border border-white/20">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent p-6 flex flex-col justify-end">
                        <p class="font-black text-[11px] text-white uppercase tracking-tighter leading-tight italic drop-shadow-lg"><?= $s['nombre'] ?></p>
                        <p class="text-[8px] text-medical-400 font-bold uppercase tracking-[0.2em] drop-shadow-md"><?= $s['tipo'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selected_sede_id && $selected_sede_data): ?>
            <!-- Inventario de la Sede Seleccionada -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden animate-in fade-in slide-in-from-bottom-4">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700/50 flex flex-col md:flex-row items-center justify-between bg-slate-50/50 dark:bg-slate-900/50 gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic text-xs">📍 Logística Local: <?= $selected_sede_data['nombre'] ?></h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic">Página <?= $current_page_num ?> de <?= $total_pages ?> — Mostrando 10 de <?= $total_items ?> registros</p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num - 1 ?>" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-100 dark:border-slate-600 rounded-xl text-[10px] font-black uppercase hover:bg-gray-50 transition-all shadow-sm italic">← Anterior</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_pages): ?>
                            <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num + 1 ?>" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase hover:scale-105 transition-all shadow-lg italic">Siguiente →</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-slate-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4 w-16">Item</th>
                                <th class="px-8 py-4">Insumo / Medicamento</th>
                                <th class="px-8 py-4 text-center">Stock Físico</th>
                                <th class="px-8 py-4 text-center">Referencia Mín.</th>
                                <th class="px-8 py-4 text-center">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php 
                            $counter = $offset + 1;
                            foreach ($sede_inventory as $i): 
                                $isCritical = $i['stock_actual'] < $i['stock_minimo'];
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-black text-gray-300 font-mono"><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="text-[11px] font-black text-gray-800 dark:text-white uppercase italic"><?= $i['nombre_generico'] ?></div>
                                    <div class="text-[9px] text-gray-400 font-bold">LAB: <?= $i['laboratorio'] ?></div>
                                </td>
                                <td class="px-8 py-4 text-center tabular-nums font-black text-sm <?= $isCritical ? 'text-red-500' : 'text-slate-700' ?>">
                                    <?= number_format($i['stock_actual'], 0) ?>
                                </td>
                                <td class="px-8 py-4 text-center tabular-nums font-bold text-xs text-gray-400">
                                    <?= number_format($i['stock_minimo'], 0) ?>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $isCritical ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100' ?>">
                                        <?= $isCritical ? 'Huelga de Stock' : 'Garantizado ✓' ?>
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
