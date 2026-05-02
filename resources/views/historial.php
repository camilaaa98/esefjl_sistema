<?php
/**
 * Auditoría de Movimientos - ESE Fabio Jaramillo
 * Versión 9.8: Paginación Inteligente (Ventana de 3) Inferior.
 */
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
$root_path = dirname(__DIR__, 2);
require_once $root_path . '/app/config/Database.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$sede_id_session = $_SESSION['sede_id'];
$is_directivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Administrador', 'Subgerente de Servicios de Salud']);

$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;

$filtro_sede = isset($_GET['sede']) ? $_GET['sede'] : null;

$query_base = "
    FROM entregas e
    JOIN productos p ON e.producto_id = p.id
    JOIN pacientes pac ON e.paciente_id = pac.documento
    JOIN sedes s ON e.sede_id = s.id
";

if (!$is_directivo) {
    $where = "WHERE e.sede_id = ?";
    $params = [$sede_id_session];
} else {
    $where = $filtro_sede ? "WHERE e.sede_id = ?" : "";
    $params = $filtro_sede ? [$filtro_sede] : [];
}

$stmtCount = $db->prepare("SELECT COUNT(*) $query_base $where");
$stmtCount->execute($params);
$total_items = $stmtCount->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Lógica de Ventana de 3 Páginas
$start_page = max(1, $current_page_num - 1);
$end_page = min($total_pages, $start_page + 2);
if ($end_page - $start_page < 2) $start_page = max(1, $end_page - 2);

$stmt = $db->prepare("
    SELECT e.*, p.nombre_generico, p.imagen_url, p.laboratorio, pac.nombres AS paciente_nom, pac.apellidos AS paciente_ape, s.nombre as sede_nombre
    $query_base
    $where
    ORDER BY e.fecha_entrega DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);

$movimientos = $stmt->fetchAll();
$sedes = $is_directivo ? $db->query("SELECT * FROM sedes ORDER BY nombre ASC")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trazabilidad Histórica - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../../resources/views/partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="mb-12 fade-in-institutional text-center">
                <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-4 border border-[#d4af37]/30">Log de Auditoría Farmacéutica</span>
                <h2 class="text-4xl font-black text-[#111111] tracking-tighter uppercase italic leading-none">Trazabilidad <span class="text-[#d4af37]">Histórica</span></h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-[0.2em] mt-3">Monitoreo de movimientos en la Red Hospitalaria ESEFJL</p>
            </header>

            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden fade-in-institutional">
                <?php if ($is_directivo): ?>
                <div class="p-6 bg-slate-50/50 border-b border-slate-100">
                    <form method="GET" class="flex justify-center">
                        <select name="sede" onchange="this.form.submit()" class="p-4 bg-[#111111] text-[#d4af37] text-[10px] font-black rounded-2xl outline-none uppercase tracking-widest cursor-pointer italic border border-[#d4af37]/20">
                            <option value="">--- TODAS LAS SEDES (AUDITORíA GLOBAL) ---</option>
                            <?php foreach ($sedes as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($filtro_sede == $s['id']) ? 'selected' : '' ?>><?= strtoupper($s['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <?php endif; ?>

                <div class="overflow-x-auto">
                    <table class="table-clinical w-full">
                        <thead>
                            <tr class="bg-[#1a202c] border-b-2 border-[#d4af37]">
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Folio</th>
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Cronología</th>
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Insumo Dispensado</th>
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Cant.</th>
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Paciente</th>
                                <th class="px-8 py-6 text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em] text-center">Sede</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($movimientos)): ?>
                                <tr><td colspan="6" class="px-8 py-24 text-center text-slate-300 italic uppercase font-black text-xs tracking-widest opacity-30">Sin registros en el periodo actual.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($movimientos as $m): ?>
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-6 text-center text-[10px] font-black text-slate-300 font-mono italic">#<?= str_pad($m['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-[10px] font-black text-slate-600 uppercase leading-none mb-1"><?= date('d/m/Y', strtotime($m['fecha_entrega'])) ?></p>
                                    <p class="text-[9px] font-bold text-slate-400"><?= date('H:i', strtotime($m['fecha_entrega'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5 text-left justify-center">
                                        <?php 
                                            $img = isset($m['imagen_url']) && $m['imagen_url'] ? $m['imagen_url'] : 'https://img.icons8.com/color/96/pill.png';
                                        ?>
                                        <div class="w-16 h-16 bg-white rounded-2xl flex-shrink-0 flex items-center justify-center border border-slate-100 shadow-sm overflow-hidden group">
                                            <img src="<?= $img ?>" class="w-12 h-12 object-contain group-hover:scale-110 transition-transform" alt="Medicamento">
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#111111] uppercase italic leading-none mb-1"><?= $m['nombre_generico'] ?></p>
                                            <span class="text-[8px] font-bold text-[#d4af37] uppercase italic"><?= $m['laboratorio'] ?> | LOTE: <?= $m['lote'] ?? 'N/A' ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center text-sm font-black text-[#111111] tabular-nums"><?= $m['cantidad'] ?></td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-[11px] font-black text-slate-700 uppercase italic leading-none mb-1"><?= $m['paciente_nom'] . ' ' . $m['paciente_ape'] ?></p>
                                    <p class="text-[9px] font-bold text-slate-400">CC: <?= $m['paciente_id'] ?></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1 bg-black text-[#d4af37] text-[8px] font-black rounded-lg uppercase italic"><?= $m['sede_nombre'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación Inteligente Inferior -->
                <div class="px-10 py-8 bg-[#111111] border-t border-[#d4af37]/20">
                    <div class="flex flex-col items-center gap-4">
                        <p class="text-[10px] font-black text-[#d4af37] uppercase tracking-[0.3em] italic">
                            Auditoría página <?= $current_page_num ?> de <?= $total_pages ?>
                        </p>

                        <div class="flex items-center gap-3">
                            <?php if ($current_page_num > 1): ?>
                                <a href="?p=<?= $current_page_num - 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[#d4af37] hover:bg-[#d4af37] hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest">
                                    ANTERIOR
                                </a>
                            <?php endif; ?>
                            
                            <div class="flex gap-2">
                                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                    <a href="?p=<?= $i ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all text-[10px] font-black <?= $i == $current_page_num ? 'bg-[#d4af37] text-[#111111] shadow-lg shadow-[#d4af37]/20' : 'bg-white/5 text-slate-400 border border-white/10 hover:border-[#d4af37] hover:text-[#d4af37]' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($current_page_num < $total_pages): ?>
                                <a href="?p=<?= $current_page_num + 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-6 py-3 bg-[#d4af37] text-[#111111] rounded-xl hover:bg-white hover:text-[#111111] transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-[#d4af37]/10">
                                    SIGUIENTE
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
</body>
</html>

