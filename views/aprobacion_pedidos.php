<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    die("Acceso restringido a Regencia CEDIS.");
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();

// Procesar Aprobación
if (isset($_GET['approve'])) {
    $pedido_id = $_GET['approve'];
    $db->prepare("UPDATE pedidos_municipios SET estado = 'DESPACHADO' WHERE id = ?")->execute([$pedido_id]);
    header('Location: aprobacion_pedidos.php?msg=despachado');
}

$pedidos = $db->query("
    SELECT p.*, s.nombre as sede 
    FROM pedidos_municipios p 
    JOIN sedes s ON p.sede_solicitante_id = s.id 
    WHERE p.estado = 'PENDIENTE'
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Despacho CEDIS - SISFARMA PRO</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <style>
        .container { max-width: 900px; margin: 40px auto; }
        .pedido-card { background: var(--primary-light); padding: 20px; border-radius: 12px; border-left: 5px solid var(--secondary); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .btn-approve { background: var(--secondary); color: var(--primary); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="inicio.php" style="color:var(--secondary); text-decoration:none;">← Volver</a>
        <h1 style="margin: 20px 0;">ORDENES DE DESPACHO PENDIENTES (CEDIS)</h1>
        
        <?php if (empty($pedidos)): ?>
            <p style="color:var(--text-dim); text-align: center;">No hay solicitudes de reabastecimiento pendientes.</p>
        <?php endif; ?>

        <?php foreach ($pedidos as $p): ?>
            <div class="pedido-card">
                <div>
                    <strong>IPS SOLICITANTE: <?php echo strtoupper($p['sede']); ?></strong><br>
                    <small style="color:var(--text-dim);">ID Referencia: SKU-<?php echo $p['id']; ?> | Fecha: <?php echo $p['fecha_pedido']; ?></small>
                </div>
                <a href="?approve=<?php echo $p['id']; ?>" class="btn-approve">APROBAR Y DESPACHAR</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
