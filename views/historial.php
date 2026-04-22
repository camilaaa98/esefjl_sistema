<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$sede_id = $_SESSION['sede_id'];

$current_page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 10;
$offset = ($current_page_num - 1) * $limit;

// Filtro por sede (solo admin puede ver todas)
$filtro_sede = isset($_GET['sede']) ? $_GET['sede'] : null;

$query_base = "
    FROM entregas e
    JOIN productos p ON e.producto_id = p.id
    JOIN pacientes pac ON e.paciente_id = pac.documento
    JOIN sedes s ON e.sede_id = s.id
";

$where = ($rol === 'Administrador' && $filtro_sede) ? "WHERE e.sede_id = ?" : (($rol !== 'Administrador') ? "WHERE e.sede_id = ?" : "");
$params = ($rol === 'Administrador' && $filtro_sede) ? [$filtro_sede] : (($rol !== 'Administrador') ? [$sede_id] : []);

$stmtCount = $db->prepare("SELECT COUNT(*) $query_base $where");
$stmtCount->execute($params);
$total_items = $stmtCount->fetchColumn();

$stmt = $db->prepare("
    SELECT e.*, p.nombre_generico, pac.nombres AS paciente, s.nombre as sede
    $query_base
    $where
    ORDER BY e.fecha_entrega DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);

$movimientos = $stmt->fetchAll();
$total_pages = ceil($total_items / $limit);
$sedes = $db->query("SELECT * FROM sedes")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuditorÃ­a de Movimientos - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 fade-in-institutional">
                <div>
                    <h2 class="text-3xl font-black text-[#111111] tracking-tight italic uppercase">Trazabilidad <span class="text-[#d4af37]">HistÃ³rica</span></h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">Log de AuditorÃ­a para Farmacovigilancia Ã‰lite</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="px-5 py-2.5 bg-[#111111] text-white text-[10px] font-black rounded-xl shadow-lg hover:bg-black hover:text-[#d4af37] transition-all border border-transparent hover:border-[#d4af37]/30 uppercase tracking-widest">
                        ðŸ–¨ï¸ Exportar Acta de AuditorÃ­a
                    </button>
                </div>
            </header>

            <!-- Barra de Herramientas Premium -->
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-100 flex flex-col md:flex-row gap-4 fade-in-institutional" style="animation-delay: 0.1s">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="ðŸ”Ž Buscar por paciente, insumo o sede..." 
                        class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-medium text-slate-700">
                </div>
                <?php if ($rol === 'Administrador'): ?>
                <div class="w-full md:w-64">
                    <form method="GET">
                        <select name="sede" onchange="this.form.submit()" 
                            class="w-full p-4 bg-[#111111] border border-[#d4af37]/20 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-bold text-[#d4af37] uppercase tracking-widest cursor-pointer">
                            <option value="" class="bg-white text-black underline">--- TODAS LAS SEDES ---</option>
                            <?php foreach ($sedes as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($filtro_sede == $s['id']) ? 'selected' : '' ?>><?= $s['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tabla de AuditorÃ­a Elite -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden fade-in-institutional" style="animation-delay: 0.2s">
                <div class="px-8 py-6 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center bg-[#111111] gap-4">
                    <div class="text-center md:text-left">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Registros de Control Operativo</span>
                        <span class="text-xs font-black text-[#d4af37] uppercase italic">PÃ¡gina <?= $current_page_num ?> de <?= $total_pages ?> â€” SISFARMA Ã‰LITE v7.5</span>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?p=<?= $current_page_num - 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-4 py-2 bg-white/10 border border-white/10 rounded-xl text-[10px] font-black uppercase text-white hover:bg-white/20 transition-all italic">â† Anterior</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_pages): ?>
                            <a href="?p=<?= $current_page_num + 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-4 py-2 bg-[#d4af37] text-white rounded-xl text-[10px] font-black uppercase hover:scale-105 transition-all shadow-lg italic">Siguiente â†’</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="tabla">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">Item</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha / Hora</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Insumo / Medicamento</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Cant.</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Beneficiario</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($movimientos)): ?>
                                <tr><td colspan="6" class="px-8 py-24 text-center text-slate-300 italic uppercase font-black text-xs tracking-widest opacity-30">No se registran movimientos en este ciclo operativo.</td></tr>
                            <?php endif; ?>
                            <?php 
                            $counter = $offset + 1;
                            foreach ($movimientos as $m): ?>
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-5 text-center">
                                    <span class="text-[10px] font-black text-slate-300 font-mono"><?= str_pad($counter++, 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-8 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-tighter"><?= date('d/m/Y <br> H:i:s', strtotime($m['fecha_entrega'])) ?></td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-black text-[#111111] uppercase italic border-b border-[#d4af37] leading-none"><?= strtoupper($m['nombre_generico']) ?></span>
                                    <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">SEDE: <?= $m['sede'] ?></div>
                                </td>
                                <td class="px-8 py-5 text-center text-sm font-black text-[#111111] tabular-nums"><?= $m['cantidad'] ?></td>
                                <td class="px-8 py-5 text-[10px] font-black text-slate-600 uppercase italic leading-tight"><?= strtoupper($m['paciente']) ?></td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 bg-slate-900 text-white text-[8px] font-black rounded-full uppercase border border-black shadow-sm tracking-[0.1em]">Auditado âœ“</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', (e) => {
            const q = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#tabla tbody tr:not(:first-child)');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
    <script src="../assets/js/theme-toggle.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
