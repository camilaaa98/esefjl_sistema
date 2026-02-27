<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/AlertController.php';
require_once __DIR__ . '/../core/Database.php';

$sede_id = $_SESSION['sede_id'];
$rol = $_SESSION['rol'];
$db = Database::getInstance();

$inventory = InventoryController::getInventoryBySede($sede_id);
$alerts = AlertController::getInactivityAlerts();

// Simulación de datos para gráficas (En el futuro vendrán de DB real de histórico)
$data_labels = ["Ene", "Feb", "Mar", "Abr", "May", "Jun"];
$data_entregas = [120, 450, 300, 700, 850, 1000];
$data_pedidos = [5, 12, 8, 15, 20, 25];

$isHighCargo = in_array($rol, ['Gerente', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera']);
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisfarma Pro - Dashboard Gerencial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .alert-pulse { animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight tracking-tighter">SISFARMA</h1>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold tracking-widest uppercase">Gerencia Corporativa</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>🏠</span> Inicio
                </a>
                <?php if ($isHighCargo): ?>
                    <a href="reportes.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                        <span>📊</span> Reportes Mensuales
                    </a>
                    <a href="asignacion_personal.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                        <span>🤝</span> Gestión de Talento
                    </a>
                <?php endif; ?>
                <a href="historial.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🔍</span> Auditoría Pública
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-700 text-center">
                <button id="theme-toggle" class="w-full flex items-center justify-center gap-2 p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-300">
                    🌓 Cambiar Tema
                </button>
                <p class="text-[9px] text-gray-400 mt-4 uppercase font-bold tracking-widest">ESE Fabio Jaramillo</p>
            </div>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic"><?php echo strtoupper($rol); ?></h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Panel de Monitoreo Directivo — Bogotá / Florencia</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Sincronización Cloud</span>
                    <a href="../core/logout.php" class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">Desconectar</a>
                </div>
            </header>

            <!-- Alertas Críticas (Solo si existen) -->
            <?php if (!empty($alerts)): ?>
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Alertas de Inactividad IPS</h3>
                <?php foreach($alerts as $alert): 
                    $color = ($alert['nivel'] == 3) ? 'red' : (($alert['nivel'] == 2) ? 'orange' : 'yellow');
                ?>
                <div class="flex items-center justify-between p-4 bg-<?php echo $color; ?>-50 border-l-4 border-<?php echo $color; ?>-500 rounded-r-2xl <?php echo ($alert['nivel'] >= 2) ? 'alert-pulse' : ''; ?>">
                    <div class="flex items-center gap-4">
                        <span class="text-2xl"><?php echo ($alert['nivel'] == 3) ? '🚨' : '⚠️'; ?></span>
                        <div>
                            <p class="text-xs font-black text-<?php echo $color; ?>-700 uppercase tracking-tighter"><?php echo $alert['mensaje']; ?></p>
                            <p class="text-[10px] text-<?php echo $color; ?>-600/70 font-bold italic">Sede: <?php echo $alert['sede_nombre']; ?> | Días transcurridos: <?php echo $alert['dias']; ?></p>
                        </div>
                    </div>
                    <?php if ($rol == 'Gerente' || $rol == 'Subgerente de Servicios de Salud'): ?>
                        <button class="px-4 py-1.5 bg-<?php echo $color; ?>-600 text-white text-[10px] font-black rounded-lg hover:shadow-lg transition-all">ACCIONAR REPORTE</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Gráficas Gerenciales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic">Tendencia de Dispensación Regional</h3>
                        <span class="text-[10px] font-bold text-gray-400 tracking-widest">HISTÓRICO 6 MESES</span>
                    </div>
                    <canvas id="chartEntregas" height="120"></canvas>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter italic mb-8">Abastecimiento IPS</h3>
                    <canvas id="chartPedidos" height="240"></canvas>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl shadow-slate-900/20">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pacientes Cargados</p>
                    <p class="text-4xl font-black tabular-nums">1.000</p>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Fuerza Laboral</p>
                    <p class="text-4xl font-black text-medical-500 tabular-nums">500</p>
                </div>
                <!-- ... más stats -->
            </div>
        </main>
    </div>

    <script>
        // Config Charts
        const ctxEntregas = document.getElementById('chartEntregas').getContext('2d');
        new Chart(ctxEntregas, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($data_labels); ?>,
                datasets: [{
                    label: 'Medicamentos Entregados',
                    data: <?php echo json_encode($data_entregas); ?>,
                    borderColor: '#006D5B',
                    backgroundColor: 'rgba(0, 109, 91, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } } }
        });

        const ctxPedidos = document.getElementById('chartPedidos').getContext('2d');
        new Chart(ctxPedidos, {
            type: 'doughnut',
            data: {
                labels: ['Florencia', 'Solita', 'Solano', 'Milán', 'Getucha'],
                datasets: [{
                    data: [40, 15, 10, 20, 15],
                    backgroundColor: ['#0f172a', '#006D5B', '#14b8a6', '#0ea5e9', '#6366f1'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '80%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } } } }
        });
    </script>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>

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
