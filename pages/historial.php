<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$sede_id = $_SESSION['sede_id'];

// Filtro por sede (solo admin puede ver todas)
$filtro_sede = isset($_GET['sede']) ? $_GET['sede'] : null;
$param = ($rol === 'Administrador') ? null : $sede_id;

if ($filtro_sede && $rol === 'Administrador') {
    $stmt = $db->prepare("
        SELECT e.*, p.nombre_generico, pac.nombres AS paciente, s.nombre as sede
        FROM entregas e
        JOIN productos p ON e.producto_id = p.id
        JOIN pacientes pac ON e.paciente_id = pac.documento
        JOIN sedes s ON e.sede_id = s.id
        WHERE e.sede_id = ?
        ORDER BY e.fecha_entrega DESC
    ");
    $stmt->execute([$filtro_sede]);
} elseif ($rol === 'Administrador') {
    $stmt = $db->query("
        SELECT e.*, p.nombre_generico, pac.nombres AS paciente, s.nombre as sede
        FROM entregas e
        JOIN productos p ON e.producto_id = p.id
        JOIN pacientes pac ON e.paciente_id = pac.documento
        JOIN sedes s ON e.sede_id = s.id
        ORDER BY e.fecha_entrega DESC
    ");
} else {
    $stmt = $db->prepare("
        SELECT e.*, p.nombre_generico, pac.nombres AS paciente, s.nombre as sede
        FROM entregas e
        JOIN productos p ON e.producto_id = p.id
        JOIN pacientes pac ON e.paciente_id = pac.documento
        JOIN sedes s ON e.sede_id = s.id
        WHERE e.sede_id = ?
        ORDER BY e.fecha_entrega DESC
    ");
    $stmt->execute([$sede_id]);
}
$movimientos = $stmt->fetchAll();
$sedes = $db->query("SELECT * FROM sedes")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría Histórica - SISFARMA PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <style>
        .app-shell { display: grid; grid-template-columns: 240px 1fr; height: 100vh; background: var(--primary); }
        .sidebar { background: var(--primary-light); border-right: 1px solid var(--border); padding: 15px; display: flex; flex-direction: column; }
        .main-view { overflow-y: auto; padding: 20px 25px; background: var(--primary); }
        .nav-link { color: var(--text-dim); text-decoration: none; padding: 8px 15px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 3px; display: block; transition: var(--transition); }
        .nav-link.active, .nav-link:hover { background: var(--secondary-soft); color: var(--secondary); border-left: 3px solid var(--secondary); }
        .data-card { background: var(--primary-light); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .card-title { padding: 12px 20px; background: rgba(100,255,218,0.05); border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--secondary); font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        th { text-align: left; padding: 10px 20px; color: var(--text-dim); border-bottom: 1px solid var(--border); }
        td { padding: 8px 20px; color: var(--text-main); border-bottom: 1px solid rgba(100,255,218,0.04); }
        .badge-ok { padding: 3px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; background: rgba(100,255,218,0.2); color: var(--secondary); }
        .toolbar { display: flex; gap: 10px; margin-bottom: 15px; }
        .search-box { background: var(--primary-light); border: 1px solid var(--border); color: white; padding: 8px 15px; border-radius: 8px; flex: 1; }
        .filter-select { background: var(--primary-light); border: 1px solid var(--border); color: var(--secondary); padding: 8px 15px; border-radius: 8px; }
        .btn-print { background: transparent; color: var(--secondary); border: 1px solid var(--secondary); padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 0.8rem; }
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
            <a href="solicitud_municipio.php" class="nav-link">🚚 Logística</a>
            <a href="registro_entrega.php" class="nav-link">💊 Entregas</a>
            <a href="proveedores.php" class="nav-link">🏭 Proveedores</a>
            <a href="#" class="nav-link active">🔍 Auditoría</a>
        </nav>
        <div style="margin-top:auto; padding:10px; font-size:0.7rem; border-top:1px solid var(--border);">
            <span style="color:var(--secondary); font-weight:bold;"><?= strtoupper($_SESSION['nombre']) ?></span><br>
            <span style="color:var(--text-dim);"><?= strtoupper($rol) ?> | <?= $_SESSION['sede'] ?></span>
            <a href="../core/logout.php" style="color:var(--accent); display:block; margin-top:8px; text-decoration:none;">⏻ DESCONECTAR</a>
        </div>
    </aside>

    <main class="main-view">
        <h2 style="color:white; margin-bottom:15px; text-align:center;">🔍 Trazabilidad de Movimientos</h2>

        <div class="toolbar">
            <input type="text" id="searchInput" class="search-box" placeholder="🔎 Buscar por paciente, insumo o sede..." onkeyup="filtrar()">
            <?php if ($rol === 'Administrador'): ?>
            <form method="GET" style="display:flex;gap:10px;">
                <select name="sede" class="filter-select" onchange="this.form.submit()">
                    <option value="">— Todas las IPS —</option>
                    <?php foreach ($sedes as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($filtro_sede == $s['id']) ? 'selected' : '' ?>><?= $s['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
            <button class="btn-print" onclick="window.print()">🖨️ Exportar Reporte</button>
        </div>

        <div class="data-card">
            <div class="card-title">
                <span>LOG DE OPERACIONES — <?= count($movimientos) ?> REGISTROS</span>
                <small style="color:var(--text-dim);">Actualizado: <?= date('d/m/Y H:i') ?></small>
            </div>
            <table id="tabla">
                <thead>
                    <tr>
                        <th>FECHA / HORA</th>
                        <th>INSUMO GENÉRICO</th>
                        <th>CANT.</th>
                        <th>BENEFICIARIO</th>
                        <th>IPS SEDE</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movimientos)): ?>
                        <tr><td colspan="6" style="text-align:center; padding: 30px; color:var(--text-dim);">Sin registros de movimiento aún.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($m['fecha_entrega'])) ?></td>
                        <td><strong><?= strtoupper($m['nombre_generico']) ?></strong></td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= strtoupper($m['paciente']) ?></td>
                        <td><?= strtoupper($m['sede']) ?></td>
                        <td><span class="badge-ok">PROCESADO ✓</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function filtrar() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#tabla tbody tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
