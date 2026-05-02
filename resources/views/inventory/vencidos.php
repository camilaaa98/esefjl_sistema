<?php
/**
 * Monitor de Vencimientos - ESE Fabio Jaramillo
 * Versión 10.0: Filtros por Municipio y Lote.
 */
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}

$root_path = dirname(__DIR__, 3);
require_once $root_path . '/app/config/Database.php';
require_once $root_path . '/app/Controllers/InventoryController.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$user_sede_id = $_SESSION['sede_id'];
$is_directivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud']);

// Filtros GET
$filter_sede  = isset($_GET['sede'])  ? trim($_GET['sede'])  : '';
$filter_lote  = isset($_GET['lote'])  ? trim($_GET['lote'])  : '';

// Parámetros de Paginación
$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit  = 10;
$offset = ($current_page_num - 1) * $limit;

// Construir WHERE dinámico con parámetros preparados
$conditions = ["i.fecha_vencimiento < DATE('now')"];
$params = [];

if (!$is_directivo) {
    $conditions[] = "i.sede_id = ?";
    $params[] = $user_sede_id;
}
if ($filter_sede !== '') {
    $conditions[] = "s.id = ?";
    $params[] = $filter_sede;
}
if ($filter_lote !== '') {
    $conditions[] = "i.lote LIKE ?";
    $params[] = "%$filter_lote%";
}

$where_clause = 'WHERE ' . implode(' AND ', $conditions);

// Total para paginación
$stmtCount = $db->prepare("SELECT COUNT(*) FROM inventario i JOIN sedes s ON i.sede_id = s.id $where_clause");
$stmtCount->execute($params);
$total_items = $stmtCount->fetchColumn();
$total_pages = max(1, ceil($total_items / $limit));

// Ventana de 3 páginas
$start_page = max(1, $current_page_num - 1);
$end_page   = min($total_pages, $start_page + 2);
if ($end_page - $start_page < 2) $start_page = max(1, $end_page - 2);

// Datos paginados
$stmtData = $db->prepare("
    SELECT i.*, p.nombre_generico, p.laboratorio, p.imagen_url, s.nombre as sede_nombre, s.id as sede_id_val
    FROM inventario i
    JOIN productos p ON i.producto_id = p.id
    JOIN sedes s     ON i.sede_id     = s.id
    $where_clause
    ORDER BY i.fecha_vencimiento ASC
    LIMIT $limit OFFSET $offset
");
$stmtData->execute($params);
$vencidos = $stmtData->fetchAll();

// Lista de sedes para el selector (solo directivos)
$all_sedes = $is_directivo ? $db->query("SELECT id, nombre FROM sedes ORDER BY nombre ASC")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saneamiento y Cuarentena - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="mb-12 fade-in-institutional text-center">
                <span class="inline-block px-4 py-1.5 bg-red-600 text-white text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-4 border border-red-800 shadow-lg shadow-red-200">Alerta de Seguridad Sanitaria</span>
                <h2 class="text-4xl font-black text-[#111111] tracking-tighter italic uppercase leading-none">Cuarentena y <span class="text-red-600">Saneamiento</span></h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-[0.2em] mt-3">Gestión de Lotes Vencidos / Retiro de Circulación Red ESEFJL</p>
            </header>

            <!-- BARRA DE FILTROS -->
            <form method="GET" action="" style="display:flex; align-items:center; gap:10px; background:#1a202c; border-radius:1.5rem; padding:14px 20px; margin-bottom:20px; flex-wrap:wrap;">
                <?php if ($is_directivo): ?>
                <div style="display:flex; align-items:center; gap:8px; background:rgba(255,255,255,.05); border:1px solid rgba(212,175,55,.2); border-radius:10px; padding:6px 12px;">
                    <span style="font-size:9px; color:#d4af37; font-weight:900; text-transform:uppercase; letter-spacing:.1em; white-space:nowrap;">🏢 Municipio</span>
                    <select name="sede" style="background:transparent; border:none; color:#fff; font-size:10px; font-weight:700; outline:none; cursor:pointer; min-width:130px;">
                        <option value="" style="background:#1a202c;">— Todos —</option>
                        <?php foreach ($all_sedes as $s): ?>
                            <option value="<?= $s['id'] ?>" style="background:#1a202c;" <?= ($filter_sede == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div style="display:flex; align-items:center; gap:8px; background:rgba(255,255,255,.05); border:1px solid rgba(212,175,55,.2); border-radius:10px; padding:6px 12px; flex:1; min-width:160px;">
                    <span style="font-size:9px; color:#d4af37; font-weight:900; text-transform:uppercase; letter-spacing:.1em; white-space:nowrap;">🏷️ Lote</span>
                    <input type="text" name="lote" value="<?= htmlspecialchars($filter_lote) ?>" placeholder="Buscar número de lote..." style="background:transparent; border:none; color:#fff; font-size:10px; font-weight:700; outline:none; width:100%; placeholder-color:#64748b;">
                </div>

                <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:8px 18px; border-radius:10px; font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; cursor:pointer; white-space:nowrap;">Filtrar</button>

                <?php if ($filter_sede || $filter_lote): ?>
                <a href="vencidos" style="background:rgba(255,255,255,.08); color:#94a3b8; text-decoration:none; padding:8px 14px; border-radius:10px; font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; white-space:nowrap;">✕ Limpiar</a>
                <?php endif; ?>

                <div style="margin-left:auto; text-align:right; white-space:nowrap;">
                    <p style="font-size:10px; color:#dc2626; font-weight:900; margin:0;"><?= $total_items ?> lotes vencidos</p>
                    <?php if ($filter_sede || $filter_lote): ?>
                    <p style="font-size:8px; color:#64748b; margin:0; text-transform:uppercase;">con filtros activos</p>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (empty($vencidos)): ?>
                <div class="py-32 text-center bg-white rounded-[4rem] border border-slate-100 shadow-xl fade-in-institutional">
                    <div class="opacity-10 text-8xl mb-6">🔍</div>
                    <p class="text-slate-300 text-xs font-black italic tracking-[0.2em] uppercase">No se encontraron lotes con los filtros aplicados.</p>
                    <a href="vencidos" style="display:inline-block; margin-top:16px; background:#1a202c; color:#d4af37; padding:8px 20px; border-radius:10px; font-size:10px; font-weight:900; text-decoration:none; text-transform:uppercase;">Ver todos los vencidos</a>
                </div>
            <?php else: ?>
                <div style="background:#fff; border-radius:2rem; box-shadow:0 20px 40px -10px rgba(15,23,42,.1); border:1px solid #fecaca; overflow:hidden;">
                    <!-- CABECERA -->
                    <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1.3fr 0.9fr; background:#1a202c; border-bottom:2px solid #d4af37;">
                        <div style="padding:14px 12px; font-size:10px; font-weight:900; color:#d4af37; text-transform:uppercase; letter-spacing:.15em; text-align:left;">Insumo / Detalle Técnico</div>
                        <div style="padding:14px 12px; font-size:10px; font-weight:900; color:#d4af37; text-transform:uppercase; letter-spacing:.15em; text-align:center;">Sede Origen</div>
                        <div style="padding:14px 12px; font-size:10px; font-weight:900; color:#d4af37; text-transform:uppercase; letter-spacing:.15em; text-align:center;">Stock Actual</div>
                        <div style="padding:14px 12px; font-size:10px; font-weight:900; color:#d4af37; text-transform:uppercase; letter-spacing:.15em; text-align:center;">Fecha Vencimiento</div>
                        <div style="padding:14px 12px; font-size:10px; font-weight:900; color:#d4af37; text-transform:uppercase; letter-spacing:.15em; text-align:center;">Acción</div>
                    </div>
                    <!-- FILAS -->
                    <?php foreach ($vencidos as $item):
                        $img = isset($item['imagen_url']) ? $item['imagen_url'] : null;
                        if (!$img || strpos($img, 'unsplash') !== false || strpos($img, 'icons8') !== false) {
                            $name = strtolower($item['nombre_generico']);
                            if (strpos($name, 'acetaminofen') !== false) $img = BASE_URL . '/img/productos/acetaminofen.png';
                            elseif (strpos($name, 'loratadina') !== false) $img = BASE_URL . '/img/productos/loratadina.png';
                            elseif (strpos($name, 'salina') !== false) $img = BASE_URL . '/img/productos/solucion_salina.png';
                            elseif (strpos($name, 'amoxicilina') !== false) $img = BASE_URL . '/img/productos/amoxicilina.png';
                            elseif (strpos($name, 'aciclovir') !== false) {
                                if (strpos($name, 'crema') !== false || strpos($name, 'ungüento') !== false) {
                                    $img = 'https://images.unsplash.com/photo-1584017911766-d451b3d0e843?auto=format&fit=crop&q=80&w=400';
                                } else {
                                    $img = 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&q=80&w=400';
                                }
                            }
                            else $img = 'https://img.icons8.com/color/96/pill.png';
                        }
                        // Fecha coloreada — siempre es vencido en esta vista
                        $dateStr = $item['fecha_vencimiento'] ? date('d/m/Y', strtotime($item['fecha_vencimiento'])) : 'N/A';
                        $daysAgo = $item['fecha_vencimiento'] ? floor((time() - strtotime($item['fecha_vencimiento'])) / 86400) : 0;
                    ?>
                    <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1.3fr 0.9fr; border-bottom:1px solid #fef2f2; align-items:center;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='#fff'">
                        <!-- COLUMNA 1: INSUMO -->
                        <div style="padding:10px 12px; display:flex; align-items:center; gap:10px;">
                            <img src="<?= $img ?>" onerror="this.src='https://img.icons8.com/color/96/pill.png'" style="width:48px; height:48px; border-radius:10px; object-fit:cover; flex-shrink:0; border:1px solid #fecaca; box-shadow:0 2px 8px rgba(220,38,38,.1);">
                            <div style="min-width:0; flex:1;">
                                <p style="font-size:11px; font-weight:900; color:#dc2626; text-transform:uppercase; font-style:italic; margin:0 0 3px 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= $item['nombre_generico'] ?></p>
                                <p style="font-size:9px; color:#94a3b8; margin:0 0 5px 0;">Lote vencido — retiro inmediato requerido.</p>
                                <div style="display:flex; gap:5px; flex-wrap:wrap;">
                                    <span style="font-size:8px; font-weight:900; color:#fff; background:#ef4444; padding:2px 6px; border-radius:4px; text-transform:uppercase;"><?= $item['laboratorio'] ?></span>
                                    <span style="font-size:8px; font-weight:900; color:#dc2626; background:#fef2f2; border:1px solid #fecaca; padding:2px 6px; border-radius:4px; text-transform:uppercase;">LOTE: <?= $item['lote'] ?></span>
                                </div>
                            </div>
                        </div>
                        <!-- COLUMNA 2: SEDE -->
                        <div style="padding:10px 12px; text-align:center;">
                            <div style="background:#1e293b; border-radius:8px; padding:6px 10px; display:inline-block;">
                                <p style="font-size:10px; font-weight:900; color:#fca5a5; text-transform:uppercase; font-style:italic; margin:0; letter-spacing:.05em;"><?= $item['sede_nombre'] ?></p>
                            </div>
                        </div>
                        <!-- COLUMNA 3: STOCK -->
                        <div style="padding:10px 12px; text-align:center;">
                            <span style="font-size:20px; font-weight:900; color:#dc2626; font-style:italic;"><?= number_format($item['stock_actual']) ?></span>
                            <p style="font-size:8px; color:#fca5a5; text-transform:uppercase; font-weight:700; margin:2px 0 0 0;">Uds. Vencidas</p>
                        </div>
                        <!-- COLUMNA 4: FECHA VENCIMIENTO -->
                        <div style="padding:10px 12px; text-align:center;">
                            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:6px 10px; display:inline-block; min-width:90px;">
                                <p style="font-size:13px; font-weight:900; color:#dc2626; margin:0; letter-spacing:-.02em;"><?= $dateStr ?></p>
                                <p style="font-size:8px; font-weight:900; color:#ef4444; text-transform:uppercase; margin:2px 0 0 0;">VENCIDO · <?= $daysAgo ?>d atrás</p>
                            </div>
                        </div>
                        <!-- COLUMNA 5: ACCIÓN -->
                        <div style="padding:10px 12px; text-align:center;">
                            <button style="background:#dc2626; color:#fff; border:none; padding:8px 14px; border-radius:10px; font-size:10px; font-weight:900; text-transform:uppercase; cursor:pointer; transition:background .2s;" onmouseover="this.style.background='#991b1b'" onmouseout="this.style.background='#dc2626'">Dar de Baja</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>


                    <!-- Paginación Centrada -->
                    <div class="px-8 py-8 bg-[#111111] border-t border-[#d4af37]/20">
                        <div class="flex flex-col items-center gap-4">
                            <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] italic">
                                Página <?= $current_page_num ?> de <?= $total_pages ?> <span class="text-white/10 mx-2">|</span> <?= $total_items ?> Lotes en Alerta
                            </p>

                            <div class="flex items-center gap-3">
                                <?php if ($current_page_num > 1): ?>
                                    <a href="?p=<?= $current_page_num - 1 ?>&sede=<?= urlencode($filter_sede) ?>&lote=<?= urlencode($filter_lote) ?>" class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[#d4af37] hover:bg-red-600 hover:text-white transition-all text-[10px] font-black uppercase tracking-widest">
                                        ANTERIOR
                                    </a>
                                <?php endif; ?>
                                
                                <div class="flex gap-2">
                                    <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                        <a href="?p=<?= $i ?>&sede=<?= urlencode($filter_sede) ?>&lote=<?= urlencode($filter_lote) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all text-[10px] font-black <?= $i == $current_page_num ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white/5 text-slate-400 border border-white/10 hover:border-red-600 hover:text-red-600' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($current_page_num < $total_pages): ?>
                                    <a href="?p=<?= $current_page_num + 1 ?>&sede=<?= urlencode($filter_sede) ?>&lote=<?= urlencode($filter_lote) ?>" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-white hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-600/10">
                                        SIGUIENTE
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
</body>
</html>

