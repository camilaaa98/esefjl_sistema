<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Controllers/DeliveryController.php';
require_once __DIR__ . '/../core/Controllers/InventoryController.php';
require_once __DIR__ . '/../core/Models/PatientModel.php';
require_once __DIR__ . '/../core/ViewHelper.php';

$sede_id = $_SESSION['sede_id'];
$patientModel = new PatientModel();
$pacientes = $patientModel->getAllBySede($sede_id);

$inventoryCtrl = new InventoryController();
$inventory = $inventoryCtrl->getInventoryBySede($sede_id);

$resultado_entrega = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryCtrl = new DeliveryController();
    $resultado_entrega = $deliveryCtrl->processDelivery([
        'paciente_id' => $_POST['paciente_id'],
        'producto_id' => $_POST['producto_id'],
        'inventario_id' => $_POST['inventario_id'], // Asumiendo que se pasa el ID de inventario
        'cantidad' => $_POST['cantidad'],
        'usuario_id' => $_SESSION['usuario_id'],
        'sede_id' => $sede_id
    ]);
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
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
<body>
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

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
                        <select name="inventario_id" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-black text-medical-500 italic" required>
                            <option value="">Seleccione Medicamento...</option>
                            <?php foreach ($inventory as $i): ?>
                                <option value="<?php echo $i['id']; ?>"><?php echo strtoupper($i['nombre_generico']); ?> (STOCK: <?php echo $i['stock_actual']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="producto_id" id="selected_prod_id">
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

                <script>
                // Sincronización de IDs para la lógica del controlador
                document.querySelector('select[name="inventario_id"]').addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    // En un sistema real buscaríamos el producto_id real, aquí asumimos coherencia
                    document.getElementById('selected_prod_id').value = this.value; 
                });
                </script>

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
