<?php
$current_page = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? '';
$vencidos_count = $vencidos_count ?? 0;

$isDirectivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera']);
?>
<aside class="w-full md:w-72 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-12">
        <img src="../img/logoesefjl.jpg" alt="Logo" class="w-12 h-12 rounded-[1rem] shadow-md border border-gray-100">
        <div>
            <h1 class="text-medical-500 font-black text-xl leading-tight tracking-tighter italic">SISFARMA</h1>
            <span class="text-[9px] text-gray-400 dark:text-gray-500 font-black tracking-[0.2em] uppercase">ESE FABIO JARAMILLO</span>
        </div>
    </div>

    <nav class="flex-1 space-y-2">
        <p class="text-[10px] font-black text-gray-300 dark:text-slate-600 uppercase tracking-widest pl-4 mb-4">Menú Principal</p>
        
        <a href="dashboard.php" class="flex items-center gap-4 p-4 <?= $current_page == 'dashboard.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group">
            <span class="text-xl group-hover:scale-110 transition-transform">🏠</span>
            <span class="font-bold text-sm tracking-tight italic">Dashboard Central</span>
        </a>

        <?php if ($isDirectivo): ?>
        <a href="sedes.php" class="flex items-center gap-4 p-4 <?= $current_page == 'sedes.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group">
            <span class="text-xl group-hover:scale-110 transition-transform">📍</span>
            <span class="font-bold text-sm tracking-tight italic">Gestionar Sedes</span>
        </a>

        <a href="proveedores.php" class="flex items-center gap-4 p-4 <?= $current_page == 'proveedores.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group">
            <span class="text-xl group-hover:scale-110 transition-transform">🏭</span>
            <span class="font-bold text-sm tracking-tight italic">Proveedores</span>
        </a>
        <?php endif; ?>

        <?php if ($rol == 'IPS (Municipio)'): ?>
        <a href="solicitud_municipio.php" class="flex items-center gap-4 p-4 <?= $current_page == 'solicitud_municipio.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group">
            <span class="text-xl group-hover:scale-110 transition-transform">📦</span>
            <span class="font-bold text-sm tracking-tight italic">Solicitar Suministro</span>
        </a>
        <?php endif; ?>

        <a href="vencidos.php" class="flex items-center gap-4 p-4 <?= $current_page == 'vencidos.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group relative">
            <span class="text-xl group-hover:scale-110 transition-transform">⚠️</span>
            <span class="font-bold text-sm tracking-tight italic">Gestión de Vencidos</span>
            <?php if ($vencidos_count > 0): ?>
                <span class="absolute right-4 top-1/2 -translate-y-1/2 bg-red-500 text-white text-[9px] px-2 py-0.5 rounded-full font-black animate-pulse"><?= $vencidos_count ?></span>
            <?php endif; ?>
        </a>

        <p class="text-[10px] font-black text-gray-300 dark:text-slate-600 uppercase tracking-widest pl-4 mt-8 mb-4">Auditoría</p>
        
        <a href="historial.php" class="flex items-center gap-4 p-4 <?= $current_page == 'historial.php' ? 'bg-medical-50 dark:bg-medical-500/10 text-medical-500 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50' ?> rounded-2xl transition-all group">
            <span class="text-xl group-hover:scale-110 transition-transform">🔍</span>
            <span class="font-bold text-sm tracking-tight italic">Historial Público</span>
        </a>
    </nav>

    <div class="mt-auto pt-8 border-t border-gray-100 dark:border-slate-700/50 space-y-4">
        <button id="theme-toggle" class="w-full flex items-center justify-center gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-slate-900 text-xs font-black text-gray-600 dark:text-gray-300 hover:scale-[1.02] transition-all uppercase tracking-widest border border-gray-100 dark:border-slate-800">
            🌓 Cambiar Tema
        </button>
        <div class="text-center">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">SISFARMA PRO v2.5</p>
        </div>
    </div>
</aside>
