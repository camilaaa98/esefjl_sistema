<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$proveedores = $db->query("SELECT * FROM proveedores")->fetchAll();
$compras = $db->query("
    SELECT c.*, p.razon_social 
    FROM compras c 
    JOIN proveedores p ON c.proveedor_id = p.id 
    ORDER BY fecha_compra DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores Pro - ESE FJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: #020c1b; border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px; background: #0a192f; }
        
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 5px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-action { background: var(--secondary); color: var(--primary); border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; }

        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .data-card { background: var(--primary-light); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .card-title { padding: 12px 20px; background: rgba(100,255,218,0.05); border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--secondary); font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th { text-align: left; padding: 12px 20px; color: var(--text-dim); border-bottom: 1px solid var(--border); }
        td { padding: 10px 20px; color: var(--text-main); border-bottom: 1px solid rgba(100,255,218,0.05); }
        
        .badge-status { padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .bg-paid { background: rgba(100, 255, 218, 0.2); color: var(--secondary); }
        .bg-pending { background: rgba(245, 127, 23, 0.2); color: var(--accent); }
    </style>
</head>
<body>
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight tracking-tighter uppercase">SISFARMA</h1>
                    <span class="text-[8px] text-gray-400 dark:text-gray-500 font-bold tracking-widest uppercase">ESE Fabio Jaramillo</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🏠</span> Inicio
                </a>
                <a href="proveedores.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>🏭</span> Gestión Proveedores
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-700 text-center">
                <button id="theme-toggle" class="w-full flex items-center justify-center gap-2 p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-300">
                    🌓 Cambiar Tema
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Gestión de Proveedores y Cartera</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">Control de abastecimiento institucional y pagos CEDIS</p>
                </div>
                <button class="px-6 py-3 bg-medical-500 hover:bg-medical-600 text-white font-black rounded-2xl shadow-lg shadow-medical-500/20 transition-all uppercase text-xs tracking-widest">
                    + Nuevo Proveedor
                </button>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mt-1">Historial de Compras al por Mayor</span>
                            <span class="text-[9px] font-black text-medical-500 uppercase italic">Control Patrimonial</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-gray-100 dark:border-slate-700">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Razón Social</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                                    <?php foreach ($compras as $c): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 font-mono">
                                            <?= date('d/m/Y', strtotime($c['fecha_compra'])) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-black text-gray-800 dark:text-white"><?= strtoupper($c['razon_social']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-black text-medical-500">
                                            $<?= number_format($c['total'], 0) ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $c['estado_pago'] == 'PAGADO' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                                <?= $c['estado_pago'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar derecha -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Directorio Activo</h3>
                        <div class="space-y-4">
                            <?php foreach ($proveedores as $p): ?>
                                <div class="p-4 bg-gray-50 dark:bg-slate-900 border border-transparent hover:border-medical-200 dark:hover:border-medical-500/30 rounded-2xl transition-all group">
                                    <div class="text-sm font-black text-gray-800 dark:text-white group-hover:text-medical-500 transition-colors uppercase leading-tight"><?= strtoupper($p['razon_social']) ?></div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIT: <?= $p['nit'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
