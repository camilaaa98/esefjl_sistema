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
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Inicio</span>
        </a>

        <?php if ($isDirectivo): ?>
        <a href="sedes" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= ($current_page == 'sedes.php') ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Sedes / IPS</span>
        </a>
        <?php endif; ?>

        <a href="vencidos" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'vencidos.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Vencimientos</span>
        </a>

        <a href="registro-paciente" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'registro_paciente.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Pacientes</span>
        </a>

        <a href="registro-entrega" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'registro_entrega.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.281a2 2 0 01-1.18.168l-2.352-.294a2 2 0 00-1.236.324l-1.436.958a2 2 0 00-.782 1.186l-.423 1.477a2 2 0 00.159 1.419l.856 1.498a2 2 0 001.317.953l1.558.312a2 2 0 001.623-.467l1.142-.856a2 2 0 011.283-.51h3.14a2 2 0 011.513.657l1.482 1.556a2 2 0 001.31.914l1.678.335a2 2 0 001.887-1.186l.502-1.256a2 2 0 00-.149-1.43l-.949-1.495a2 2 0 00-1.347-.949l-1.677-.335z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Entregas</span>
        </a>

        <a href="historial" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all group <?= $current_page == 'historial.php' ? 'bg-[#111111] text-[#d4af37] shadow-lg border border-[#d4af37]/20' : 'text-slate-500 hover:bg-slate-100 hover:text-[#111111]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em]">Auditoría</span>
        </a>

    </nav>

    <!-- Footer Sidebar -->
    <div class="mt-auto pt-3 border-t border-slate-50">
        <a href="logout" class="flex items-center gap-2 px-2 py-2 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span class="text-[9px] font-black uppercase tracking-wider">Salir</span>
        </a>
    </div>
</aside>
