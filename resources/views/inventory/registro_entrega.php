<?php

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
$root_path = dirname(__DIR__, 3);
require_once $root_path . '/app/Controllers/DeliveryController.php';
require_once $root_path . '/app/Controllers/InventoryController.php';
require_once $root_path . '/app/Repositories/PatientRepository.php';
require_once $root_path . '/app/Helpers/ViewHelper.php';

$sede_id = $_SESSION['sede_id'];
$patientRepo = new PatientRepository(Database::getInstance());
$pacientes = $patientRepo->getAllBySede($sede_id);


$inventory = InventoryController::getInstance()->getInventoryBySede($sede_id);

$resultado_entrega = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryCtrl = new DeliveryController();
    $resultado_entrega = $deliveryCtrl->processDelivery($_POST);
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Entregas - FARMACIA ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <script src="<?= BASE_URL ?>/public/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-gray-50 flex overflow-hidden">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../../../resources/views/partials/sidebar.php'; ?>

        <!-- Main content -->
        <main class="content-area  fade-in-institutional flex justify-center items-center py-12">
            <div class="max-w-2xl w-full bg-white p-12 md:p-16 rounded-[2.5rem] shadow-2xl border border-slate-100 relative overflow-hidden">
                <!-- Efectos de Fondo Decorativos -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#d4af37]/5 rounded-full blur-3xl"></div>
                
                <header class="text-center mb-12 relative z-10">
                    <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-6 border border-[#d4af37]/30">Protocolo de Dispensación</span>
                    <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter leading-none mb-3">Registro Técnico de <span class="text-[#d4af37]">Entrega</span></h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Soporte Farmacológico SISFARMA Central</p>
                </header>
                
                <?php if ($resultado_entrega): ?>
                    <div class="mb-10 p-5 <?= $resultado_entrega['success'] ? 'bg-[#111111] text-[#d4af37]' : 'bg-red-600 text-white' ?> border <?= $resultado_entrega['success'] ? 'border-[#d4af37]/30' : 'border-red-400' ?> rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-bounce shadow-xl">
                        <?= $resultado_entrega['message'] ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-8 relative z-10">
                    <input type="hidden" name="sede_id" value="<?= $sede_id ?>">
                    
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Identificación del Ciudadano</label>
                        <select name="paciente_id" class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic shadow-inner cursor-pointer" required>
                            <option value="" class="text-slate-300 italic">--- SELECCIONAR BENEFICIARIO ---</option>
                            <?php foreach ($pacientes as $p): ?>
                                <?php 
                                    $info_regimen = $p['regimen'] ?? 'SIN Rí‰GIMEN';
                                    if ($p['es_desplazado'] ?? false) $info_regimen = "EXENTO (Ley 1448)";
                                    else if ($info_regimen == 'SUBSIDIADO') $info_regimen = "EXENTO (Subsidiado)";
                                ?>
                                <option value="<?php echo $p['documento']; ?>">
                                    <?php echo strtoupper($p['nombres'] . ' ' . $p['apellidos']) . ' — ' . $info_regimen; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Suministro Farmacológico</label>
                        <div class="relative group">
                            <select name="inventario_id" id="inventario_select" class="w-full p-5 bg-[#111111] border border-[#d4af37]/20 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#d4af37] italic uppercase cursor-pointer" required>
                                <option value="" class="text-white opacity-30 italic">--- BUSCAR EN STOCK LOCAL ---</option>
                                <?php foreach ($inventory as $i): ?>
                                    <option value="<?php echo $i['id']; ?>" data-producto-id="<?= $i['producto_id'] ?>">
                                        <?php echo strtoupper($i['nombre_generico']); ?> (LOTE: <?= $i['lote'] ?> - DISP: <?php echo $i['stock_actual']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="producto_id" id="selected_prod_id">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2 text-center">Número de Orden Médica</label>
                        <input type="text" name="numero_orden" placeholder="EJ: ORD-2026-XXXX" 
                            class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-black text-[#111111] placeholder:text-slate-200 shadow-inner text-center" required>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2 text-center">Cantidad a Entregar</label>
                        <input type="number" name="cantidad" min="1" value="1"
                            class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xl font-black text-[#111111] shadow-inner text-center" required>
                    </div>

                    <div class="flex items-center justify-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <input type="checkbox" name="copago_pagado" id="copago_pagado" value="1" class="w-6 h-6 rounded-lg accent-[#111111] cursor-pointer">
                        <label for="copago_pagado" class="text-[10px] font-black text-[#111111] uppercase tracking-widest cursor-pointer">Confirmar Pago de Copago</label>
                    </div>

                    <button type="submit" class="w-full py-6 bg-[#111111] text-white font-black rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] transition-all transform hover:scale-[1.02] active:scale-95 uppercase text-[11px] tracking-[0.4em] mt-10 border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37]">
                        Autorizar y Notificar por SMS
                    </button>
                </form>

                <script>
                document.getElementById('inventario_select').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    document.getElementById('selected_prod_id').value = selectedOption.getAttribute('data-producto-id');
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
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
    <script src="<?= BASE_URL ?>/public/js/theme-toggle.js"></script>
    <script src="<?= BASE_URL ?>/public/js/animations.js" defer></script>
</body>
</html>




