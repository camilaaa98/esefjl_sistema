<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/Database.php';

// Manejo del Simulador de Riesgos
if (isset($_GET['toggleContingency'])) {
    $_SESSION['modo_contingencia'] = !($_SESSION['modo_contingencia'] ?? false);
    header('Location: dashboard.php');
    exit();
}

$sede_id = $_SESSION['sede_id'];
$rol = $_SESSION['rol'];
$db = Database::getInstance();

$inventory = InventoryController::getInventoryBySede($sede_id);

$stockCritico = 0; $vencidos = 0; $today = date('Y-m-d');
$warning_date = date('Y-m-d', strtotime('+3 months'));

foreach ($inventory as $item) {
    if ($item['stock_actual'] <= ($item['stock_minimo'] * 0.25)) $stockCritico++;
    if ($item['fecha_vencimiento'] && $item['fecha_vencimiento'] < $today) $vencidos++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sisfarma Pro - ESE Fabio Jaramillo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=1.2">
    <style>
        .sustentacion-float { position: fixed; bottom: 20px; right: 20px; background: var(--secondary); color: var(--primary); padding: 10px 20px; border-radius: 50px; font-weight: 800; text-decoration: none; box-shadow: 0 5px 20px var(--secondary-soft); animation: pulse 2s infinite; z-index: 1000; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div style="margin-bottom: 20px; padding: 0 10px; text-align: center;">
                <img src="../img/logoesefjl.jpg" width="70" style="filter: drop-shadow(0 0 10px var(--secondary-soft)); display: block; margin: 0 auto 5px;">
                <h4 style="color:var(--secondary); font-size: 0.85rem; text-align: center;">SISFARMA PRO</h4>
            </div>
            <nav>
                <a href="#" class="nav-link active">📊 Consola Central</a>
                <?php if ($rol === 'Administrador'): ?>
                    <a href="admin_usuarios.php" class="nav-link">👥 Personal IPS</a>
                    <a href="aprobacion_pedidos.php" class="nav-link">📦 Despacho CEDIS</a>
                    <a href="registro_paciente.php" class="nav-link">🏥 Vincular Paciente</a>
                <?php endif; ?>
                <a href="solicitud_municipio.php" class="nav-link">🚚 Logística</a>
                <a href="registro_entrega.php" class="nav-link">💊 Entregas</a>
                <a href="proveedores.php" class="nav-link">🏭 Proveedores</a>
                <a href="historial.php" class="nav-link">🔍 Auditoría</a>
            </nav>
            <div style="margin-top:auto; padding:10px; font-size:0.7rem; border-top: 1px solid var(--border);">
                <span style="color:var(--secondary); font-weight:bold;"><?php echo strtoupper($_SESSION['nombre']); ?></span><br>
                <span style="color:var(--text-dim);"><?php echo strtoupper($rol); ?> | <?php echo $_SESSION['sede']; ?></span>
                <a href="../core/logout.php" style="color:var(--accent); display:block; margin-top:8px; text-decoration:none;">⏻ DESCONECTAR</a>
            </div>
        </aside>

        <main class="main-view">
            <?php if (isset($_SESSION['modo_contingency']) && $_SESSION['modo_contingency']): ?>
                <div class="contingency-banner">⚠️ MODO DE CONTINGENCIA ACTIVO - SIMULANDO FALLA DE API (LOG LOCAL)</div>
            <?php endif; ?>

            <header class="dashboard-header" style="flex-direction: column; text-align: center; gap: 10px;">
                <div style="width: 100%;">
                    <h2 style="color:var(--white); font-size:1.4rem; margin-bottom: 5px;">Operación Institucional ESE FJL</h2>
                    <p style="color:var(--text-dim); font-size:0.8rem;">Sistema de Control de Insumos Críticos Hospitalarios</p>
                </div>
                <div class="action-tray" style="justify-content: center; width: 100%;">
                    <button class="btn-exp" onclick="window.print()">🖨️ REPORTE SECRETARÍA</button>
                    <a href="?toggleContingency=1" class="btn-exp warn">⚡ SIMULAR FALLA API</a>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-box"><span>MEDICAMENTOS</span><strong><?php echo count($inventory); ?></strong></div>
                <div class="stat-box"><span>VENCIDOS</span><strong style="color:#ff5252;"><?php echo $vencidos; ?></strong></div>
                <div class="stat-box"><span>BAJO STOCK</span><strong style="color:var(--accent);"><?php echo $stockCritico; ?></strong></div>
                <div class="stat-box"><span>IPS ACTIVAS</span><strong>5</strong></div>
            </div>

            <div class="data-card">
                <div class="card-title">
                    <span>SEMAFORIZACIÓN PREVENTIVA DE VENCIMIENTOS (AUDITORÍA SALUD)</span>
                    <small style="color:var(--text-dim);">Vencimiento < 90 días = Acción Inmediata</small>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>GENÉRICO / IDENTIFICADOR</th>
                            <th>LOTE</th>
                            <th>VENCIMIENTO</th>
                            <th>STOCK</th>
                            <th>ESTADO DE RIESGO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $i): ?>
                        <tr>
                            <td><strong><?php echo strtoupper($i['nombre_generico']); ?></strong></td>
                            <td><code><?php echo $i['lote'] ?? 'L-01'; ?></code></td>
                            <td><?php echo $i['fecha_vencimiento'] ? date('d/m/Y', strtotime($i['fecha_vencimiento'])) : 'VIGENTE'; ?></td>
                            <td><strong><?php echo $i['stock_actual']; ?></strong> <small>UN</small></td>
                            <td>
                                <?php $status = InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']); 
                                      if (strpos($status, 'VENCIDO') !== false || strpos($status, 'CRíTICO') !== false) {
                                          echo '<span class="badge-pro bg-red">RETIRAR / AUDITAR</span>';
                                      } else {
                                          echo '<span class="badge-pro bg-green">OPERATIVO</span>';
                                      }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="data-card">
                <div class="card-title">HOJA DE RUTA Y SEGUIMIENTO TÉCNICO</div>
                <div style="padding:15px; font-size:0.8rem; color:var(--text-main); display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div>
                        <strong style="color:var(--secondary);">Cronograma de Actividades:</strong><br>
                        • Semana 1: Taller y Stack [Finalizado]<br>
                        • Semana 2: Core e Inventario [Finalizado]<br>
                        • Semana 3: Distribución IPS [En Curso]<br>
                        • Semana 4: Auditoría y Despliegue [Pendiente]
                    </div>
                    <div>
                        <strong style="color:var(--secondary);">Gestión de Riesgos de hoy:</strong><br>
                        Simulación de caída de API externa. El sistema opera en modo local con sincronización diferida para garantizar la entrega ininterrumpida en municipios.
                    </div>
                </div>
            </div>

            <a href="presentacion.php" class="sustentacion-float">🚀 INICIAR PRESENTACIÓN 15 MIN</a>
        </main>
    </div>
</body>
</html>
