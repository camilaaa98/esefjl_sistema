<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
$rol = $_SESSION['rol'];
if ($rol !== 'Subgerente de Servicios de Salud' && $rol !== 'Gerente') {
    header('Location: inicio.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AsignaciÛn de Personal - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Inicio.css">
</head>
<body class="bg-gray-50 flex overflow-hidden">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content-area fade-in-institutional">
            <header class="mb-12">
                <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter">GestiÛn de <span class="text-[#d4af37]">Talento Humano</span></h2>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Mando Directo y Control de Cobertura Red IPS Regional</p>
            </header>

            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-slate-100 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 text-slate-50 text-9xl font-black group-hover:text-[#d4af37]/10 transition-colors pointer-events-none italic">HR</div>
                
                <div class="grid grid-cols-1 md:grid-cols-6 gap-8 items-end relative z-10">
                    <div class="md:col-span-2 space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Seleccionar Funcionario de Planta</label>
                        <select class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic shadow-inner cursor-pointer">
                            <option>--- CARGANDO ESCALAF√ìN (500+) ---</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">JurisdicciÛn de Destino (IPS)</label>
                        <select class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic shadow-inner cursor-pointer">
                            <option>SOLITA</option>
                            <option>SOLANO</option>
                            <option>MIL√ÅN</option>
                            <option>SAN ANTONIO DE GETUCHA</option>
                            <option>VALPARA√çSO</option>
                        </select>
                    </div>
                    <button class="md:col-span-2 py-5 bg-[#111111] text-white font-black rounded-3xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] transition-all transform hover:scale-[1.02] active:scale-95 uppercase text-[10px] tracking-[0.4em] border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37]">
                        Confirmar AsignaciÛn Directa
                    </button>
                </div>
            </div>

            <div class="bg-[#111111] p-10 rounded-[3rem] shadow-2xl text-white border border-[#d4af37]/20 relative overflow-hidden">
                <div class="absolute right-0 bottom-0 p-8 opacity-10">
                    <span class="text-8xl">üìä</span>
                </div>
                
                <h3 class="text-[10px] font-black text-[#d4af37] uppercase tracking-[0.5em] mb-10 italic border-l-2 border-[#d4af37] pl-4">Estado de Cobertura Sanitaria IPS</h3>
                
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-8">
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-[#d4af37]/40 transition-all group/stat">
                        <p class="text-3xl font-black text-[#d4af37] group-hover:scale-110 transition-transform">98%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Solita</p>
                    </div>
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-[#d4af37]/40 transition-all group/stat">
                        <p class="text-3xl font-black text-white group-hover:text-[#d4af37] group-hover:scale-110 transition-transform">85%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Solano</p>
                    </div>
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-[#d4af37]/40 transition-all group/stat">
                        <p class="text-3xl font-black text-white group-hover:text-[#d4af37] group-hover:scale-110 transition-transform">92%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Mil·n</p>
                    </div>
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-[#d4af37]/40 transition-all group/stat">
                        <p class="text-3xl font-black text-white group-hover:text-[#d4af37] group-hover:scale-110 transition-transform">77%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">Getucha</p>
                    </div>
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-[#d4af37]/40 transition-all group/stat">
                        <p class="text-3xl font-black text-white group-hover:text-[#d4af37] group-hover:scale-110 transition-transform">95%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-2">ValparaÌso</p>
                    </div>
                </div>
            </div>

            <footer class="mt-20 pt-8 border-t border-slate-100 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center pb-12 italic">
                CONTROL DE TALENTO HUMANO ‚Äî SISFARMA Central v7.5
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
