<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/Database.php';

// Manejo del Simulador de Riesgos
if (isset($_GET['toggleContingency'])) {
    $_SESSION['modo_contingencia'] = !($_SESSION['modo_contingencia'] ?? false);
    header('Location: dashboard.php');
    exit();
}

$sede_id = $_SESSION['sede_id'];
$rol = $_SESSION['rol'];
$db = Database::getInstance();

$inventory = InventoryController::getInventoryBySede($sede_id);

$stockCritico = 0; $vencidos = 0; $today = date('Y-m-d');
$warning_date = date('Y-m-d', strtotime('+3 months'));

foreach ($inventory as $item) {
    if ($item['stock_actual'] <= ($item['stock_minimo'] * 0.25)) $stockCritico++;
    if ($item['fecha_vencimiento'] && $item['fecha_vencimiento'] < $today) $vencidos++;
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisfarma Pro - ESE Fabio Jaramillo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .dark { background-color: #0f172a; color: white; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Overlay de Contingencia -->
    <?php if (isset($_SESSION['modo_contingencia']) && $_SESSION['modo_contingencia']): ?>
        <div class="bg-red-600 text-white text-center py-2 text-sm font-bold animate-pulse uppercase tracking-widest">
            ⚠️ Modo de Contingencia Activo - Sincronización Local Habilitada
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight">SISFARMA</h1>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold tracking-widest uppercase">ESE Fabio Jaramillo</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>📊</span> Resumen Operativo
                </a>
                <?php if ($rol === 'Administrador'): ?>
                    <a href="admin_usuarios.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                        <span>👥</span> Gestión IPS
                    </a>
                    <a href="aprobacion_pedidos.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                        <span>📦</span> Despacho CEDIS
                    </a>
                <?php endif; ?>
                <a href="solicitud_municipio.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🚚</span> Pedido de Insumos
                </a>
                <a href="registro_entrega.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>💊</span> Entregas Pacientes
                </a>
                <a href="registro_paciente.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🏥</span> Vincular Paciente
                </a>
                <a href="historial.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🔍</span> Auditoría Real
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-medical-500 flex items-center justify-center text-white font-bold">
                        <?php echo substr($_SESSION['nombre'], 0, 1); ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-white"><?php echo $_SESSION['nombre']; ?></p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase"><?php echo $rol; ?> | <?php echo $_SESSION['sede']; ?></p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <button id="theme-toggle" class="w-full flex items-center justify-center gap-2 p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-300 transition-all hover:bg-gray-200 dark:hover:bg-slate-600">
                        <span class="dark:hidden">🌙 Modo Oscuro</span>
                        <span class="hidden dark:block">☀️ Modo Claro</span>
                    </button>
                    <a href="../core/logout.php" class="text-center p-2 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
                        ⏻ CERRAR SESIÓN
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Consola de Control Central</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">E.S.E Fabio Jaramillo Londoño — Operación Nacional</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="?toggleContingency=1" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-orange-500/20 transition-all">
                        ⚡ SIMULAR FALLA
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                        🖨️ REPORTE
                    </button>
                    <a href="presentacion.php" class="px-4 py-2 bg-medical-500 hover:bg-medical-600 text-white text-xs font-extrabold rounded-xl shadow-lg shadow-medical-500/20 transition-all animate-pulse">
                        🚀 PRESENTACIÓN
                    </a>
                </div>
            </header>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-transform hover:scale-[1.02]">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Medicamentos</p>
                    <p class="text-3xl font-black text-gray-800 dark:text-white"><?php echo count($inventory); ?></p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-transform hover:scale-[1.02]">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Vencidos</p>
                    <p class="text-3xl font-black text-red-500"><?php echo $vencidos; ?></p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-transform hover:scale-[1.02]">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Stock Crítico</p>
                    <p class="text-3xl font-black text-orange-500"><?php echo $stockCritico; ?></p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-transform hover:scale-[1.02]">
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">IPS Activas</p>
                    <p class="text-3xl font-black text-medical-500">5</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic">Semaforización Preventiva</h3>
                    <div class="text-[10px] bg-red-50 dark:bg-red-500/10 text-red-500 font-bold px-3 py-1 rounded-full">
                        AUDITORÍA: < 90 DÍAS
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-slate-800/50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Medicamento</th>
                                <th class="px-8 py-4">Lote</th>
                                <th class="px-8 py-4">Vencimiento</th>
                                <th class="px-8 py-4">Stock</th>
                                <th class="px-8 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php foreach ($inventory as $i): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-8 py-4">
                                    <p class="font-bold text-gray-800 dark:text-gray-200"><?php echo strtoupper($i['nombre_generico']); ?></p>
                                    <p class="text-[10px] text-gray-400 italic">ID: #<?php echo str_pad($i['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 text-[10px] font-mono font-bold rounded">
                                        <?php echo $i['lote'] ?? 'N/A'; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo $i['fecha_vencimiento'] ? date('d/m/Y', strtotime($i['fecha_vencimiento'])) : 'N/A'; ?>
                                    </p>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-gray-800 dark:text-white"><?php echo $i['stock_actual']; ?></span>
                                        <span class="text-[10px] text-gray-400 font-bold">UND</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-xs font-bold">
                                    <?php $status = InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']); 
                                          if (strpos($status, 'VENCIDO') !== false || strpos($status, 'CRíTICO') !== false): ?>
                                        <span class="text-red-500 bg-red-50 dark:bg-red-500/10 px-3 py-1 rounded-lg border border-red-100 dark:border-red-500/20">RETIRAR</span>
                                    <?php else: ?>
                                        <span class="text-medical-500 bg-medical-50 dark:bg-medical-500/10 px-3 py-1 rounded-lg border border-medical-100 dark:border-medical-500/20">ÓPTIMO</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
