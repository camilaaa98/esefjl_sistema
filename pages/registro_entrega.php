<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/DeliveryController.php';

$sede_id = $_SESSION['sede_id'];
$db = Database::getInstance();

$pacientes = $db->query("SELECT * FROM pacientes")->fetchAll();
$inventory = InventoryController::getInventoryBySede($sede_id);

$resultado_entrega = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_id = $_POST['paciente_id'];
    $prod_id = $_POST['producto_id'];
    $cant = $_POST['cantidad'];
    $resultado_entrega = DeliveryController::processDelivery($p_id, $prod_id, $cant, $sede_id);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Entregas - ESE FJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: var(--primary-light); border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px; background: var(--primary); }
        
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 5px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        
        .form-container { max-width: 500px; margin: 20px auto; background: var(--primary-light); padding: 30px; border-radius: 15px; border: 1px solid var(--border); }
        h2 { color: var(--secondary); margin-bottom: 25px; text-align: center; font-size: 1.2rem; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; color: var(--text-dim); margin-bottom: 8px; font-size: 0.8rem; text-transform: uppercase; }
        .form-control { width: 100%; background: var(--primary); border: 1px solid var(--border); border-radius: 8px; padding: 12px; color: var(--white); outline: none; transition: var(--transition); }
        .form-control:focus { border-color: var(--secondary); box-shadow: 0 0 15px var(--secondary-soft); }
        
        .btn-submit { width: 100%; background: transparent; border: 1px solid var(--secondary); color: var(--secondary); padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: var(--transition); margin-top: 10px; }
        .btn-submit:hover { background: var(--secondary-soft); box-shadow: 0 0 20px var(--secondary-soft); }
        
        .notif-box { background: rgba(100, 255, 218, 0.05); border-left: 4px solid var(--secondary); padding: 15px; border-radius: 8px; margin-top: 20px; }
        .notif-box strong { color: var(--secondary); font-size: 0.8rem; }
        .notif-box p { color: var(--text-main); font-size: 0.8rem; font-style: italic; margin-top: 5px; }
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
                <a href="#" class="nav-link active">Módulo de Entregas</a>
                <a href="historial.php" class="nav-link">Historial</a>
            </nav>
        </aside>

        <main class="main-view">
            <div class="form-container">
                <h2>REGISTRO TÉCNICO DE ENTREGA</h2>
                
                <?php if ($resultado_entrega): ?>
                    <div style="background:var(--secondary-soft); color:var(--secondary); padding:10px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                        ENTREGA PROCESADA EXITOSAMENTE
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Identificación del Ciudadano</label>
                        <select name="paciente_id" class="form-control" required>
                            <option value="">Seleccione Paciente...</option>
                            <?php foreach ($pacientes as $p): ?>
                                <?php 
                                    $info_regimen = $p['regimen'] ?? 'SIN RÉGIMEN';
                                    if ($p['es_desplazado']) $info_regimen = "EXENTO (Ley 1448 / Desplazado)";
                                    else if ($info_regimen == 'SUBSIDIADO') $info_regimen = "EXENTO (Subsidiado)";
                                ?>
                                <option value="<?php echo $p['documento']; ?>">
                                    <?php echo $p['nombres'] . ' ' . $p['apellidos'] . ' [' . $info_regimen . ']'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Insumo a Entregar</label>
                        <select name="producto_id" class="form-control" required>
                            <option value="">Seleccione Medicamento...</option>
                            <?php foreach ($inventory as $i): ?>
                                <option value="<?php echo $i['producto_id']; ?>"><?php echo strtoupper($i['nombre_generico']); ?> (Disp: <?php echo $i['stock_actual']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad Autorizada</label>
                        <input type="number" name="cantidad" class="form-control" min="1" required placeholder="0">
                    </div>

                    <button type="submit" class="btn-submit">REGISTRAR Y DISPARAR NOTIFICACIÓN</button>
                </form>

                <?php if ($resultado_entrega): ?>
                    <div class="notif-box">
                        <strong>LOG DE MENSAJERÍA AUTOMÁTICA:</strong>
                        <p><?php echo $resultado_entrega['preview']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
