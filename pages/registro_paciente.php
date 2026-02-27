<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';

$mensaje = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $doc = $_POST['documento'];
        $nom = $_POST['nombres'];
        $ape = $_POST['apellidos'] ?? ''; // Asegurando compatibilidad con el nuevo esquema
        $cel = $_POST['celular'];
        $eps = $_POST['eps'] ?? '';
        $reg = $_POST['regimen'] ?? '';
        $des = isset($_POST['es_desplazado']) ? 1 : 0;
        $sis = $_POST['sisben'] ?? '';
        
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO pacientes (documento, nombres, apellidos, celular, eps, regimen, es_desplazado, sisben) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$doc, $nom, $ape, $cel, $eps, $reg, $des, $sis]);
            $mensaje = "✅ Ciudadano vinculado exitosamente a la Red de Salud.";
        } catch (Exception $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Vinculación de Pacientes - SISFARMA PRO</title>
        <link rel="stylesheet" href="../assets/css/base.css?v=1.2">
        <style>
            .form-card {
                max-width: 600px;
                margin: 40px auto;
                background: var(--primary-light);
                padding: 30px;
                border-radius: 20px;
                border: 1px solid var(--border);
                box-shadow: var(--shadow);
            }
            .input-box { width: 100%; padding: 12px; margin: 8px 0; background: var(--primary); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; box-sizing: border-box; }
            .btn-save { background: var(--secondary); color: var(--primary); padding: 15px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: 800; width: 100%; margin-top: 20px; transition: var(--transition); }
            .btn-save:hover { filter: brightness(1.2); transform: translateY(-2px); }
            .flex-row { display: flex; gap: 10px; }
            .flex-row > * { flex: 1; }
            label { display: block; margin-top: 10px; color: var(--text-dim); font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div style="padding: 20px;">
            <a href="dashboard.php" style="color:var(--secondary); text-decoration:none;">← Volver al Panel</a>
        </div>
        
        <div class="form-card">
            <div style="text-align: center;">
                <img src="../img/logoesefjl.jpg" width="80" style="margin-bottom: 15px;">
                <h1 style="margin:0;">VINCULACIÓN DE PACIENTES</h1>
                <p style="color:var(--text-dim); margin-bottom: 25px;">Registro oficial de ciudadanos para la Red IPS</p>
            </div>
            
            <?php if($mensaje): ?>
                <div style="text-align:center; color:var(--secondary); margin-bottom: 20px; padding:10px; border:1px solid var(--secondary); border-radius:8px;"><?php echo $mensaje; ?></div>
            <?php endif; ?>
    
            <form method="POST">
                <div class="flex-row">
                    <input type="text" name="documento" placeholder="Cédula de Ciudadanía" class="input-box" required>
                    <input type="text" name="sisben" placeholder="Nivel Sisbén (ej: A1)" class="input-box">
                </div>
                
                <div class="flex-row">
                    <input type="text" name="nombres" placeholder="Nombres" class="input-box" required>
                    <input type="text" name="apellidos" placeholder="Apellidos" class="input-box" required>
                </div>
                
                <input type="text" name="celular" placeholder="Celular de Contacto (SMS)" class="input-box" required>
                
                <label>Entidad Promotora de Salud (EPS)</label>
                <input type="text" name="eps" placeholder="Nombre de la EPS" class="input-box" required>
                
                <label>Régimen de Afiliación</label>
                <select name="regimen" class="input-box" required>
                    <option value="">Seleccione Régimen...</option>
                    <option value="CONTRIBUTIVO">CONTRIBUTIVO (Genera Copago)</option>
                    <option value="SUBSIDIADO">SUBSIDIADO (Exento)</option>
                </select>
    
                <div style="margin-top: 15px; background: var(--secondary-soft); padding: 10px; border-radius: 8px; border-left: 4px solid var(--secondary);">
                    <label style="color:var(--secondary); display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="es_desplazado" style="margin-right:10px;">
                        ¿Es población desplazada? (Ley 1448 / Exención Total)
                    </label>
                </div>
                
                <button type="submit" class="btn-save">VINCULAR CIUDADANO</button>
            </form>
        </div>
    </body>
</html>
