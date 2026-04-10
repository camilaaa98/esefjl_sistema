<?php
$current_page = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? '';
$vencidos_count = $vencidos_count ?? 0;

$isDirectivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera']);
?>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? '';
$vencidos_count = $vencidos_count ?? 0;

$isDirectivo = in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente de Servicios de Salud', 'Subgerente Administrativa y Financiera', 'Administrador']);
?>
<aside class="sidebar-glass h-screen sticky top-0 flex flex-col p-8 z-50">
    <div class="flex items-center gap-4 mb-12 animate-fade-up">
        <div class="p-2 bg-white rounded-2xl shadow-premium border border-gray-100">
            <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-xl">
        </div>
        <div>
            <h1 class="text-xl font-black tracking-tighter text-gradient uppercase italic leading-none">Farmacia ESEFJL</h1>
            <span class="text-[8px] text-gray-400 font-bold tracking-[0.25rem] uppercase">Sede Municipal</span>
        </div>
    </div>

    <nav class="flex-1 space-y-3 overflow-y-auto pr-2 custom-scrollbar">
        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest pl-4 mb-4">Módulos de Gestión</p>
        
        <a href="inicio.php" class="flex items-center gap-4 p-4 rounded-2xl transition-all group <?= $current_page == 'inicio.php' ? 'bg-primary-main text-white shadow-premium' : 'text-gray-500 hover:bg-gray-50' ?>">
            <span class="text-xl group-hover:scale-110 transition-transform">📊</span>
            <span class="font-bold text-sm tracking-tight italic">Panel de Control</span>
        </a>

        <?php if ($isDirectivo): ?>
        <a href="sedes.php" class="flex items-center gap-4 p-4 rounded-2xl transition-all group <?= $current_page == 'sedes.php' ? 'bg-primary-main text-white shadow-premium' : 'text-gray-500 hover:bg-gray-50' ?>">
            <span class="text-xl group-hover:scale-110 transition-transform">🗺️</span>
            <span class="font-bold text-sm tracking-tight italic">Red Hospitalaria</span>
        </a>

        <a href="inventario_central.php" class="flex items-center gap-4 p-4 rounded-2xl transition-all group <?= $current_page == 'inventario_central.php' ? 'bg-primary-main text-white shadow-premium' : 'text-gray-500 hover:bg-gray-50' ?>">
            <span class="text-xl group-hover:scale-110 transition-transform">💊</span>
            <span class="font-bold text-sm tracking-tight italic">Stock Maestro</span>
        </a>
        <?php endif; ?>

        <a href="vencidos.php" class="flex items-center gap-4 p-4 rounded-2xl transition-all group relative <?= $current_page == 'vencidos.php' ? 'bg-primary-main text-white shadow-premium' : 'text-gray-500 hover:bg-gray-50' ?>">
            <span class="text-xl group-hover:scale-110 transition-transform">⌛</span>
            <span class="font-bold text-sm tracking-tight italic">Farmaco-vigilancia</span>
            <?php if ($vencidos_count > 0): ?>
                <span class="absolute right-4 top-1/2 -translate-y-1/2 bg-status-red text-white text-[9px] px-2 py-0.5 rounded-full font-black animate-pulse shadow-lg"><?= $vencidos_count ?></span>
            <?php endif; ?>
        </a>

        <a href="historial.php" class="flex items-center gap-4 p-4 rounded-2xl transition-all group <?= $current_page == 'historial.php' ? 'bg-primary-main text-white shadow-premium' : 'text-gray-500 hover:bg-gray-50' ?>">
            <span class="text-xl group-hover:scale-110 transition-transform">🛡️</span>
            <span class="font-bold text-sm tracking-tight italic">Trazabilidad</span>
        </a>

        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest pl-4 mt-8 mb-4">Investigación Doctoral</p>
        
        <a href="../docs/articulo_cientifico.html" target="_blank" class="flex items-center gap-4 p-4 rounded-2xl transition-all group text-gray-500 hover:bg-gray-50">
            <span class="text-xl group-hover:scale-110 transition-transform">📄</span>
            <span class="font-bold text-sm tracking-tight italic">Artículo (Español)</span>
        </a>

        <a href="../docs/articulo_cientifico_en.html" target="_blank" class="flex items-center gap-4 p-4 rounded-2xl transition-all group text-gray-500 hover:bg-gray-50">
            <span class="text-xl group-hover:scale-110 transition-transform">📄</span>
            <span class="font-bold text-sm tracking-tight italic">Article (English)</span>
        </a>
    </nav>

    <div class="mt-auto pt-8 border-t border-gray-100 flex flex-col gap-3">
        <a href="../core/logout.php" class="btn-elite bg-red-50 text-red-600 hover:bg-red-600 hover:text-white justify-center">
            🔒 Salir del Sistema
        </a>
        <div class="text-center">
            <span class="text-[9px] text-gray-400 font-black tracking-[0.3em] uppercase italic">Farmacia ESEFJL</span>
        </div>
    </div>
</aside>

