<?php
/**
 * Dashboard Administrativo v9.8 - ESE Fabio Jaramillo
 * Centrado de Información y Paginación ANTERIOR/SIGUIENTE.
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

$root_path = dirname(__DIR__, 2);
require_once $root_path . '/app/Controllers/InventoryController.php';
require_once $root_path . '/app/Helpers/ViewHelper.php';
require_once $root_path . '/app/config/Database.php';

$db = Database::getInstance();
$inventory_ctrl = InventoryController::getInstance();

$sede_id_session = (int)$_SESSION['sede_id'];
$rol = $_SESSION['rol'] ?? '';
$florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();

// --- Filtros y Paginación ---
$limit = 10;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$filter_sede = isset($_GET['filter_sede']) ? $_GET['filter_sede'] : $florencia_id;
$filter_lab = isset($_GET['filter_lab']) ? $_GET['filter_lab'] : '';
$filter_query = isset($_GET['q']) ? $_GET['q'] : '';

// SEGURIDAD: Forzar la sede del usuario si no es directivo
if (!in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud'])) {
    $filter_sede = $sede_id_session;
}

$filters = [
    'sede_id' => $filter_sede,
    'laboratorio' => $filter_lab,
    'query' => $filter_query
];

// Datos para la vista
$all_ips_data = $inventory_ctrl->getFilteredInventory($filters, $limit, $offset);
$total_items = $inventory_ctrl->getFilteredInventoryCount($filters);
$total_pages = ceil($total_items / $limit);

$ips_summary = $inventory_ctrl->getSummaryBySede();
$unique_labs = $inventory_ctrl->getUniqueLaboratories();
$unique_products = $inventory_ctrl->getUniqueProductNames();
$sedes = $db->query("SELECT id, nombre FROM sedes ORDER BY nombre ASC")->fetchAll();

$isDirectivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud']);

// --- Cálculo de Eficiencia Real ---
$stockCriticoCount = 0;
$totalVigentes = 0;
$all_stock_vigente = $db->query("
    SELECT i.stock_actual, s.stock_minimo_referencia 
    FROM inventario i 
    JOIN sedes s ON i.sede_id = s.id 
    WHERE (i.fecha_vencimiento IS NULL OR i.fecha_vencimiento >= DATE('now'))
")->fetchAll();

foreach ($all_stock_vigente as $item) {
    $totalVigentes++;
    if ($item['stock_actual'] < $item['stock_minimo_referencia']) {
        $stockCriticoCount++;
    }
}
$efficiency = $totalVigentes > 0 ? round((($totalVigentes - $stockCriticoCount) / $totalVigentes) * 100, 1) : 100;

// Lógica de Ventana de 3 Páginas
$start_page = max(1, $page - 1);
$end_page = min($total_pages, $start_page + 2);
if ($end_page - $start_page < 2) $start_page = max(1, $end_page - 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INICIO - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css?v=<?= time() ?>">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="flex flex-col lg:flex-row items-stretch gap-6 mb-8 fade-in-institutional">
                <!-- Branding -->
                <div class="bg-white p-6 rounded-[2.5rem] shadow-xl border border-slate-100 flex items-center gap-6 border-l-[6px] border-l-[#d4af37] min-w-[280px]">
                    <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="Logo" class="w-20 h-20 rounded-2xl shadow-lg object-contain">
                    <div>
                        <h2 class="text-4xl font-black text-[#111111] tracking-tighter uppercase leading-none">ESEFJL</h2>
                        <p class="text-[#d4af37] text-[9px] font-black uppercase tracking-[0.2em] mt-2 italic"><?= $_SESSION['sede'] ?? 'ADMIN CENTRAL' ?></p>
                    </div>
                </div>

                <!-- Resumen Ejecutivo -->
                <?php if ($isDirectivo): ?>
                <div class="flex-1 bg-white p-6 rounded-[2.5rem] shadow-xl border border-slate-100 flex flex-col md:flex-row items-center gap-6">
                    <div class="flex-1 text-center md:text-left pl-4">
                        <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3rem] mb-1">Auditoría de Gestión</h3>
                        <h2 class="text-xl font-black text-[#111111] leading-tight mb-1 italic uppercase tracking-tighter">Eficiencia en <span class="text-[#d4af37]">Suministros</span></h2>
                        <p class="text-slate-500 text-[10px] leading-relaxed uppercase font-bold tracking-[0.1em] opacity-80">Control en tiempo real de la red hospitalaria regional.</p>
                    </div>
                    <div class="w-full md:w-auto px-10 py-4 bg-[#111111] rounded-[2rem] shadow-2xl border-2 border-[#d4af37]/20 text-center">
                        <p class="text-[8px] font-black text-[#d4af37] uppercase tracking-widest mb-1">Índice Global</p>
                        <p class="text-3xl font-black text-white italic tracking-tighter"><?= $efficiency ?><span class="text-[#d4af37] text-xl">%</span></p>
                    </div>
                </div>

                <div class="bg-[#111111] p-6 rounded-[2.5rem] text-white flex flex-col justify-center items-center min-w-[180px] relative overflow-hidden group cursor-pointer hover:shadow-2xl transition-all border-2 border-[#d4af37]/10" onclick="window.location.href='sedes'">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#d4af37]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h3 class="text-[8px] font-black text-[#d4af37] uppercase tracking-[0.2em] mb-2 relative z-10">Cobertura de Red</h3>
                    <div class="flex flex-col items-center gap-0 relative z-10">
                        <span class="text-5xl font-black italic tracking-tighter text-[#d4af37]"><?= count($ips_summary) ?></span>
                        <p class="text-[10px] font-black uppercase italic tracking-tighter">IPS ACTIVAS</p>
                    </div>
                </div>
                <?php endif; ?>
            </header>

            <!-- SECCIí“N 1: RESUMEN GENERAL POR IPS -->
            <?php if ($isDirectivo): ?>
            <section class="mb-10 fade-in-institutional">
                <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 text-center">Capacidad de Respuesta Regional</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
                    <?php foreach ($ips_summary as $summary): ?>
                    <a href="sedes?sede_id=<?= $summary['id'] ?>" class="bg-white p-5 rounded-[2rem] shadow-lg border border-slate-100 hover:border-[#d4af37] transition-all group text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#d4af37]/10 group-hover:bg-[#d4af37] transition-all"></div>
                        <div class="flex justify-center items-start mb-3 relative">
                            <span class="text-[10px] font-black text-[#111111] uppercase italic border-b-2 border-[#d4af37] tracking-tighter leading-none pb-1"><?= htmlspecialchars($summary['nombre']) ?></span>
                            <?php if ($summary['items_criticos'] > 0): ?>
                                <span class="absolute -top-3 -right-2 w-5 h-5 flex items-center justify-center bg-red-600 text-white text-[9px] font-black rounded-full animate-pulse shadow-lg shadow-red-200">!</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col items-center gap-0">
                            <span class="text-3xl font-black text-slate-900 tracking-tighter"><?= number_format($summary['stock_total']) ?></span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Unidades Stock</span>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-50 flex justify-center gap-4">
                            <div class="text-center">
                                <p class="text-[10px] font-black text-slate-700"><?= $summary['total_items'] ?></p>
                                <p class="text-[7px] font-bold text-slate-400 uppercase tracking-tighter">REFS</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black <?= $summary['items_criticos'] > 0 ? 'text-red-600' : 'text-slate-700' ?>"><?= $summary['items_criticos'] ?></p>
                                <p class="text-[7px] font-bold text-slate-400 uppercase tracking-tighter">CRíTICOS</p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECCIí“N 2: MONITOREO DETALLADO CON DOBLE SEMíFORO -->
            <section class="fade-in-institutional mt-16">
                <header class="flex flex-col lg:flex-row items-end justify-between gap-8 mb-10 border-b border-slate-100 pb-10">
                    <div>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase leading-none mb-2">Semaforización <span class="text-[#d4af37]">por Medicamento</span></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] italic">Detección de Lotes Vencidos vs Niveles de Stock</p>
                    </div>

                    <form method="GET" class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-[2.5rem] shadow-xl border border-slate-100 ring-4 ring-slate-50/50">
                        <div class="flex items-center gap-2 px-4 border-r border-slate-100">
                            <span class="text-[9px] font-black text-slate-300">SEDE:</span>
                            <select name="filter_sede" class="bg-transparent border-none text-[10px] font-black uppercase outline-none cursor-pointer text-[#111111]">
                                <?php foreach ($sedes as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $filter_sede == $s['id'] ? 'selected' : '' ?>><?= $s['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" name="filter_lab" list="list-labs" value="<?= htmlspecialchars($filter_lab) ?>" placeholder="FILTRAR LABORATORIO" class="bg-transparent border-none text-[10px] font-black uppercase w-32 outline-none placeholder:text-slate-300 px-4 border-r border-slate-100">
                        <input type="text" name="q" list="list-products" value="<?= htmlspecialchars($filter_query) ?>" placeholder="BUSCAR MEDICAMENTO..." class="bg-transparent border-none text-[10px] font-black uppercase w-48 outline-none placeholder:text-slate-300 px-4">
                        <button type="submit" class="bg-[#111111] text-[#d4af37] px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-black/10">Ejecutar Filtro</button>
                    </form>
                </header>

                    <div class="table-clinical-wrapper">
                        <table class="table-clinical">
                            <thead>
                                <tr>
                                    <th class="text-left">Insumo / Clasificación Técnica</th>
                                    <th class="text-center">Valor Red</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Vigilancia</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_ips_data as $item): 
                                    $img = $item['imagen_url'];
                                    // Validar si la primera letra es mayúscula o minúscula según el archivo real
                                    if (!$img) $img = 'https://img.icons8.com/color/96/pill.png';
                                ?>
                                <tr>
                                    <td>
                                        <div class="product-cell-detail">
                                            <div class="product-img-container" onclick="openPreview('<?= BASE_URL . '/' . $img ?>', '<?= htmlspecialchars($item['nombre_generico']) ?>')">
                                                <img src="<?= BASE_URL . '/' . $img ?>" onerror="this.src='https://img.icons8.com/color/96/pill.png'">
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-slate-900 uppercase italic leading-none mb-1"><?= htmlspecialchars($item['nombre_generico']) ?></p>
                                                <p class="text-[10px] text-slate-400 font-medium line-clamp-1 mb-2"><?= htmlspecialchars($item['descripcion_breve'] ?: 'Sin descripción adicional.') ?></p>
                                                
                                                <div class="product-info-grid">
                                                    <span class="info-tag tag-gold">💊 <?= htmlspecialchars($item['presentacion'] ?? 'N/A') ?></span>
                                                    <span class="info-tag tag-blue">📏 <?= htmlspecialchars($item['tamano'] ?? 'N/A') ?></span>
                                                    
                                                    <?php $audience = $item['target_audience'] ?? 'N/A'; ?>
                                                    <?php if($audience == 'Mujeres'): ?>
                                                        <span class="info-tag tag-pink">👩 <?= $audience ?></span>
                                                    <?php elseif($audience == 'Niños'): ?>
                                                        <span class="info-tag tag-blue">👶 <?= $audience ?></span>
                                                    <?php elseif($audience == 'Hombres'): ?>
                                                        <span class="info-tag tag-blue">👨 <?= $audience ?></span>
                                                    <?php else: ?>
                                                        <span class="info-tag tag-green">👥 <?= $audience ?></span>
                                                    <?php endif; ?>

                                                    <?php if(!empty($item['requiere_frio'])): ?>
                                                        <span class="info-tag tag-blue">❄️ Refrigeración</span>
                                                    <?php endif; ?>

                                                    <?php if(!empty($item['es_delicado'])): ?>
                                                        <span class="info-tag tag-perishable">⚠️ Delicado</span>
                                                    <?php endif; ?>

                                                    <?php if(!empty($item['perecedero'])): ?>
                                                        <span class="info-tag tag-perishable">⏳ Perecedero</span>
                                                    <?php else: ?>
                                                        <span class="info-tag tag-green">🛡️ No Perecedero</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-bold text-slate-900">$<?= number_format($item['valor_unitario'] ?: 0) ?></td>
                                    <td class="text-center">
                                        <?php
                                            $stk = intval($item['stock_actual'] ?? 0);
                                            $min = intval($item['stock_minimo'] ?? 25);
                                            if ($stk >= $min) $badge = 'badge-success';
                                            elseif ($stk >= $min * 0.5) $badge = 'badge-warning';
                                            else $badge = 'badge-critical';
                                        ?>
                                        <div class="flex flex-col items-center">
                                            <span class="text-xl font-black text-slate-900"><?= number_format($stk) ?></span>
                                            <span class="badge <?= $badge ?> scale-75">Uds</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?= InventoryController::getExpiryBadge($item) ?>
                                    </td>
                                    <td class="text-center">
                                        <button onclick="window.location.href='inventario-central?q=<?= urlencode($item['nombre_generico']) ?>'" class="btn-institutional !px-4 !py-2 !text-[9px]">Gestionar</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                    <!-- Paginación Centrada -->
                    <?php if ($total_pages > 1): ?>
                    <div class="px-8 py-8 bg-[#111111] border-t border-[#d4af37]/20">
                        <div class="flex flex-col items-center gap-4">
                            <p class="text-[10px] font-black text-[#d4af37] uppercase tracking-[0.2em] italic">
                                Página <?= $page ?> de <?= $total_pages ?> <span class="text-white/20 mx-2">|</span> Total <?= $total_items ?> Registros Detectados
                            </p>

                            <div class="flex items-center gap-3">
                                <?php if ($page > 1): ?>
                                    <a href="?p=<?= $page-1 ?>&filter_sede=<?= $filter_sede ?>&filter_lab=<?= $filter_lab ?>&q=<?= $filter_query ?>" class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[#d4af37] hover:bg-[#d4af37] hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest">
                                        ANTERIOR
                                    </a>
                                <?php endif; ?>
                                
                                <div class="flex gap-2">
                                    <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                        <a href="?p=<?= $i ?>&filter_sede=<?= $filter_sede ?>&filter_lab=<?= $filter_lab ?>&q=<?= $filter_query ?>" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all text-[10px] font-black <?= $i == $page ? 'bg-[#d4af37] text-[#111111] shadow-lg shadow-[#d4af37]/20' : 'bg-white/5 text-slate-400 border border-white/10 hover:border-[#d4af37] hover:text-[#d4af37]' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?p=<?= $page+1 ?>&filter_sede=<?= $filter_sede ?>&filter_lab=<?= $filter_lab ?>&q=<?= $filter_query ?>" class="px-6 py-3 bg-[#d4af37] text-[#111111] rounded-xl hover:bg-white hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-[#d4af37]/10">
                                        SIGUIENTE
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    <!-- MODAL DE PREVIEW -->
    <div id="imageModal" class="modal-preview">
        <span class="modal-close" onclick="closePreview()">&times;</span>
        <img class="modal-preview-content" id="modalImg">
        <div id="modalCaption" class="text-center mt-4 text-white font-bold uppercase tracking-widest text-sm"></div>
    </div>

    <script>
        function openPreview(src, title) {
            document.getElementById('imageModal').style.display = "block";
            document.getElementById('modalImg').src = src;
            document.getElementById('modalCaption').innerHTML = title;
        }
        function closePreview() {
            document.getElementById('imageModal').style.display = "none";
        }
        // Cerrar al clickear fuera
        window.onclick = function(event) {
            let modal = document.getElementById('imageModal');
            if (event.target == modal) closePreview();
        }
    </script>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
</body>
</html>
