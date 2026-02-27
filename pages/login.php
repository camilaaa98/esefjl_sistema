<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro - ESE Fabio Jaramillo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: url('../img/fondo.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="min-h-screen p-6">
    <div class="max-w-md w-full login-glass rounded-3xl shadow-2xl overflow-hidden transform transition-all hover:scale-[1.01]">
        <div class="p-8 md:p-12">
            <div class="flex justify-center mb-8">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-24 h-24 rounded-2xl shadow-lg ring-4 ring-medical-50">
            </div>

            <div class="text-center mb-10">
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">Portal Operativo</h1>
                <p class="text-medical-500 font-bold text-sm">E.S.E. Fabio Jaramillo Londoño</p>
                <div class="w-12 h-1 bg-medical-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Usuario de Red</label>
                    <input type="text" id="username" required 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 outline-none transition-all text-gray-700 font-medium placeholder-gray-300"
                        placeholder="Ej: admin">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Contraseña Institucional</label>
                    <input type="password" id="password" required 
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 outline-none transition-all text-gray-700 font-medium placeholder-gray-300"
                        placeholder="••••••••">
                </div>

                <div id="login-error" class="hidden text-center text-xs font-bold text-red-500 bg-red-50 p-3 rounded-xl border border-red-100">
                    Credenciales incorrectas. Intente de nuevo.
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-medical-500 text-white font-black rounded-2xl shadow-lg shadow-medical-500/30 hover:bg-medical-600 hover:shadow-medical-500/40 transform transition-all active:scale-[0.98] uppercase tracking-widest text-xs">
                    Entrar al Sistema
                </button>
            </form>
        </div>

        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                NIT: 900211468-3 | Florencia, Caquetá — v2.0
            </p>
        </div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>
    <script src="../assets/js/login.js"></script>
</body>
</html>
