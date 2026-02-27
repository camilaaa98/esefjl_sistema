<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$proveedores = $db->query("SELECT * FROM proveedores")->fetchAll();
$compras = $db->query("
    SELECT c.*, p.razon_social 
    FROM compras c 
    JOIN proveedores p ON c.proveedor_id = p.id 
    ORDER BY fecha_compra DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores Pro - ESE FJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: #020c1b; border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px; background: #0a192f; }
        
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 5px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-action { background: var(--secondary); color: var(--primary); border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; }

        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .data-card { background: var(--primary-light); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .card-title { padding: 12px 20px; background: rgba(100,255,218,0.05); border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--secondary); font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th { text-align: left; padding: 12px 20px; color: var(--text-dim); border-bottom: 1px solid var(--border); }
        td { padding: 10px 20px; color: var(--text-main); border-bottom: 1px solid rgba(100,255,218,0.05); }
        
        .badge-status { padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .bg-paid { background: rgba(100, 255, 218, 0.2); color: var(--secondary); }
        .bg-pending { background: rgba(245, 127, 23, 0.2); color: var(--accent); }
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
                <a href="solicitud_municipio.php" class="nav-link">Distribución IPS</a>
                <a href="#" class="nav-link active">Proveedores</a>
                <a href="historial.php" class="nav-link">Historial</a>
            </nav>
        </aside>

        <main class="main-view">
            <div class="header-flex">
                <h2 style="color:white;">Gestión de Proveedores y Cartera</h2>
                <button class="btn-action">+ NUEVO PROVEEDOR</button>
            </div>

            <div class="dashboard-grid">
                <div class="data-card">
                    <div class="card-title">HISTORIAL DE COMPRAS AL POR MAYOR</div>
                    <table>
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>RAZÓN SOCIAL</th>
                                <th>TOTAL</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compras as $c): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($c['fecha_compra'])); ?></td>
                                <td><?php echo strtoupper($c['razon_social']); ?></td>
                                <td>$<?php echo number_format($c['total'], 0); ?></td>
                                <td>
                                    <span class="badge-status <?php echo $c['estado_pago'] == 'PAGADO' ? 'bg-paid' : 'bg-pending'; ?>">
                                        <?php echo $c['estado_pago']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="data-card">
                    <div class="card-title">DIRECTORIO ACTIVO</div>
                    <div style="padding: 15px;">
                        <?php foreach ($proveedores as $p): ?>
                            <div style="padding: 10px; border-bottom: 1px solid var(--border); margin-bottom: 10px;">
                                <div style="color:var(--secondary); font-size: 0.9rem; font-weight:bold;"><?php echo strtoupper($p['razon_social']); ?></div>
                                <div style="color:var(--text-dim); font-size: 0.75rem;">NIT: <?php echo $p['nit']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
