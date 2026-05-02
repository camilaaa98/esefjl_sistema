<?php
/**
 * Inventario Regional v9.8 - ESE Fabio Jaramillo
 * Paginación Inteligente (Ventana de 3) Inferior.
 */
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

$rol = $_SESSION['rol'];
$is_authorized = in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud']);

if (!$is_authorized) {
    die("Acceso restringido.");
}

$root_path = dirname(__DIR__, 3);
require_once $root_path . '/app/config/Database.php';
require_once $root_path . '/app/Controllers/InventoryController.php';

$db = Database::getInstance();
$inventory_ctrl = InventoryController::getInstance();

$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;

$total_items = $db->query("
    SELECT COUNT(*) 
    FROM inventario i
    WHERE (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= DATE('now'))
")->fetchColumn();

$total_pages = ceil($total_items / $limit);

// Lógica de Ventana de 3 Páginas
$start_page = max(1, $current_page_num - 1);
$end_page = min($total_pages, $start_page + 2);
if ($end_page - $start_page < 2) $start_page = max(1, $end_page - 2);

$inventory_data = $db->query("
    SELECT i.*, p.nombre_generico, p.laboratorio, s.nombre as sede_nombre, c.nombre as categoria_nombre
    FROM inventario i
    JOIN productos p ON i.producto_id = p.id
    JOIN sedes s     ON i.sede_id     = s.id
    JOIN categorias c ON p.categoria_id = c.id
    WHERE (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= DATE('now'))
    ORDER BY p.nombre_generico ASC
    LIMIT $limit OFFSET $offset
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Regional - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css?v=<?= time() ?>">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="mb-12 fade-in-institutional text-center">
                <h2 class="text-3xl font-black text-[#111111] tracking-tighter italic uppercase leading-none">Monitoreo Regional de <span class="text-[#d4af37]">Existencias</span></h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.4em] mt-3 italic">Visibilidad Total de la Red Hospitalaria Regional</p>
                <div class="w-20 h-1.5 bg-[#d4af37] mx-auto mt-6 rounded-full shadow-lg shadow-[#d4af37]/20"></div>
            </header>

            <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden fade-in-institutional">
                <div class="overflow-x-auto">
                        <table class="table-clinical w-full table-fixed">
                        <thead>
                            <tr class="bg-[#1a202c] border-b border-[#d4af37]/20">
                                <th class="w-[40%] pl-6 pr-4 py-8 text-[10px] font-black text-[#d4af37] uppercase tracking-widest text-left">Insumo / Detalle Técnico</th>
                                <th class="w-[15%] px-6 py-8 text-[10px] font-black text-[#d4af37] uppercase tracking-widest text-center">Sede Destino</th>
                                <th class="w-[15%] px-6 py-8 text-[10px] font-black text-[#d4af37] uppercase tracking-widest text-center">Stock Disponible</th>
                                <th class="w-[15%] px-6 py-8 text-[10px] font-black text-[#d4af37] uppercase tracking-widest text-center">Vigilancia Sanitaria</th>
                                <th class="w-[15%] px-6 py-8 text-[10px] font-black text-[#d4af37] uppercase tracking-widest text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($inventory_data as $item): 
                                $img = isset($item['imagen_url']) && $item['imagen_url'] ? $item['imagen_url'] : 'https://img.icons8.com/color/96/pill.png';
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="pl-6 pr-4 py-8 text-left">
                                    <div class="flex items-center gap-5 text-left">
                                        <div class="w-8 h-8 bg-white rounded-xl flex-shrink-0 flex items-center justify-center border border-slate-100 shadow-sm overflow-hidden group">
                                            <img src="<?= $img ?>" class="w-6 h-6 object-contain group-hover:scale-110 transition-transform" alt="Medicamento">
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#111111] uppercase italic leading-none mb-1"><?= $item['nombre_generico'] ?></p>
                                            <p class="text-[9px] text-slate-400 font-medium line-clamp-1 max-w-[200px] italic"><?= isset($item['descripcion_breve']) ? $item['descripcion_breve'] : 'Referencia regional validada.' ?></p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[8px] font-black text-white bg-slate-400 px-2 py-0.5 rounded uppercase tracking-tighter"><?= $item['laboratorio'] ?></span>
                                                <span class="text-[8px] font-black text-[#d4af37] bg-black px-2 py-0.5 rounded uppercase">LOTE: <?= $item['lote'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-2 bg-slate-900 text-[#d4af37] text-[9px] font-black rounded-xl uppercase italic tracking-widest shadow-sm"><?= $item['sede_nombre'] ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xl font-black text-slate-800 tracking-tighter italic"><?= number_format($item['stock_actual']) ?></span>
                                    <p class="text-[8px] font-bold text-slate-300 uppercase tracking-widest mt-1">Existencia Real</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center scale-90 mb-2"><?= InventoryController::getExpiryBadge($item) ?></div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter italic"><?= $item['fecha_vencimiento'] ?: 'VIGENTE' ?></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="window.location.href='vencidos?q=<?= $item['nombre_generico'] ?>'" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-900 text-[9px] font-black rounded-xl uppercase italic tracking-widest hover:bg-[#111111] hover:text-[#d4af37] hover:border-[#111111] transition-all shadow-sm">Gestionar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación Regional Superior -->
                <?php if ($total_pages > 1): ?>
                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100 flex flex-col items-center gap-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Página <?= $current_page_num ?> de <?= $total_pages ?></p>

                    <div class="flex items-center justify-center gap-3">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?p=<?= $current_page_num-1 ?>" class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-[#d4af37] hover:border-[#d4af37] transition-all text-[10px] font-black uppercase tracking-widest shadow-sm">Anterior</a>
                        <?php endif; ?>
                        
                            <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                <?php if ($i == $current_page_num): ?>
                                    <span class="w-9 h-9 flex items-center justify-center bg-slate-800 text-white text-xs font-bold rounded-lg shadow-md"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="?p=<?= $i ?>" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-300 text-slate-600 text-xs font-semibold rounded-lg hover:border-[#d4af37] hover:text-[#d4af37] transition-all"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($current_page_num < $total_pages): ?>
                            <a href="?p=<?= $current_page_num + 1 ?>" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-all text-xs font-semibold shadow-md">
                                Siguiente â†’
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-slate-200 border border-slate-300 rounded-lg text-slate-400 text-xs font-semibold cursor-not-allowed">
                                Siguiente â†’
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
</body>
</html>

