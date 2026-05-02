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
    <!-- Logo Section -->
    <div class="flex flex-col items-center mb-12 px-4 pt-8">
        <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="Logo ESEFJL" class="w-24 h-24 object-contain mb-4 rounded-2xl shadow-xl border-4 border-white/10">
        <h2 class="text-3xl font-black text-white tracking-tighter uppercase leading-none italic">ESEFJL</h2>
        <span class="text-[#d4af37] text-[8px] font-black uppercase tracking-[0.4em] mt-2 opacity-80">Farmacia Central</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto mt-6">
        <a href="inicio" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'inicio.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>📊</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Dashboard</span>
        </a>

        <?php if ($isDirectivo): ?>
        <a href="sedes" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= ($current_page == 'sedes.php') ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>🏠</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Sedes / IPS</span>
        </a>
        <?php endif; ?>

        <a href="vencidos" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'vencidos.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>⌛</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Vencimientos</span>
        </a>

        <a href="registro-paciente" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'registro_paciente.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>👤</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Pacientes</span>
        </a>

        <a href="registro-entrega" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'registro_entrega.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>💊</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Entregas</span>
        </a>

        <a href="historial" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'historial.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <span>📜</span>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Auditoría</span>
        </a>

    </nav>

    <!-- Footer Sidebar -->
    <div class="mt-auto pt-3 border-t border-slate-50">
        <a href="logout" class="flex items-center gap-2 px-2 py-2 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-all">
            <span>🚪</span>
            <span class="text-[9px] font-black uppercase tracking-wider">Salir</span>
        </a>
    </div>
</aside>
