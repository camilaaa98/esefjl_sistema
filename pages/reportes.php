<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
$rol = $_SESSION['rol'];
$isHighCargo = in_array($rol, ['Gerente', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera']);

if (!$isHighCargo) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Institucionales - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Reutilizada -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg">
                <h1 class="text-medical-500 font-extrabold text-lg">SISFARMA</h1>
            </div>
            <nav class="space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 rounded-xl transition-all">🏠 Inicio</a>
                <a href="reportes.php" class="flex items-center gap-3 p-3 bg-medical-50 text-medical-500 font-bold rounded-xl">📊 Reportes</a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8">
            <header>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white italic uppercase tracking-tighter">Central de Inteligencia y Reportes</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Informes Consolidados para Secretaría de Salud y Entes de Control</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 uppercase text-xs tracking-widest">Dispensación Mensual (Unidades)</h3>
                    <canvas id="chartMensual"></canvas>
                </div>
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 uppercase text-xs tracking-widest">Cumplimiento de Pedidos IPS</h3>
                    <canvas id="chartIPS"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-medical-500 mb-6 uppercase text-xs tracking-widest">Generación de Informes Oficiales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <button class="p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-medical-50 hover:border-medical-200 transition-all text-left">
                        <span class="block text-xs font-black text-slate-400 uppercase">Período</span>
                        <span class="text-sm font-bold text-slate-700">Informe Semanal</span>
                    </button>
                    <button class="p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-medical-50 hover:border-medical-200 transition-all text-left">
                        <span class="block text-xs font-black text-slate-400 uppercase">Período</span>
                        <span class="text-sm font-bold text-slate-700">Informe Trimestral</span>
                    </button>
                    <button class="p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-medical-50 hover:border-medical-200 transition-all text-left">
                        <span class="block text-xs font-black text-slate-400 uppercase">Período</span>
                        <span class="text-sm font-bold text-slate-700">Cierre Anual 2026</span>
                    </button>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Placeholder scripts para las gráficas
        new Chart(document.getElementById('chartMensual'), {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
                datasets: [{ label: 'Entregas', data: [400, 600, 550, 800, 750], backgroundColor: '#006D5B' }]
            }
        });
        new Chart(document.getElementById('chartIPS'), {
            type: 'pie',
            data: {
                labels: ['Solita', 'Solano', 'Milán', 'Valparaíso', 'Getucha'],
                datasets: [{ data: [20, 15, 25, 20, 20], backgroundColor: ['#0f172a', '#006D5B', '#14b8a6', '#0ea5e9', '#6366f1'] }]
            }
        });
    </script>
</body>
</html>
