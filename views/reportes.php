<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
$rol = $_SESSION['rol'];
$isHighCargo = in_array($rol, ['Gerente', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera']);

if (!$isHighCargo) {
    header('Location: inicio.php');
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
    <link rel="stylesheet" href="../assets/css/Inicio.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 fade-in-institutional">
            <header>
                <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter">Central de Inteligencia y <span class="text-[#d4af37]">Reportes</span></h2>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">Monitoreo de Indicadores de Alta Gerencia</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
                    <h3 class="font-black text-[#111111] mb-6 uppercase text-[10px] tracking-widest border-b border-[#d4af37]/20 pb-2">DispensaciÃ³n Mensual (Unidades)</h3>
                    <canvas id="chartMensual"></canvas>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
                    <h3 class="font-black text-[#111111] mb-6 uppercase text-[10px] tracking-widest border-b border-[#d4af37]/20 pb-2">Cumplimiento de Pedidos IPS</h3>
                    <canvas id="chartIPS"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
                <h3 class="font-black text-[#111111] mb-8 uppercase text-[10px] tracking-widest border-l-4 border-l-[#d4af37] pl-4">GeneraciÃ³n de Informes Oficiales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <button class="p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-[#111111] hover:text-white transition-all text-left group">
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">LogÃ­stica Semanal</span>
                        <span class="text-sm font-black uppercase italic group-hover:text-[#d4af37]">Informe Semanal</span>
                    </button>
                    <button class="p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-[#111111] hover:text-white transition-all text-left group">
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">LogÃ­stica Trimestral</span>
                        <span class="text-sm font-black uppercase italic group-hover:text-[#d4af37]">Informe Trimestral</span>
                    </button>
                    <button class="p-6 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-[#111111] hover:text-white transition-all text-left group">
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Cierre Consolidado</span>
                        <span class="text-sm font-black uppercase italic group-hover:text-[#d4af37]">Cierre Anual 2026</span>
                    </button>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Scripts para las grÃ¡ficas con colores corporativos Ã‰lite
        new Chart(document.getElementById('chartMensual'), {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
                datasets: [{ label: 'Entregas', data: [400, 600, 550, 800, 750], backgroundColor: '#111111', borderRadius: 8 }]
            },
            options: { plugins: { legend: { display: false } } }
        });
        new Chart(document.getElementById('chartIPS'), {
            type: 'doughnut',
            data: {
                labels: ['Solita', 'Solano', 'MilÃ¡n', 'Valp.', 'Getucha'],
                datasets: [{ data: [20, 15, 25, 20, 20], backgroundColor: ['#111111', '#d4af37', '#333333', '#c0c0c0', '#b8860b'] }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { font: { weight: '800', size: 9 } } } } }
        });
    </script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
