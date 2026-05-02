<?php
/**
 * Monitoreo de IPS - ESE Fabio Jaramillo
 * Versión 9.8: Paginación Inteligente (Ventana de 3) en la parte inferior.
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

$base_path = dirname(__DIR__, 3);
require_once $base_path . '/app/config/Database.php';
require_once $base_path . '/app/Controllers/InventoryController.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$user_sede_id = $_SESSION['sede_id'];
$is_directivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud']);

$selected_sede_id = $_GET['sede_id'] ?? ($is_directivo ? null : $user_sede_id);
if (!$is_directivo) $selected_sede_id = $user_sede_id;

$sedes = $is_directivo 
    ? $db->query("SELECT * FROM sedes ORDER BY nombre ASC")->fetchAll()
    : $db->query("SELECT * FROM sedes WHERE id = '$user_sede_id'")->fetchAll();

$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;

$selected_sede_data = null;
$sede_inventory = [];
$total_items = 0;

if ($selected_sede_id) {
    $stmt = $db->prepare("SELECT * FROM sedes WHERE id = ?");
    $stmt->execute([$selected_sede_id]);
    $selected_sede_data = $stmt->fetch();

    $stmtCount = $db->prepare("SELECT COUNT(*) FROM inventario WHERE sede_id = ? AND (fecha_vencimiento IS NULL OR fecha_vencimiento >= DATE('now'))");
    $stmtCount->execute([$selected_sede_id]);
    $total_items = $stmtCount->fetchColumn();

    $stmtInv = $db->prepare("
        SELECT i.*, p.nombre_generico, p.laboratorio, p.concentracion_presentacion, p.requiere_frio, p.es_delicado, p.imagen_url,
               s.stock_minimo_referencia
        FROM inventario i
        JOIN productos p ON i.producto_id = p.id
        JOIN sedes s ON i.sede_id = s.id
        WHERE i.sede_id = ? AND (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= DATE('now'))
        ORDER BY p.nombre_generico ASC
        LIMIT ? OFFSET ?
    ");
    $stmtInv->execute([$selected_sede_id, $limit, $offset]);
    $sede_inventory = $stmtInv->fetchAll();
}

$total_pages = ceil($total_items / $limit);

// Lógica de Ventana de 3 Páginas
$start_page = max(1, $current_page_num - 1);
$end_page = min($total_pages, $start_page + 2);
if ($end_page - $start_page < 2) $start_page = max(1, $end_page - 2);

$img_map = [
    'Florencia' => 'AdministrativaFlorencia.jpg',
    'Milán' => 'Milan.jpg',
    'San Antonio de Getucha' => 'SanAntonioGetucha.jpg',
    'Solano' => 'solano.jpg',
    'Solita' => 'solita.jpg',
    'Valparaíso' => 'valparaiso.jpg'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo IPS - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="mb-12 text-center fade-in-institutional">
                <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-4 border border-[#d4af37]/30">Inteligencia de Red Hospitalaria</span>
                <h2 class="text-4xl font-black text-[#111111] tracking-tighter italic uppercase leading-none">Monitoreo de la <span class="text-[#d4af37]">Red IPS</span></h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-[0.2em] mt-3">Visibilidad de Stock y Vigilancia Sanitaria en tiempo real</p>
            </header>

            <?php if ($is_directivo): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-12 fade-in-institutional">
                <?php foreach ($sedes as $s): 
                    $img = $img_map[$s['nombre']] ?? 'AdministrativaFlorencia.jpg';
                    $isActive = ($selected_sede_id == $s['id']);
                ?>
                <a href="?sede_id=<?= $s['id'] ?>" class="group relative h-48 rounded-[2rem] overflow-hidden shadow-lg border-2 <?= $isActive ? 'border-[#d4af37]' : 'border-transparent' ?> transition-all hover:scale-105">
                    <img src="<?= BASE_URL ?>/img/sedes/<?= $img ?>" alt="<?= $s['nombre'] ?>" class="absolute inset-0 w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent p-6 flex flex-col justify-end">
                        <p class="text-[11px] font-black text-white uppercase italic tracking-tighter text-center"><?= $s['nombre'] ?></p>
                        <p class="text-[8px] text-[#d4af37] font-bold uppercase tracking-widest text-center"><?= $s['tipo'] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($selected_sede_data): ?>
            <section class="fade-in-institutional">
                <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden">
                    <div class="px-10 py-8 bg-slate-900 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-xl font-black text-[#d4af37] uppercase italic tracking-tighter">ðŸ“¦ Sede: <?= $selected_sede_data['nombre'] ?></h3>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Inventario Maestro Filtrado</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-clinical w-full">
                            <thead>
                                <tr class="bg-[#1a202c] border-b-2 border-[#d4af37]">
                                    <th class="px-3 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-left">Insumo / Detalle Técnico</th>
                                    <th class="px-6 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Stock Disponible</th>
                                    <th class="px-6 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Semáforo</th>
                                    <th class="px-6 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Vigilancia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($sede_inventory as $i): 
                                    $img = isset($i['imagen_url']) ? $i['imagen_url'] : null;
                                    if (!$img || strpos($img, 'unsplash') !== false) {
                                        $name = strtolower($i['nombre_generico']);
                                        if (strpos($name, 'acetaminofen') !== false) $img = '<?= BASE_URL ?>/img/productos/acetaminofen.png';
                                        elseif (strpos($name, 'loratadina') !== false) $img = '<?= BASE_URL ?>/img/productos/loratadina.png';
                                        elseif (strpos($name, 'salina') !== false) $img = '<?= BASE_URL ?>/img/productos/solucion_salina.png';
                                        else $img = 'https://img.icons8.com/color/96/pill.png';
                                    }
                                ?>
                                <tr class="hover:bg-slate-50/50 transition-all">
                                    <td class="text-left">
                                        <div class="flex items-center gap-5 text-left">
                                            <div class="w-16 h-16 bg-white rounded-2xl flex-shrink-0 flex items-center justify-center border border-slate-100 shadow-sm overflow-hidden group">
                                                <img src="<?= $img ?>" class="w-14 h-14 object-cover group-hover:scale-110 transition-transform" alt="Medicamento">
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-[#111111] uppercase italic leading-none mb-1"><?= $i['nombre_generico'] ?></p>
                                                <p class="text-[9px] text-slate-400 font-medium line-clamp-1 max-w-[200px] italic">Referencia maestra validada.</p>
                                                <div class="flex items-center gap-2 mt-2">
                                                    <span class="text-[8px] font-black text-white bg-slate-400 px-2 py-0.5 rounded uppercase tracking-tighter"><?= $i['laboratorio'] ?></span>
                                                    <span class="text-[8px] font-black text-[#d4af37] bg-black px-2 py-0.5 rounded uppercase">LOTE: <?= $i['lote'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xl font-black text-slate-800 tracking-tighter italic"><?= number_format($i['stock_actual']) ?></span>
                                        <p class="text-[8px] font-bold text-slate-300 uppercase tracking-widest mt-1">Existencia Real</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center scale-90 mb-2"><?= InventoryController::getStockBadge($i) ?></div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter italic">Estado Operativo</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center scale-90 mb-2"><?= InventoryController::getExpiryBadge($i) ?></div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter italic"><?= $i['fecha_vencimiento'] ?: 'VIGENTE' ?></p>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación Centrada -->
                    <div class="px-8 py-8 bg-[#111111] border-t border-[#d4af37]/20">
                        <div class="flex flex-col items-center gap-4">
                            <p class="text-[10px] font-black text-[#d4af37] uppercase tracking-[0.3em] italic">
                                Página <?= $current_page_num ?> de <?= $total_pages ?> <span class="text-white/10 mx-2">|</span> <?= $total_items ?> Registros Activos
                            </p>

                            <div class="flex items-center gap-3">
                                <?php if ($current_page_num > 1): ?>
                                    <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num - 1 ?>" class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[#d4af37] hover:bg-[#d4af37] hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest">
                                        ANTERIOR
                                    </a>
                                <?php endif; ?>
                                
                                <div class="flex gap-2">
                                    <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                        <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all text-[10px] font-black <?= $i == $current_page_num ? 'bg-[#d4af37] text-[#111111] shadow-lg shadow-[#d4af37]/20' : 'bg-white/5 text-slate-400 border border-white/10 hover:border-[#d4af37] hover:text-[#d4af37]' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($current_page_num < $total_pages): ?>
                                    <a href="?sede_id=<?= $selected_sede_id ?>&p=<?= $current_page_num + 1 ?>" class="px-6 py-3 bg-[#d4af37] text-[#111111] rounded-xl hover:bg-white hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-[#d4af37]/10">
                                        SIGUIENTE
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
    <script src="<?= BASE_URL ?>/public/js/animations.js" defer></script>
</body>
</html>

