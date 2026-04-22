<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Acceso - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6">
    
    <div class="max-w-5xl w-full flex flex-col md:flex-row bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
        <!-- Lado Izquierdo: Branding Institucional -->
        <div class="hidden md:flex flex-1 bg-gradient-to-br from-[#111111] to-[#000000] p-16 flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10">
                <img src="../img/logoesefjl.jpg" alt="ESEFJL Logo" class="w-32 h-32 rounded-3xl shadow-2xl mb-8 ring-4 ring-white/10 grayscale-[0.2]">
                <h1 class="text-4xl font-extrabold text-white leading-tight tracking-tighter">
                    Excelencia en Gestión <br>
                    <span class="text-[#d4af37]">Farmacéutica</span>
                </h1>
                <div class="w-16 h-1.5 bg-[#d4af37] mt-6 rounded-full shadow-[0_0_15px_rgba(212,175,55,0.4)]"></div>
            </div>

            <div class="relative z-10">
                <p class="text-slate-200 text-sm font-medium leading-relaxed opacity-80">
                    Empresa Social del Estado Fabio Jaramillo Londoño. <br>
                    <span class="text-[#d4af37] italic">"Revive la Salud ¡Luchando de Corazón!"</span>
                </p>
                <div class="mt-8 flex gap-4">
                    <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] text-white font-bold uppercase tracking-widest border border-white/10">v7.0 Élite Premium</span>
                </div>
            </div>
        </div>

        <!-- Lado Derecho: Formulario de Acceso -->
        <div class="flex-1 p-8 md:p-16 flex flex-col justify-center">
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h2>
                <p class="text-slate-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Acceso Administrativo de Alta Seguridad</p>
            </div>

            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Usuario de Red</label>
                    <div class="relative">
                        <input type="text" id="username" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] outline-none transition-all text-slate-700 font-semibold"
                            placeholder="Ej: admin">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" required 
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] outline-none transition-all text-slate-700 font-semibold"
                            placeholder="••••••••">
                    </div>
                </div>

                <div id="login-error" class="hidden text-center text-xs font-bold text-red-600 bg-red-50 p-4 rounded-2xl border border-red-100 animate-shake">
                    🚨 Credenciales no válidas. Verifica e intenta de nuevo.
                </div>

                <button type="submit" 
                    class="w-full py-5 bg-[#111111] text-white font-black rounded-2xl shadow-xl hover:bg-black border border-transparent hover:border-[#d4af37]/50 transform transition-all active:scale-[0.98] uppercase tracking-[0.2em] text-[11px]">
                    Autenticar Acceso Élite
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-slate-50 flex justify-between items-center bg-transparent">
                <span class="text-[9px] text-slate-300 font-black uppercase tracking-widest italic">ESEFJL — Florencia, Caquetá</span>
                <span class="text-[9px] text-slate-400 font-bold">NIT 900211468-3</span>
            </div>
        </div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>
