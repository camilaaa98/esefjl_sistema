<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/MunicipalityController.php';
require_once __DIR__ . '/../core/InventoryController.php';

$sede_id = $_SESSION['sede_id'];
$rol = $_SESSION['rol'];
$mensaje_res = '';

if (isset($_POST['btnManualRequest'])) {
    $result = MunicipalityController::createAutoRequest($sede_id);
    $mensaje_res = $result['message'];
}

$inventory = InventoryController::getInventoryBySede($sede_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Logística IPS - SISFARMA PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: var(--primary-light); border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px 25px; background: var(--primary); }
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 8px 15px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 3px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        .hero-banner { background: linear-gradient(135deg, var(--primary-light), #0d2137); border: 1px solid var(--border); border-radius: 20px; padding: 40px; margin-bottom: 25px; text-align: center; }
        .btn-request { background: var(--secondary); color: var(--primary); border: none; padding: 16px 40px; font-size: 1rem; font-weight: 800; border-radius: 50px; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-request:hover { filter: brightness(1.15); transform: translateY(-2px); box-shadow: 0 10px 30px var(--secondary-soft); }
        .data-card { background: var(--primary-light); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .card-title { padding: 12px 20px; background: rgba(100,255,218,0.05); border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--secondary); font-weight: bold; }
        table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        th { text-align: left; padding: 10px 20px; color: var(--text-dim); border-bottom: 1px solid var(--border); }
        td { padding: 8px 20px; color: var(--text-main); border-bottom: 1px solid rgba(100,255,218,0.04); }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div style="margin-bottom: 20px; padding: 0 10px; text-align: center;">
            <img src="../img/logoesefjl.jpg" width="60" style="margin: 0 auto; display: block;">
            <h4 style="color:var(--secondary); font-size: 0.8rem; margin-top:10px; text-align: center;">SISFARMA PRO</h4>
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link">📊 Panel Maestro</a>
            <?php if ($rol === 'Administrador'): ?>
                <a href="admin_usuarios.php" class="nav-link">👥 Personal IPS</a>
                <a href="aprobacion_pedidos.php" class="nav-link">📦 Despacho CEDIS</a>
                <a href="registro_paciente.php" class="nav-link">🏥 Vincular Paciente</a>
            <?php endif; ?>
            <a href="#" class="nav-link active">🚚 Logística</a>
            <a href="registro_entrega.php" class="nav-link">💊 Entregas</a>
            <a href="proveedores.php" class="nav-link">🏭 Proveedores</a>
            <a href="historial.php" class="nav-link">🔍 Auditoría</a>
        </nav>
        <div style="margin-top:auto; padding:10px; font-size:0.7rem; border-top:1px solid var(--border);">
            <span style="color:var(--secondary); font-weight:bold;"><?= strtoupper($_SESSION['nombre']) ?></span><br>
            <span style="color:var(--text-dim);"><?= strtoupper($rol) ?> | <?= $_SESSION['sede'] ?></span>
            <a href="../core/logout.php" style="color:var(--accent); display:block; margin-top:8px; text-decoration:none;">⏻ DESCONECTAR</a>
        </div>
    </aside>
    <main class="main-view">
        <?php if ($mensaje_res): ?>
            <div style="background:var(--secondary-soft); color:var(--secondary); padding:15px; border-radius:10px; border:1px solid var(--border); margin-bottom:20px; text-align:center;">
                <?= $mensaje_res ?>
            </div>
        <?php endif; ?>

        <div class="hero-banner">
            <h1 style="text-align:center;">🚚 Abastecimiento Inteligente</h1>
            <p style="text-align:center; color:var(--text-dim);">El sistema detecta qué medicamentos están por debajo del punto de reorden y envía la solicitud automáticamente al CEDIS Florencia.</p>
            <form method="POST">
                <button type="submit" name="btnManualRequest" class="btn-request">⚡ SOLICITAR ABASTECIMIENTO AL CEDIS</button>
            </form>
        </div>

        <div class="data-card">
            <div class="card-title">INVENTARIO ACTUAL — <?= strtoupper($_SESSION['sede']) ?></div>
            <table>
                <thead>
                    <tr>
                        <th>MEDICAMENTO</th>
                        <th>LOTE</th>
                        <th>VENCIMIENTO</th>
                        <th>STOCK ACTUAL</th>
                        <th>STOCK MÍNIMO</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $i): ?>
                    <tr>
                        <td><strong><?= strtoupper($i['nombre_generico']) ?></strong></td>
                        <td><code><?= $i['lote'] ?? 'L-01' ?></code></td>
                        <td><?= $i['fecha_vencimiento'] ? date('d/m/Y', strtotime($i['fecha_vencimiento'])) : '—' ?></td>
                        <td><strong><?= $i['stock_actual'] ?></strong></td>
                        <td><?= $i['stock_minimo'] ?></td>
                        <td><?= InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inventory)): ?>
                        <tr><td colspan="6" style="text-align:center; padding:20px; color:var(--text-dim);">Sin inventario registrado en esta sede.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
