<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Institucional - ESEFJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-white overflow-hidden">

    <div class="login-split-wrapper">
        <!-- LADO VISUAL (AMAZONÍA) -->
        <div class="login-side-visual" style="background: url('<?= BASE_URL ?>/img/GrupoTrabajoSP.jpg') center/cover no-repeat;">
            <div class="login-side-content text-center">
                <span class="login-side-tag">Puerta de Oro de la Amazonía</span>
                <h1 class="login-side-title text-center">Gestión <br><span class="text-[#d4af37]">Farmacéutica</span></h1>
                <p class="login-side-desc text-center">Sistema centralizado de control de suministros y vigilancia sanitaria para la red de IPS de la ESE Fabio Jaramillo Londoño. Optimizando la salud en el departamento del Caquetá.</p>
                
                <div class="login-side-footer justify-center">
                    <div class="login-side-footer-item text-center">
                        <h4>Ubicación</h4>
                        <p>Florencia, Caquetá</p>
                    </div>
                    <div class="login-side-footer-item text-center">
                        <h4>Institución</h4>
                        <p>ESE Fabio Jaramillo Londoño</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LADO FORMULARIO -->
        <div class="login-form-area text-center">
            <div class="login-form-container mx-auto">
                <div class="flex justify-center">
                    <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="Logo" class="login-form-logo mx-auto">
                </div>

                <div class="login-form-header text-center">
                    <h2>Bienvenido</h2>
                    <p>Ingresa tus credenciales administrativas para acceder al panel de control central.</p>
                </div>

                <form id="loginForm">
                    <?= CsrfMiddleware::field() ?>
                    
                    <div class="institutional-input-group">
                        <label class="institutional-label text-center">Usuario</label>
                        <input type="text" id="username" class="institutional-input text-center" placeholder="admin_user" required>
                    </div>

                    <div class="institutional-input-group relative">
                        <label class="institutional-label text-center">Contraseña</label>
                        <div class="relative">
                            <input type="password" id="password" class="institutional-input text-center" placeholder="••••••••" required>
                            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#064e3b] transition-colors" onclick="togglePassword()">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="login-error" class="hidden py-3 px-4 bg-red-50 border border-red-100 rounded-xl text-red-600 text-xs font-bold mb-6 text-center">
                        Credenciales no válidas. Contacte a soporte si el problema persiste.
                    </div>

                    <div class="flex justify-center mt-4">
                        <button type="submit" class="btn-login-new !w-auto !px-12 !py-4 mx-auto block text-center">
                            INGRESAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (p.type === 'password') {
                p.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                p.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
    <script src="<?= BASE_URL ?>/public/js/login.js"></script>
</body>
</html>
