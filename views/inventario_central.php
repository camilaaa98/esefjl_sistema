<?php
/**
 * inventario_central.php â€” Farmacia ESEFJL Â· ESE Fabio Jaramillo
 * CORREGIDO: columnas reales de la BD, XSS, codificación, filtros, badges.
 */
header('Content-Type: text/html; charset=utf-8');
session_start();
require_once '../core/Database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$db  = Database::getInstance();
$rol = $_SESSION['rol'] ?? '';

/* â”€â”€â”€ Determinar sede según rol â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$sede_id = (in_array($rol, ['Administrador', 'Gerente', 'Regente Farmacia']))
    ? null
    : ($_SESSION['sede_id'] ?? null);

/* â”€â”€â”€ Filtros GET â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$busqueda   = trim($_GET['q']    ?? '');
$filtro_sede = (int)($_GET['sede'] ?? 0);
$filtro_est  = $_GET['estado']   ?? '';

/* â”€â”€â”€ Query corregida con columnas reales de la BD â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$sql = "
    SELECT
        i.id,
        p.nombre_generico,
        p.nombre_comercial,
        p.concentracion_presentacion,
        p.laboratorio,
        p.codigo_invima,
        c.nombre        AS categoria,
        i.stock_actual,
        i.stock_minimo,
        i.lote,
        i.fecha_vencimiento,
        s.nombre        AS sede_nombre,
        s.id            AS sede_id,
        -- % sobre mínimo para el badge de estado
        CASE
            WHEN i.fecha_vencimiento < DATE('now')                         THEN 'VENCIDO'
            WHEN i.fecha_vencimiento < DATE('now','+3 months')             THEN 'POR_VENCER'
            WHEN i.stock_actual <= (i.stock_minimo * 0.25)                 THEN 'CRITICO'
            WHEN i.stock_actual < i.stock_minimo                           THEN 'BAJO'
            ELSE                                                                 'OPTIMO'
        END             AS estado
    FROM inventario i
    JOIN productos p  ON i.producto_id = p.id
    JOIN sedes    s   ON i.sede_id     = s.id
    JOIN categorias c ON p.categoria_id = c.id
";

$params = [];
$where  = [];

if ($sede_id) {
    $where[]  = "i.sede_id = ?";
    $params[] = $sede_id;
}
if ($filtro_sede) {
    $where[]  = "i.sede_id = ?";
    $params[] = $filtro_sede;
}
if ($busqueda !== '') {
    $where[]  = "(p.nombre_generico LIKE ? OR p.nombre_comercial LIKE ? OR i.lote LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY
    CASE estado
        WHEN 'VENCIDO'   THEN 1
        WHEN 'CRITICO'   THEN 2
        WHEN 'POR_VENCER'THEN 3
        WHEN 'BAJO'      THEN 4
        ELSE 5
    END, i.fecha_vencimiento ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$stock = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* â”€â”€â”€ Filtro post-query por estado (badge) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
if ($filtro_est) {
    $stock = array_filter($stock, fn($r) => $r['estado'] === $filtro_est);
}

/* â”€â”€â”€ Totales para tarjetas de resumen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$totalRegistros  = count($stock);
$totalVencidos   = count(array_filter($stock, fn($r) => $r['estado'] === 'VENCIDO'));
$totalCriticos   = count(array_filter($stock, fn($r) => $r['estado'] === 'CRITICO'));
$totalPorVencer  = count(array_filter($stock, fn($r) => $r['estado'] === 'POR_VENCER'));

/* â”€â”€â”€ Sedes para el dropdown de filtro â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$sedes = $db->query("SELECT id, nombre FROM sedes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

/* â”€â”€â”€ Helper badges â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function badge(string $estado): string {
    return match($estado) {
        'VENCIDO'   => '<span class="badge badge-red">Vencido</span>',
        'POR_VENCER'=> '<span class="badge badge-orange">Por vencer</span>',
        'CRITICO'   => '<span class="badge badge-red">Stock crítico</span>',
        'BAJO'      => '<span class="badge badge-yellow">Stock bajo</span>',
        default     => '<span class="badge badge-green">Ã“ptimo</span>',
    };
}
function e(string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventario Central Â· Farmacia ESEFJL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="../assets/css/main.css">
<style>
/* â”€â”€ Badges â”€â”€ */
.badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 9px; font-weight: 800; letter-spacing: .08em;
    text-transform: uppercase; padding: 3px 10px; border-radius: 999px;
}
.badge-green  { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.badge-yellow { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
.badge-orange { background:#ffedd5; color:#9a3412; border:1px solid #fed7aa; }
.badge-red    { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

/* â”€â”€ Barra de progreso stock â”€â”€ */
.stock-bar { height:4px; border-radius:4px; background:#e2e8f0; overflow:hidden; }
.stock-bar-fill { height:100%; border-radius:4px; transition:width .4s; }

/* â”€â”€ Tarjetas de resumen â”€â”€ */
.summary-card {
    background:#fff; border-radius:1.5rem;
    border:1px solid #f1f5f9; padding:1.5rem 1.8rem;
    box-shadow:0 2px 12px rgba(0,0,0,.04);
    display:flex; flex-direction:column; gap:.4rem;
}
.summary-num { font-size:2rem; font-weight:900; line-height:1; }
.summary-lbl { font-size:.6rem; font-weight:800; text-transform:uppercase;
               letter-spacing:.15em; color:#94a3b8; }

/* â”€â”€ Input búsqueda â”€â”€ */
.search-wrap { position:relative; }
.search-wrap input {
    padding:.65rem 1rem .65rem 2.6rem;
    border:1px solid #e2e8f0; border-radius:1rem;
    font-size:.8rem; font-weight:600; width:100%;
    transition:border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    outline:none; border-color:#d4af37;
    box-shadow:0 0 0 3px rgba(212,175,55,.15);
}
.search-wrap svg { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); }

/* â”€â”€ Select filtros â”€â”€ */
select.filtro {
    padding:.6rem 1rem; border:1px solid #e2e8f0; border-radius:.85rem;
    font-size:.75rem; font-weight:600; background:#fff; cursor:pointer;
    transition:border-color .2s;
}
select.filtro:focus { outline:none; border-color:#d4af37; }

/* â”€â”€ Tabla â”€â”€ */
.tbl thead th {
    background:#f8fafc; font-size:.65rem; font-weight:900;
    text-transform:uppercase; letter-spacing:.12em; color:#94a3b8;
    padding:1rem 1.2rem; border-bottom:1px solid #f1f5f9;
}
.tbl tbody td { padding:.95rem 1.2rem; border-top:1px solid #f8fafc; }
.tbl tbody tr:hover td { background:#fafbfc; }

/* â”€â”€ Nombre medicamento â”€â”€ */
.med-name { font-weight:900; color:#111; font-size:.85rem; font-style:italic;
            text-transform:uppercase; line-height:1.2; }
.med-sub  { font-size:.6rem; color:#94a3b8; font-weight:700;
            text-transform:uppercase; letter-spacing:.08em; margin-top:2px; }

/* â”€â”€ Lote â”€â”€ */
code.lote {
    font-size:.65rem; font-family:monospace; background:#111; color:#d4af37;
    padding:3px 9px; border-radius:.5rem; border:1px solid rgba(212,175,55,.2);
}

/* â”€â”€ Alerta vacía â”€â”€ */
.empty-state {
    text-align:center; padding:4rem 2rem; color:#94a3b8;
    font-size:.8rem; font-weight:600;
}
</style>
</head>

<body class="bg-slate-50 flex overflow-hidden">
<div class="main-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content-area fade-in-institutional" style="overflow-y:auto;">

        <!-- â”€â”€ Cabecera â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <header class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-[#111] tracking-tighter italic uppercase">
                    Inventario <span class="text-[#d4af37]">Central</span>
                </h2>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[.3em] mt-1">
                    Stock maestro Â· Red Hospitalaria ESEFJL
                </p>
            </div>
            <div class="flex gap-2">
                <a href="?<?= e(http_build_query(array_merge($_GET, ['export'=>'csv']))) ?>"
                   class="btn-institutional text-[10px]">
                    â¬‡ Exportar CSV
                </a>
            </div>
        </header>

        <!-- â”€â”€ Tarjetas resumen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="summary-card">
                <span class="summary-num text-[#111]"><?= $totalRegistros ?></span>
                <span class="summary-lbl">Registros totales</span>
            </div>
            <div class="summary-card">
                <span class="summary-num text-red-600"><?= $totalVencidos ?></span>
                <span class="summary-lbl">Vencidos</span>
            </div>
            <div class="summary-card">
                <span class="summary-num text-orange-500"><?= $totalPorVencer ?></span>
                <span class="summary-lbl">Por vencer (90 días)</span>
            </div>
            <div class="summary-card">
                <span class="summary-num text-amber-600"><?= $totalCriticos ?></span>
                <span class="summary-lbl">Stock crítico</span>
            </div>
        </div>

        <!-- â”€â”€ Filtros â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <form method="GET" class="flex flex-wrap gap-3 mb-6">
            <div class="search-wrap flex-1 min-w-[200px]">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="q" placeholder="Buscar por nombre, loteâ€¦"
                       value="<?= e($busqueda) ?>">
            </div>

            <?php if (!$sede_id): /* Solo admin/gerente ve el filtro de sedes */ ?>
            <select name="sede" class="filtro">
                <option value="0">Todas las sedes</option>
                <?php foreach ($sedes as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($filtro_sede == $s['id']) ? 'selected' : '' ?>>
                    <?= e($s['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>

            <select name="estado" class="filtro">
                <option value="">Todos los estados</option>
                <option value="OPTIMO"    <?= $filtro_est==='OPTIMO'    ?'selected':'' ?>>Ã“ptimo</option>
                <option value="BAJO"      <?= $filtro_est==='BAJO'      ?'selected':'' ?>>Stock bajo</option>
                <option value="CRITICO"   <?= $filtro_est==='CRITICO'   ?'selected':'' ?>>Stock crítico</option>
                <option value="POR_VENCER"<?= $filtro_est==='POR_VENCER'?'selected':'' ?>>Por vencer</option>
                <option value="VENCIDO"   <?= $filtro_est==='VENCIDO'   ?'selected':'' ?>>Vencido</option>
            </select>

            <button type="submit" class="btn-institutional">Filtrar</button>
            <a href="inventario_central.php" class="btn-institutional"
               style="background:#f8fafc;color:#64748b;border-color:#e2e8f0;">âœ• Limpiar</a>
        </form>

        <!-- â”€â”€ Tabla â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="bg-white rounded-[1.8rem] border border-slate-100 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full tbl">
                    <thead>
                        <tr>
                            <th>Medicamento / Insumo</th>
                            <th>Categoría</th>
                            <th>Sede</th>
                            <th class="text-center">Stock actual</th>
                            <th class="text-center">Mínimo</th>
                            <th>Vencimiento</th>
                            <th>Lote</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($stock)): ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                ðŸ” No se encontraron registros con los filtros aplicados.
                            </div>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($stock as $item):
                            $pct = ($item['stock_minimo'] > 0)
                                ? min(100, round($item['stock_actual'] / $item['stock_minimo'] * 100))
                                : 100;
                            $barColor = match($item['estado']) {
                                'VENCIDO','CRITICO' => '#ef4444',
                                'BAJO'              => '#f59e0b',
                                'POR_VENCER'        => '#f97316',
                                default             => '#22c55e',
                            };
                            $rowBg = in_array($item['estado'], ['VENCIDO','CRITICO'])
                                ? 'background:#fff5f5;' : '';
                        ?>
                        <tr style="<?= $rowBg ?>">
                            <td>
                                <div class="med-name"><?= e($item['nombre_generico']) ?></div>
                                <div class="med-sub">
                                    <?= e($item['nombre_comercial'] ?? '') ?>
                                    <?= $item['concentracion_presentacion']
                                        ? 'Â· ' . e($item['concentracion_presentacion']) : '' ?>
                                </div>
                                <div class="med-sub" style="color:#cbd5e1;">
                                    Lab: <?= e($item['laboratorio'] ?? 'â€”') ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:.7rem;font-weight:700;color:#64748b;">
                                    <?= e($item['categoria']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size:.7rem;font-weight:800;color:#475569;
                                             text-transform:uppercase;font-style:italic;">
                                    <?= e($item['sede_nombre']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div style="font-size:1.15rem;font-weight:900;
                                            color:<?= $pct<25 ? '#dc2626' : ($pct<100 ? '#b45309' : '#111') ?>;">
                                    <?= (int)$item['stock_actual'] ?>
                                </div>
                                <!-- barra de progreso respecto al mínimo -->
                                <div class="stock-bar" style="width:60px;margin:4px auto 0;">
                                    <div class="stock-bar-fill"
                                         style="width:<?= $pct ?>%;background:<?= $barColor ?>;"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span style="font-size:.75rem;font-weight:700;color:#94a3b8;">
                                    <?= (int)$item['stock_minimo'] ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $fv = $item['fecha_vencimiento'];
                                    $fvFmt = $fv ? date('d/M/Y', strtotime($fv)) : 'â€”';
                                    $fvColor = in_array($item['estado'], ['VENCIDO','POR_VENCER'])
                                        ? 'color:#dc2626;font-weight:900;'
                                        : 'color:#475569;font-weight:700;';
                                ?>
                                <span style="font-size:.75rem;<?= $fvColor ?>">
                                    <?= e($fvFmt) ?>
                                </span>
                            </td>
                            <td>
                                <code class="lote"><?= e($item['lote'] ?? 'â€”') ?></code>
                            </td>
                            <td class="text-center">
                                <?= badge($item['estado']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer de tabla -->
            <div class="px-6 py-4 border-t border-slate-50 flex justify-between items-center"
                 style="background:#fafbfc;">
                <span style="font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;
                             letter-spacing:.1em;">
                    <?= $totalRegistros ?> registro<?= $totalRegistros !== 1 ? 's' : '' ?> encontrado<?= $totalRegistros !== 1 ? 's' : '' ?>
                </span>
                <span style="font-size:.6rem;color:#cbd5e1;font-weight:600;">
                    Farmacia ESEFJL Â· ESE Fabio Jaramillo Londoño
                </span>
            </div>
        </section>

    </main>
</div>

<?php
/* â”€â”€â”€ Exportar CSV â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Re-ejecutar sin filtro de export para limpiar la URL
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="inventario_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    fputcsv($out, ['Medicamento','Presentacion','Laboratorio','Sede','Stock Actual',
                   'Stock Mínimo','Lote','Vencimiento','Estado']);
    foreach ($stock as $r) {
        fputcsv($out, [
            $r['nombre_generico'],
            $r['concentracion_presentacion'],
            $r['laboratorio'],
            $r['sede_nombre'],
            $r['stock_actual'],
            $r['stock_minimo'],
            $r['lote'],
            $r['fecha_vencimiento'],
            $r['estado'],
        ]);
    }
    fclose($out);
    exit;
}
?>

<script src="../assets/js/animations.js" defer></script>
</body>
</html>
