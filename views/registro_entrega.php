<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Controllers/DeliveryController.php';
require_once __DIR__ . '/../core/Controllers/InventoryController.php';
require_once __DIR__ . '/../core/Repositories/PatientRepository.php';
require_once __DIR__ . '/../core/Infrastructure/ViewHelper.php';

$sede_id = $_SESSION['sede_id'];
$patientRepo = new PatientRepository(Database::getInstance());
$pacientes = $patientRepo->getAllBySede($sede_id);


$inventory = InventoryController::getInstance()->getInventoryBySede($sede_id);

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
<body class="bg-gray-50 flex overflow-hidden">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="content-area fade-in-institutional flex justify-center items-center py-12">
            <div class="max-w-xl w-full bg-white p-10 md:p-16 rounded-[4rem] shadow-2xl border border-slate-100 relative overflow-hidden">
                <!-- Efectos de Fondo Decorativos -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#d4af37]/5 rounded-full blur-3xl"></div>
                
                <header class="text-center mb-12 relative z-10">
                    <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-6 border border-[#d4af37]/30">Protocolo de Dispensación</span>
                    <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter leading-none mb-3">Registro Técnico de <span class="text-[#d4af37]">Entrega</span></h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Soporte Farmacológico SISFARMA Central</p>
                </header>
                
                <?php if ($resultado_entrega): ?>
                    <div class="mb-10 p-5 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-bounce shadow-xl">
                        ENTREGA PROCESADA EXITOSAMENTE âœ“
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-8 relative z-10">
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Identificación del Ciudadano</label>
                        <select name="paciente_id" class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic shadow-inner cursor-pointer" required>
                            <option value="" class="text-slate-300 italic">--- SELECCIONAR BENEFICIARIO ---</option>
                            <?php foreach ($pacientes as $p): ?>
                                <?php 
                                    $info_regimen = $p['regimen'] ?? 'SIN RÃ‰GIMEN';
                                    if ($p['es_desplazado']) $info_regimen = "EXENTO (Ley 1448)";
                                    else if ($info_regimen == 'SUBSIDIADO') $info_regimen = "EXENTO (Subsidiado)";
                                ?>
                                <option value="<?php echo $p['documento']; ?>">
                                    <?php echo strtoupper($p['nombres'] . ' ' . $p['apellidos']) . ' â€” ' . $info_regimen; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Suministro Farmacológico</label>
                        <div class="relative group">
                            <select name="inventario_id" class="w-full p-5 bg-[#111111] border border-[#d4af37]/20 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#d4af37] italic uppercase cursor-pointer" required>
                                <option value="" class="text-white opacity-30 italic">--- BUSCAR EN STOCK LOCAL ---</option>
                                <?php foreach ($inventory as $i): ?>
                                    <option value="<?php echo $i['id']; ?>"><?php echo strtoupper($i['nombre_generico']); ?> (DISPONIBLE: <?php echo $i['stock_actual']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="producto_id" id="selected_prod_id">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Cantidad Autorizada M.I.</label>
                        <input type="number" name="cantidad" min="1" placeholder="UNIDADES A DISPENSAR" 
                            class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-black text-[#111111] placeholder:text-slate-200 shadow-inner" required>
                    </div>

                    <button type="submit" class="w-full py-6 bg-[#111111] text-white font-black rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] transition-all transform hover:scale-[1.02] active:scale-95 uppercase text-[11px] tracking-[0.4em] mt-10 border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37]">
                        Autorizar y Notificar por SMS
                    </button>
                </form>

                <script>
                document.querySelector('select[name="inventario_id"]').addEventListener('change', function() {
                    document.getElementById('selected_prod_id').value = this.value; 
                });
                </script>

                <?php if ($resultado_entrega): ?>
                    <div class="mt-12 p-8 bg-slate-50 rounded-[2rem] border border-slate-100 relative group overflow-hidden">
                        <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-20 transition-opacity">
                            <span class="text-6xl text-[#111111]">ðŸ“±</span>
                        </div>
                        <span class="block text-[8px] font-black text-[#d4af37] uppercase tracking-[0.4em] mb-4 italic">Log de Comunicación Central</span>
                        <p class="text-[10px] font-bold text-slate-500 leading-relaxed italic border-l-4 border-l-[#d4af37] pl-6 py-2 group-hover:text-slate-800 transition-colors uppercase"><?php echo $resultado_entrega['preview'] ?? 'Mensajero SISFARMA en espera...'; ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <footer class="fixed bottom-8 w-full text-center text-[8px] font-bold text-slate-300 uppercase tracking-[0.8em] italic pointer-events-none">
                PROTOCOL STATUS: AUDITED BIOMETRIC AUTHENTICATION REQUIRED
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
