<?php
/**
 * Portal de Acceso - ESE Fabio Jaramillo Londoño
 * Temática: Puerta de Oro de la Amazonía — Caquetá
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Acceso — FARMACIA ESEFJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}

        body.login-body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            background: #0a1a0f;
        }

        /* === FONDO AMAZÓNICO ANIMADO === */
        .amazon-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(ellipse at 20% 80%, rgba(16, 95, 51, 0.4) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(0, 77, 64, 0.3) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(27, 94, 32, 0.15) 0%, transparent 60%),
                linear-gradient(160deg, #071a0e 0%, #0a2818 30%, #0d1f15 60%, #091a10 100%);
        }

        /* Partículas de luciérnagas */
        .firefly {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #a8d867;
            box-shadow: 0 0 8px 3px rgba(168, 216, 103, 0.5);
            animation: float-firefly 8s ease-in-out infinite;
            opacity: 0;
        }
        .firefly:nth-child(1) { left:10%; top:20%; animation-delay:0s; animation-duration:7s; }
        .firefly:nth-child(2) { left:25%; top:70%; animation-delay:2s; animation-duration:9s; }
        .firefly:nth-child(3) { left:70%; top:30%; animation-delay:4s; animation-duration:6s; }
        .firefly:nth-child(4) { left:85%; top:60%; animation-delay:1s; animation-duration:8s; }
        .firefly:nth-child(5) { left:50%; top:15%; animation-delay:3s; animation-duration:10s; }
        .firefly:nth-child(6) { left:40%; top:85%; animation-delay:5s; animation-duration:7s; }
        .firefly:nth-child(7) { left:15%; top:50%; animation-delay:6s; animation-duration:9s; }
        .firefly:nth-child(8) { left:90%; top:40%; animation-delay:2.5s; animation-duration:11s; }

        @keyframes float-firefly {
            0%   { opacity:0; transform: translate(0,0) scale(0.5); }
            20%  { opacity:0.9; }
            50%  { opacity:0.4; transform: translate(30px,-40px) scale(1); }
            80%  { opacity:0.8; }
            100% { opacity:0; transform: translate(-20px,50px) scale(0.5); }
        }

        /* Hojas de fondo */
        .leaf-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Cpath d='M40 10 C20 25, 15 50, 40 70 C65 50, 60 25, 40 10Z' fill='%2322c55e' /%3E%3C/svg%3E");
            background-size: 120px;
        }

        /* Niebla amazónica */
        .mist {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35%;
            z-index: 1;
            background: linear-gradient(to top, rgba(10,26,15,0.7), transparent);
            pointer-events: none;
        }

        /* === CARD DE LOGIN === */
        .login-card {
            position: relative;
            z-index: 10;
            width: 460px;
            max-width: 92vw;
            background: rgba(10, 30, 18, 0.65);
            backdrop-filter: blur(30px) saturate(1.4);
            -webkit-backdrop-filter: blur(30px) saturate(1.4);
            border: 1px solid rgba(34, 197, 94, 0.15);
            border-radius: 2.5rem;
            padding: 3rem 2.5rem;
            box-shadow:
                0 40px 80px rgba(0,0,0,0.5),
                0 0 60px rgba(34, 197, 94, 0.05),
                inset 0 1px 0 rgba(255,255,255,0.05);
            animation: card-enter 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes card-enter {
            to { opacity:1; transform:translateY(0); }
        }

        /* Línea decorativa superior */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #22c55e, #d4af37, #22c55e, transparent);
            border-radius: 3px;
        }

        /* Logo */
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .logo-img {
            width: 110px;
            height: 110px;
            border-radius: 24px;
            object-fit: cover;
            border: 2px solid rgba(34, 197, 94, 0.3);
            box-shadow:
                0 0 30px rgba(34, 197, 94, 0.15),
                0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.5s, box-shadow 0.5s;
        }
        .logo-img:hover {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 0 40px rgba(34, 197, 94, 0.25);
        }

        /* Título */
        .login-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-title h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
            line-height: 1.1;
        }
        .login-title h1 span {
            color: #22c55e;
        }
        .login-title .region-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 16px;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 20px;
            color: #86efac;
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.25em;
            text-transform: uppercase;
        }
        .login-title .subtitle {
            color: #d4af37;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 0.75rem;
        }

        /* Separador */
        .separator {
            width: 50px;
            height: 2px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(90deg, #22c55e, #d4af37);
            border-radius: 2px;
        }

        /* Campos */
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(34, 197, 94, 0.15);
            border-radius: 1rem;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: center;
            outline: none;
            transition: all 0.3s;
        }
        .form-input::placeholder {
            color: rgba(255,255,255,0.2);
        }
        .form-input:focus {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.06);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.08);
        }

        /* Grupo password con toggle */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .form-input {
            padding-right: 3.5rem;
        }
        .toggle-pass {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #22c55e;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
        }
        .toggle-pass:hover { opacity:1; }

        /* Error */
        .login-error {
            display: none;
            text-align: center;
            padding: 0.75rem 1rem;
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.3);
            border-radius: 0.75rem;
            color: #fca5a5;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Botón principal */
        .btn-login {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 50%, #166534 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            border: none;
            border-radius: 1.25rem;
            cursor: pointer;
            transition: all 0.4s;
            box-shadow: 0 10px 30px rgba(22, 163, 74, 0.25);
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.6s;
        }
        .btn-login:hover::before { left:100%; }
        .btn-login:hover {
            box-shadow: 0 15px 40px rgba(22, 163, 74, 0.4);
            transform: translateY(-2px);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(22, 163, 74, 0.2);
        }

        /* Footer institucional */
        .login-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            text-align: center;
        }
        .login-footer .institution {
            font-family: 'Outfit', sans-serif;
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(255,255,255,0.5);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }
        .login-footer .nit {
            font-size: 0.6rem;
            color: rgba(255,255,255,0.25);
            margin-top: 4px;
            letter-spacing: 0.1em;
        }
        .login-footer .amazonia {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 10px;
            font-size: 0.55rem;
            color: #86efac;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.6;
        }

        /* Responsividad */
        @media (max-width: 500px) {
            .login-card { padding: 2rem 1.5rem; border-radius: 2rem; }
            .logo-img { width: 85px; height: 85px; }
            .login-title h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body class="login-body">

    <!-- Fondo amazónico -->
    <div class="amazon-bg"></div>
    <div class="leaf-overlay"></div>
    <div class="mist"></div>

    <!-- Luciérnagas -->
    <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
    <div class="firefly"></div><div class="firefly"></div><div class="firefly"></div>
    <div class="firefly"></div><div class="firefly"></div>

    <!-- Card de Login -->
    <div class="login-card">
        <div class="logo-container">
            <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="ESEFJL Logo" class="logo-img">
        </div>

        <div class="login-title">
            <span class="region-tag">🌿 Puerta de Oro de la Amazonía</span>
            <h1>Iniciar <span>Sesión</span></h1>
            <p class="subtitle">Acceso Administrativo Institucional</p>
        </div>

        <div class="separator"></div>

        <form id="loginForm" autocomplete="off">
            <?= CsrfMiddleware::field() ?>
            
            <div class="form-group">
                <label class="form-label">Usuario de Red</label>
                <input type="text" id="username" class="form-input" placeholder="Ej: admin" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="password" class="form-input" placeholder="••••••••" required>
                    <button type="button" class="toggle-pass" onclick="togglePassword()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="login-error" class="login-error">
                Credenciales no válidas. Verifica e intenta de nuevo.
            </div>

            <button type="submit" class="btn-login">
                Autenticar Acceso Central
            </button>
        </form>

        <div class="login-footer">
            <p class="institution">ESE Fabio Jaramillo Londoño</p>
            <p class="nit">NIT 900211468-3</p>
            <p class="amazonia">🌿 Florencia, Caquetá — Colombia</p>
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
