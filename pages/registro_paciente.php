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
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinculación de Pacientes - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Reutilizada -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight tracking-tighter uppercase">SISFARMA</h1>
                    <span class="text-[8px] text-gray-400 dark:text-gray-500 font-bold tracking-widest uppercase">ESE Fabio Jaramillo</span>
                </div>
            </div>
            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🏠</span> Inicio
                </a>
                <a href="registro_paciente.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>🏥</span> Vincular Paciente
                </a>
            </nav>
        </aside>
        
        <!-- Main content -->
        <main class="flex-1 p-6 md:p-10 flex justify-center items-center">
            <div class="max-w-2xl w-full bg-white dark:bg-slate-800 p-8 md:p-12 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-slate-700">
                <header class="text-center mb-10">
                    <div class="flex justify-center mb-6">
                        <img src="../img/logoesefjl.jpg" alt="Logo" class="w-20 h-20 rounded-2xl shadow-lg ring-4 ring-medical-50 dark:ring-medical-500/10">
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white italic uppercase tracking-tighter">Vinculación de Pacientes</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Registro oficial de ciudadanos para la Red Regional IPS</p>
                </header>
                
                <?php if($mensaje): ?>
                    <div class="mb-8 p-4 bg-medical-50 dark:bg-medical-500/10 border border-medical-200 dark:border-medical-500/30 rounded-2xl text-medical-600 dark:text-medical-400 text-center font-bold text-sm">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
        
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Documento de Identidad</label>
                            <input type="text" name="documento" placeholder="Cédula de Ciudadanía" 
                                class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nivel Sisbén</label>
                            <input type="text" name="sisben" placeholder="Ej: A1, B4..." 
                                class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white uppercase font-bold text-medical-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nombres</label>
                            <input type="text" name="nombres" placeholder="Nombres" 
                                class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Apellidos</label>
                            <input type="text" name="apellidos" placeholder="Apellidos" 
                                class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white" required>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Celular de Contacto (SMS)</label>
                        <input type="text" name="celular" placeholder="Número para notificaciones" 
                            class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-medium dark:text-white" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">EPS Afiliada</label>
                            <select name="eps" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-bold text-gray-700 dark:text-gray-300" required>
                                <option value="">Seleccione EPS...</option>
                                <option value="Nueva EPS">Nueva EPS</option>
                                <option value="Sanitas">Sanitas</option>
                                <option value="Asmet Salud">Asmet Salud</option>
                                <option value="Fuerzas Militares">Fuerzas Militares</option>
                                <option value="Policía Nacional">Policía Nacional</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Régimen</label>
                            <select name="regimen" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-bold text-gray-700 dark:text-gray-300" required>
                                <option value="">Seleccione Régimen...</option>
                                <option value="CONTRIBUTIVO">CONTRIBUTIVO (Genera Copago)</option>
                                <option value="SUBSIDIADO">SUBSIDIADO (Exento)</option>
                                <option value="ESPECIAL">ESPECIAL</option>
                            </select>
                        </div>
                    </div>
        
                    <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-transparent hover:border-medical-200 transition-all group">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="es_desplazado" class="w-5 h-5 rounded border-gray-300 text-medical-500 focus:ring-medical-500 dark:bg-slate-800 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400 group-hover:text-medical-600 transition-colors uppercase">¿Es población desplazada? (Ley 1448 / Exención)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-5 bg-medical-500 hover:bg-medical-600 text-white font-black rounded-3xl shadow-xl shadow-medical-500/20 transition-all transform hover:scale-[1.01] uppercase text-sm tracking-widest">
                        Vinculación Oficial Digital
                    </button>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
