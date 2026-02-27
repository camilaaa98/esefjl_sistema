<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro - ESE Fabio Jaramillo</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <link rel="stylesheet" href="../assets/css/login.css?v=1.2">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo-section" style="text-align: center;">
                <img src="../img/logoesefjl.jpg" alt="Logo ESE Fabio Jaramillo" class="main-logo" style="margin: 0 auto; display: block;">
            </div>
            
            <header>
                <h1>Portal Farmacéutico</h1>
                <p>E.S.E. Fabio Jaramillo Londoño</p>
                <div class="divider"></div>
            </header>

            <form id="loginForm" autocomplete="off">
                <div class="input-group">
                    <input type="text" id="username" required placeholder=" ">
                    <label>Usuario Autorizado</label>
                    <div class="bar"></div>
                </div>
                
                <div class="input-group">
                    <input type="password" id="password" required placeholder=" ">
                    <label>Contraseña</label>
                    <div class="bar"></div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-login">
                        <span>ENTRAR AL SISTEMA</span>
                        <div class="glow"></div>
                    </button>
                </div>
            </form>

            <footer class="login-footer">
                <p>NIT: 900211468-3 | Florencia, Caquetá</p>
            </footer>
        </div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>
</html>
