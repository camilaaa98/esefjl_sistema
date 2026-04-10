<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia ESEFJL - ESE Fabio Jaramillo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .login-body {
            background: url('../img/fondo.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .text-gradient-medical {
            background: linear-gradient(to right, #004d40, #00796b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="login-body min-h-screen flex items-center justify-center lg:justify-start p-4 md:p-12 font-inter">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between gap-12">
        
        <!-- Tarjeta de Login (Diseño de la imagen) -->
        <div class="max-w-md w-full bg-white/90 backdrop-blur-md rounded-[2.5rem] shadow-2xl p-8 md:p-12 relative">
            <!-- Mascota en el tope -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2">
                <img src="../img/logoesefjl.jpg" alt="Mascota" class="w-32 h-32 rounded-3xl shadow-xl ring-8 ring-white">
            </div>

            <div class="text-center mt-12 mb-10">
                <h1 class="text-3xl font-black text-emerald-950 tracking-tighter">Portal Operativo</h1>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mt-1">E.S.E. Fabio Jaramillo Londoño</p>
                <div class="w-12 h-1 bg-emerald-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Usuario de Red</label>
                    <input type="text" id="username" required 
                        class="w-full px-6 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-gray-700 font-medium"
                        placeholder="Ej: admin">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Contraseña Institucional</label>
                    <input type="password" id="password" required 
                        class="w-full px-6 py-4 bg-gray-50/50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all text-gray-700 font-medium"
                        placeholder="••••••••">
                </div>

                <div id="login-error" class="hidden text-center text-xs font-bold text-red-500 bg-red-50 p-3 rounded-xl border border-red-100">
                    Credenciales incorrectas.
                </div>

                <button type="submit" 
                    class="w-full py-5 bg-[#00695c] text-white font-black rounded-2xl shadow-xl hover:bg-[#004d40] hover:scale-[1.02] transform transition-all active:scale-[0.98] uppercase tracking-[0.2em] text-[11px]">
                    Entrar al Sistema
                </button>
            </form>

            <div class="mt-12 text-center">
                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest italic">
                    NIT: 900211468-3 | Florencia, Caquetá — v1.0
                </p>
            </div>
        </div>

        <!-- Área de Branding (Derecha de la imagen) -->
        <div class="hidden lg:flex flex-col items-center lg:items-end text-center lg:text-right space-y-6 max-w-2xl text-white drop-shadow-2xl">
            <div class="flex items-center gap-4 bg-white/10 p-4 rounded-3xl backdrop-blur-sm">
                <div class="text-right">
                    <h2 class="text-4xl font-black tracking-tighter uppercase leading-none">E.S.E. FABIO JARAMILLO</h2>
                    <h2 class="text-5xl font-black tracking-tighter uppercase leading-none text-emerald-100">LONDOÑO</h2>
                    <p class="text-[10px] font-bold tracking-[0.3em] uppercase mt-2">Empresa Social del Estado</p>
                </div>
                <img src="../img/logoesefjl.jpg" alt="Logo Secundario" class="w-16 h-16 rounded-xl">
            </div>

            <div class="space-y-1">
                <h3 class="text-3xl font-bold tracking-tight">Willington Arriaga Rivas</h3>
                <p class="text-xl font-medium opacity-80 uppercase tracking-widest text-emerald-50 mt-1">Gerente</p>
            </div>

            <p class="text-4xl font-serif italic text-white drop-shadow-lg py-4">Revive la Salud ¡Luchando de Corazón!</p>

            <div class="flex items-center gap-4 group">
                <div class="bg-red-600 p-2 rounded-full shadow-lg pulseMedical">
                   <span class="text-3xl">📍</span>
                </div>
                <div class="text-left">
                    <p class="text-2xl font-black tracking-tighter text-red-500 uppercase leading-none">Solita - Valparaiso - Solano</p>
                    <p class="text-2xl font-black tracking-tighter text-red-500 uppercase leading-none">Milan y San Antonio de Getucha</p>
                </div>
            </div>

            <div class="fixed bottom-12 right-12 flex items-center gap-4 bg-red-600 pr-6 pl-2 py-2 rounded-2xl shadow-2xl hover:scale-105 transition-transform cursor-pointer">
                <div class="flex gap-2">
                    <span class="bg-blue-600 p-2 rounded-lg text-white font-bold">f</span>
                    <span class="bg-gradient-to-tr from-yellow-400 to-purple-600 p-2 rounded-lg text-white font-bold">📸</span>
                </div>
                <span class="font-black text-xs uppercase tracking-widest">ESE Fabio Jaramillo</span>
            </div>
            
            <!-- Elementos 3D flotantes simulados -->
            <div class="absolute -top-10 right-20 text-7xl opacity-40 animate-bounce">➕</div>
            <div class="absolute bottom-20 right-80 text-8xl opacity-40 animate-pulse">➕</div>
        </div>

    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>

