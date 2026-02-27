<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    die("Acceso restringido: Solo para Administradores.");
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$usuarios = $db->query("
    SELECT u.*, r.nombre as rol, s.nombre as sede 
    FROM usuarios u 
    JOIN roles r ON u.rol_id = r.id 
    JOIN sedes s ON u.sede_id = s.id
    ORDER BY u.rol_id ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión RRHH - ESE FJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: var(--primary-light); border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px; background: var(--primary); }
        
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 5px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-action { background: var(--secondary); color: var(--primary); border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; }

        .user-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
        .user-card { background: var(--primary-light); border: 1px solid var(--border); padding: 20px; border-radius: 12px; position: relative; }
        .user-card h3 { color: var(--white); font-size: 1rem; margin-bottom: 5px; }
        .user-card p { color: var(--text-dim); font-size: 0.8rem; }
        
        .badge-pro { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; margin-top: 10px; }
        .bg-role { background: var(--primary); color: var(--secondary); border: 1px solid var(--border); }
        .bg-ips { background: var(--secondary-soft); color: var(--secondary); margin-left: 5px; }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div style="margin-bottom: 30px; padding: 0 10px; text-align: center;">
                <img src="../img/logoesefjl.jpg" width="60" style="margin: 0 auto; display: block;">
                <h4 style="color:var(--secondary); font-size: 0.8rem; margin-top:10px; text-align: center;">SISFARMA PRO</h4>
            </div>

            <nav>
                <a href="dashboard.php" class="nav-link">Panel Maestro</a>
                <a href="#" class="nav-link active">Gestión de Usuarios</a>
                <a href="solicitud_municipio.php" class="nav-link">Distribución IPS</a>
                <a href="historial.php" class="nav-link">Historial</a>
            </nav>
        </aside>

        <main class="main-view">
            <div class="header-flex">
                <h2 style="color:white;">Asignación de Personal IPS</h2>
                <button class="btn-action">+ VINCULAR NUEVO JEFE</button>
            </div>

            <div class="user-grid">
                <?php foreach ($usuarios as $u): ?>
                    <div class="user-card">
                        <h3><?php echo strtoupper($u['nombres'] . ' ' . $u['apellidos']); ?></h3>
                        <p>ID: <?php echo $u['documento']; ?></p>
                        <p>USUARIO: @<?php echo $u['username']; ?></p>
                        <span class="badge-pro bg-role"><?php echo strtoupper($u['rol']); ?></span>
                        <span class="badge-pro bg-ips"><?php echo strtoupper($u['sede']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>
