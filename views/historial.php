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
    <title>Auditoría de Movimientos - SISFARMA PRO</title>
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
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Trazabilidad Histórica</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Log de Auditoría para Entes de Control y Farmacovigilancia</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                        🖨️ Exportar Acta
                    </button>
                </div>
            </header>

            <!-- Barra de Herramientas -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="🔎 Buscar por paciente, insumo o sede..." 
                        class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white">
                </div>
                <?php if ($rol === 'Administrador'): ?>
                <div class="w-full md:w-64">
                    <form method="GET">
                        <select name="sede" onchange="this.form.submit()" 
                            class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-bold text-medical-500 uppercase">
                            <option value="">— Todas las IPS —</option>
                            <?php foreach ($sedes as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($filtro_sede == $s['id']) ? 'selected' : '' ?>><?= $s['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tabla de Auditoría -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-bottom border-gray-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center bg-slate-50 dark:bg-slate-900/50 gap-4">
                    <div class="text-center md:text-left">
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest block">Registros Totales: <?= $total_items ?></span>
                        <span class="text-[9px] font-bold text-medical-500 uppercase italic">Página <?= $current_page_num ?> de <?= $total_pages ?></span>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($current_page_num > 1): ?>
                            <a href="?p=<?= $current_page_num - 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase hover:bg-gray-50 transition-all shadow-sm italic">Anterior</a>
                        <?php endif; ?>
                        <?php if ($current_page_num < $total_pages): ?>
                            <a href="?p=<?= $current_page_num + 1 ?><?= $filtro_sede ? "&sede=$filtro_sede" : "" ?>" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase hover:scale-105 transition-all shadow-lg italic">Siguiente</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="tabla">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-gray-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">Item</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha / Hora</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Insumo / Medicamento</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Cant.</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Beneficiario</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php if (empty($movimientos)): ?>
                                <tr><td colspan="6" class="px-6 py-20 text-center text-gray-400 italic">No se registran movimientos para este ciclo operativo.</td></tr>
                            <?php endif; ?>
                            <?php 
                            $counter = $offset + 1;
                            foreach ($movimientos as $m): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all">
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-gray-300 font-mono"><?= str_pad($counter++, 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="px-6 py-4 text-[10px] font-bold text-gray-500 dark:text-gray-400"><?= date('d/m/Y H:i', strtotime($m['fecha_entrega'])) ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-gray-800 dark:text-white"><?= strtoupper($m['nombre_generico']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm font-black text-medical-500 tabular-nums"><?= $m['cantidad'] ?></td>
                                <td class="px-6 py-4 text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase"><?= strtoupper($m['paciente']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-green-50 text-green-700 text-[9px] font-black rounded-full uppercase border border-green-100">Auditado ✓</span>
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
</body>
</html>
