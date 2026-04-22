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
    'MilÃ¡n' => 'Milan.jpg',
    'San Antonio de Getucha' => 'SanAntonioGetucha.jpg',
    'Solano' => 'solano.jpg',
    'Solita' => 'solita.jpg',
    'ValparaÃ­so' => 'valparaiso.jpg'
];

if ($selected_sede_id) {
    $stmt = $db->prepare("SELECT * FROM sedes WHERE id = ?");
    $stmt->execute([$selected_sede_id]);
    $selected_sede_data = $stmt->fetch();

    $stmtCount = $db->prepare("SELECT COUNT(*) FROM inventario WHERE sede_id = ?");
    $stmtCount->execute([$selected_sede_id]);
    $total_items = $stmtCount->fetchColumn();

    $stmtInv = $db->prepare("
        SELECT i.*, p.nombre_generico, p.laboratorio, p.concentracion_presentacion
        FROM inventario i
        JOIN productos p ON i.producto_id = p.id
        WHERE i.sede_id = ?
        ORDER BY i.fecha_vencimiento ASC
        LIMIT ? OFFSET ?
    ");
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
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col items-center justify-center text-center gap-4 fade-in-institutional">
                <div>
                    <h2 class="text-3xl font-black text-[#111111] tracking-tight italic uppercase">Directorio de Sedes <span class="text-[#d4af37]">Municipales</span></h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">GestiÃ³n Regional de Insumos Ã‰lite</p>
                </div>
            </header>

            <!-- Grid de Sedes con ImÃ¡genes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 fade-in-institutional" style="animation-delay: 0.1s">
                <?php foreach ($sedes as $s): 
                    $img = $img_map[$s['nombre']] ?? 'AdministrativaFlorencia.jpg';
                ?>
                <a href="?sede_id=<?= $s['id'] ?>" class="group relative overflow-hidden h-40 rounded-3xl shadow-lg border <?= $selected_sede_id == $s['id'] ? 'ring-4 ring-[#d4af37] ring-offset-4 scale-105' : 'border-slate-100' ?> transition-all duration-500">
                    <img src="../img/sedes/<?= $img ?>" alt="<?= $s['nombre'] ?>" class="absolute inset-0 w-full h-full object-cover grayscale-[0.3] group-hover:grayscale-0 transition-all duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#111111] via-black/20 to-transparent p-6 flex flex-col justify-end">
                        <p class="font-black text-[11px] text-white uppercase tracking-tighter leading-tight italic drop-shadow-lg"><?= $s['nombre'] ?></p>
                        <p class="text-[8px] text-[#d4af37] font-bold uppercase tracking-[0.2em] drop-shadow-md"><?= $s['tipo'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selected_sede_id && $selected_sede_data): ?>
            <!-- Inventario de la Sede Seleccionada -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden fade-in-institutional" style="animation-delay: 0.2s">
                <div class="px-8 py-6 border-b border-slate-50 flex flex-col md:flex-row items-center justify-between bg-[#111111] gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="font-black text-[#d4af37] uppercase tracking-tighter italic text-xs">ðŸ“ LogÃ­stica Ã‰lite: <?= $selected_sede_data['nombre'] ?></h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest italic">PÃ¡gina <?= $current_page_num ?> de <?= $total_pages ?> â€” Registro Maestro de Insumos</p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num - 1 ?>" class="px-4 py-2 bg-white/10 border border-white/10 rounded-xl text-[10px] font-black uppercase text-white hover:bg-white/20 transition-all italic">â† Anterior</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_pages): ?>
                            <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num + 1 ?>" class="px-4 py-2 bg-[#d4af37] text-white rounded-xl text-[10px] font-black uppercase hover:scale-105 transition-all shadow-lg italic">Siguiente â†’</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-5 w-16">Item</th>
                                <th class="px-8 py-5">Insumo / Medicamento</th>
                                <th class="px-8 py-5 text-center">Stock Ã‰lite</th>
                                <th class="px-8 py-5 text-center">MÃ­n.</th>
                                <th class="px-8 py-5 text-center">Vencimiento</th>
                                <th class="px-8 py-5 text-center">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $counter = $offset + 1;
                            $today = date('Y-m-d');
                            $warn_date = date('Y-m-d', strtotime('+3 months'));
                            foreach ($sede_inventory as $i): 
                                $isCritical = $i['stock_actual'] < $i['stock_minimo'];
                                $fv = $i['fecha_vencimiento'] ?? null;
                                $venc_class = 'text-slate-800';
                                $venc_bg = 'bg-slate-50 border-slate-100';
                                $venc_label = 'Vigente';
                                if ($fv) {
                                    if ($fv < $today) { $venc_class='text-red-600'; $venc_bg='bg-red-50 border-red-100'; $venc_label='VENCIDO'; }
                                    elseif ($fv < $warn_date) { $venc_class='text-amber-700'; $venc_bg='bg-amber-50 border-amber-100'; $venc_label='Por vencer'; }
                                }
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-[10px] font-black text-slate-300 font-mono"><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-[11px] font-black text-[#111111] uppercase italic"><?= $i['nombre_generico'] ?></div>
                                    <div class="text-[9px] text-slate-400 font-bold uppercase">LAB: <?= $i['laboratorio'] ?></div>
                                </td>
                                <td class="px-8 py-5 text-center tabular-nums font-black text-sm <?= $isCritical ? 'text-red-500' : 'text-[#111111]' ?>">
                                    <?= number_format($i['stock_actual'], 0) ?>
                                </td>
                                <td class="px-8 py-5 text-center tabular-nums font-bold text-xs text-slate-300">
                                    <?= number_format($i['stock_minimo'], 0) ?>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <?php if ($fv): ?>
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase border <?= $venc_bg ?> <?= $venc_class ?>">
                                        <?= date('d/m/Y', strtotime($fv)) ?>
                                    </span><br><span class="text-[8px] <?= $venc_class ?> font-bold italic uppercase tracking-tighter"><?= $venc_label ?></span>
                                    <?php else: ?><span class="text-slate-200 text-[9px]">â€”</span><?php endif; ?>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $isCritical ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-900 text-white border border-black' ?>">
                                        <?= $isCritical ? 'Bajo Stock' : 'Disponible' ?>
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
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
