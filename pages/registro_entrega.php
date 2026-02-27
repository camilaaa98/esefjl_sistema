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
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Entregas - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
<body>
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
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
                <a href="registro_entrega.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>💊</span> Módulo de Entregas
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-700 text-center">
                <button id="theme-toggle" class="w-full flex items-center justify-center gap-2 p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-300">
                    🌓 Cambiar Tema
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6 md:p-10 flex justify-center items-start">
            <div class="max-w-xl w-full bg-white dark:bg-slate-800 p-8 md:p-12 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-slate-700 mt-4">
                <header class="text-center mb-10">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white italic uppercase tracking-tighter leading-tight">Registro Técnico de Entrega</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mt-1">Soporte Farmacológico Red IPS E.S.E FJL</p>
                </header>
                
                <?php if ($resultado_entrega): ?>
                    <div class="mb-8 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-2xl text-green-600 dark:text-green-400 text-center font-bold text-sm">
                        ENTREGA PROCESADA EXITOSAMENTE ✓
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Identificación del Ciudadano</label>
                        <select name="paciente_id" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-bold text-gray-700 dark:text-gray-300" required>
                            <option value="">Seleccione Paciente...</option>
                            <?php foreach ($pacientes as $p): ?>
                                <?php 
                                    $info_regimen = $p['regimen'] ?? 'SIN RÉGIMEN';
                                    if ($p['es_desplazado']) $info_regimen = "EXENTO (Ley 1448)";
                                    else if ($info_regimen == 'SUBSIDIADO') $info_regimen = "EXENTO (Subsidiado)";
                                ?>
                                <option value="<?php echo $p['documento']; ?>">
                                    <?php echo strtoupper($p['nombres'] . ' ' . $p['apellidos']) . ' — ' . $info_regimen; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Insumo a Dispensar</label>
                        <select name="producto_id" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-black text-medical-500 italic" required>
                            <option value="">Seleccione Medicamento...</option>
                            <?php foreach ($inventory as $i): ?>
                                <option value="<?php echo $i['producto_id']; ?>"><?php echo strtoupper($i['nombre_generico']); ?> (STOCK: <?php echo $i['stock_actual']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cantidad Autorizada</label>
                        <input type="number" name="cantidad" min="1" placeholder="0" 
                            class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-black dark:text-white" required>
                    </div>

                    <button type="submit" class="w-full py-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-3xl shadow-xl transition-all transform hover:scale-[1.01] uppercase text-xs tracking-widest">
                        Registrar y Generar Alerta SMS
                    </button>
                </form>

                <?php if ($resultado_entrega): ?>
                    <div class="mt-8 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-gray-100 dark:border-slate-700">
                        <span class="block text-[10px] font-black text-medical-500 uppercase tracking-widest mb-3 italic">Log de Mensajería Automática</span>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 leading-relaxed italic border-l-2 border-medical-500 pl-4"><?php echo $resultado_entrega['preview']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
