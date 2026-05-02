<?php
/**
 * Portal de Acceso Administrativo - ESE Fabio Jaramillo
 * Versión 9.5: UTF-8 Puro - Sin Caracteres Corruptos.
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Acceso - FARMACIA ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-gradient-to-br from-[#111111] to-[#000000] min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Efectos dorados de fondo -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#d4af37]/10 rounded-full blur-[100px] -mr-32 -mt-32 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-yellow-500/5 rounded-full blur-[100px] -ml-32 -mb-32 pointer-events-none"></div>

    <div class="max-w-md w-full bg-white/5 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/10 p-8 md:p-12 relative z-10">
        
        <!-- Logo Centrado -->
        <div class="flex justify-center mb-8">
            <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="ESEFJL Logo" 
                 class="w-28 h-28 rounded-2xl shadow-[0_0_25px_rgba(212,175,55,0.3)] ring-2 ring-[#d4af37]/30 object-cover grayscale-[0.2]">
        </div>

        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-white tracking-tight">Iniciar Sesión</h2>
            <p class="text-[#d4af37] text-sm font-bold mt-2 uppercase tracking-widest text-[10px]">
                Acceso Administrativo de Alta Seguridad
            </p>
            <div class="w-12 h-1 bg-[#d4af37] mx-auto mt-4 rounded-full shadow-[0_0_10px_rgba(212,175,55,0.4)]"></div>
        </div>

        <form id="loginForm" class="space-y-6">
            <?= CsrfMiddleware::field() ?>
            <div>
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-widest mb-3 text-center">Usuario de Red</label>
                <input type="text" id="username" required 
                    class="w-full px-6 py-4 bg-white/10 border border-white/10 rounded-2xl focus:ring-2 focus:ring-[#d4af37] outline-none transition-all text-white font-semibold text-center placeholder-slate-400"
                    placeholder="Ej: admin">
            </div>

            <div class="relative group">
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-widest mb-3 text-center">Contraseña</label>
                <div class="relative flex items-center">
                    <input type="password" id="password" required
                        class="w-full px-6 py-4 bg-white/10 border border-white/10 rounded-2xl focus:ring-2 focus:ring-[#d4af37] outline-none transition-all text-white font-semibold text-center placeholder-slate-400 pr-16"
                        placeholder="••••••••">
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-5 flex items-center justify-center text-[#d4af37] hover:text-[#b49020] transition-all">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <script>
                function togglePassword() {
                    const passInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eye-icon');
                    
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        `;
                    } else {
                        passInput.type = 'password';
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        `;
                    }
                }
            </script>

            <div id="login-error" class="hidden text-center text-xs font-bold text-red-400 bg-red-900/30 p-4 rounded-2xl border border-red-500/30 animate-shake">
                ⚠️ Credenciales no válidas. Verifica e intenta de nuevo.
            </div>

            <button type="submit" 
                class="w-full py-5 bg-gradient-to-r from-[#d4af37] to-[#b49020] text-black font-black rounded-2xl shadow-[0_10px_20px_rgba(212,175,55,0.3)] hover:shadow-[0_10px_30px_rgba(212,175,55,0.5)] transform transition-all active:scale-[0.98] uppercase tracking-[0.2em] text-[11px]">
                Autenticar Acceso Central
            </button>
        </form>

        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col items-center gap-2">
            <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest italic">ESEFJL - Florencia, Caquetá</span>
            <span class="text-[9px] text-slate-500 font-bold">NIT 900211468-3</span>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/login.js"></script>
</body>
</html>
