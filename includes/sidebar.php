<?php
$current_page = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? '';
$isDirectivo = in_array($rol, [
    'Gerente',
    'Regente Farmacia',
    'Subgerente de Servicios de Salud',
    'Subgerente Administrativa y Financiera',
    'Administrador'
]);
?>
<aside class="sidebar-medical">
    <!-- Logo & Header -->
    <div class="mb-10 text-center">
        <div class="relative inline-block">
            <img src="../img/logoesefjl.jpg" alt="Logo ESEFJL" class="w-24 h-24 mx-auto rounded-3xl shadow-2xl border border-slate-100 mb-6 grayscale-[0.2]">
            <div class="absolute inset-0 rounded-3xl border-2 border-[#d4af37]/20 pointer-events-none"></div>
        </div>
        <h1 class="text-[10px] font-black tracking-[0.4em] text-[#111111] uppercase">ESEFJL <span class="text-[#d4af37]">Admin Central</span></h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto pr-2">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-4 ml-2">Control de Gestión</p>
        
        <a href="inicio.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group <?= $current_page == 'inicio.php' ? 'bg-[#111111] text-[#d4af37] shadow-xl shadow-black/10' : 'text-slate-500 hover:bg-slate-50 hover:text-[#111111]' ?>">
            <span class="text-xl">📊</span>
            <span class="text-xs font-bold uppercase tracking-widest">Dashboard</span>
        </a>

        <?php if ($isDirectivo): ?>
        <a href="sedes.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group <?= $current_page == 'sedes.php' ? 'bg-[#111111] text-[#d4af37] shadow-xl shadow-black/10' : 'text-slate-500 hover:bg-slate-50 hover:text-[#111111]' ?>">
            <span class="text-xl">🏥</span>
            <span class="text-xs font-bold uppercase tracking-widest">Inventario Regional</span>
        </a>
        <?php endif; ?>

        <a href="vencidos.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group <?= $current_page == 'vencidos.php' ? 'bg-[#111111] text-[#d4af37] shadow-xl shadow-black/10' : 'text-slate-500 hover:bg-slate-50 hover:text-[#111111]' ?>">
            <span class="text-xl">⌛</span>
            <span class="text-xs font-bold uppercase tracking-widest">Vencimientos</span>
        </a>

        <a href="historial.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group <?= $current_page == 'historial.php' ? 'bg-[#111111] text-[#d4af37] shadow-xl shadow-black/10' : 'text-slate-500 hover:bg-slate-50 hover:text-[#111111]' ?>">
            <span class="text-xl">📜</span>
            <span class="text-xs font-bold uppercase tracking-widest">Historial</span>
        </a>

        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-12 mb-4 ml-2">Evidencia Científica</p>
        
        <a href="../docs/articulo_cientifico.html" target="_blank" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group text-slate-500 hover:bg-slate-50 hover:text-[#111111]">
            <span class="text-xl">📖</span>
            <span class="text-xs font-bold uppercase tracking-widest">Manual Doctoral (ES)</span>
        </a>

        <a href="../docs/articulo_cientifico_en.html" target="_blank" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all group text-slate-500 hover:bg-slate-50 hover:text-[#111111]">
            <span class="text-xl">🌎</span>
            <span class="text-xs font-bold uppercase tracking-widest">Research Draft (EN)</span>
        </a>
    </nav>

    <!-- Footer Sidebar -->
    <div class="mt-auto pt-8 border-t border-slate-50">
        <a href="../core/logout.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-red-400 hover:bg-red-50 hover:text-red-600 transition-all">
            <span class="text-xl">🚪</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Cerrar Sesión</span>
        </a>
    </div>
</aside>
