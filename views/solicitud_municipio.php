<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/InventoryController.php';
require_once __DIR__ . '/../core/RequestController.php';

$mensaje_res = "";
$sede_id = $_SESSION['sede_id'];
$db = Database::getInstance();

// Procesar Pedidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btnManualRequest'])) {
        $res = RequestController::createAutomaticOrder($sede_id);
        $mensaje_res = $res['message'];
    }
    
    if (isset($_POST['btnSolicitudManual'])) {
        $prod_id = $_POST['producto_id'];
        $cant = $_POST['cantidad'];
        $res = RequestController::createManualOrder($sede_id, $prod_id, $cant);
        $mensaje_res = $res['message'];
    }
}

$inventory = InventoryController::getInventoryBySede($sede_id);
$productos_todos = $db->query("SELECT * FROM productos ORDER BY nombre_generico ASC")->fetchAll();
?>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisfarma Pro - LogÃ­stica IPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300 min-h-screen">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="content-area fade-in-institutional">
            <?php if ($mensaje_res): ?>
                <div class="mb-8 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 p-4 rounded-2xl shadow-xl font-bold text-center animate-bounce">
                    ðŸŽ‰ <?= $mensaje_res ?>
                </div>
            <?php endif; ?>

            <header class="flex flex-col items-center justify-center text-center gap-4 relative overflow-hidden bg-[#111111] text-white p-16 rounded-[3rem] mb-12 shadow-2xl border border-[#d4af37]/20">
                <div class="relative z-10">
                    <span class="inline-block px-5 py-2 bg-[#d4af37] text-black text-[9px] font-black rounded-full uppercase tracking-[0.2em] mb-6 animate-pulse">OperaciÃ³n Municipal Ã‰lite</span>
                    <h2 class="text-4xl font-black tracking-tight leading-none mb-4 italic uppercase">LogÃ­stica de <span class="text-[#d4af37]">Suministro IPS</span></h2>
                    <p class="text-slate-400 text-sm italic font-bold tracking-widest uppercase mb-10">GestiÃ³n de Stock Inteligente: <span class="text-[#d4af37] underline decoration-2 underline-offset-8"><?= strtoupper($_SESSION['sede']) ?></span></p>
                    
                    <!-- BotÃ³n Pedido AutomÃ¡tico Premium -->
                    <form method="POST">
                        <button type="submit" name="btnManualRequest" class="group relative flex items-center gap-4 px-10 py-5 bg-white text-[#111111] font-black rounded-2xl shadow-[0_20px_50px_rgba(255,255,255,0.1)] transition-all hover:bg-[#d4af37] hover:scale-105 active:scale-95 uppercase text-xs tracking-widest">
                            ðŸš€ Generar Abastecimiento AutomÃ¡tico al CEDIS
                            <span class="group-hover:translate-x-2 transition-transform">â†’</span>
                        </button>
                    </form>
                </div>
                
                <!-- DecoraciÃ³n EstÃ©tica -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[#d4af37]/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -top-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Tabla de Inventario Ã‰lite -->
                <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden">
                    <div class="px-8 py-7 border-b border-slate-50 flex justify-between items-center bg-[#111111]">
                        <h3 class="font-black text-[#d4af37] text-[10px] uppercase tracking-[0.3em] italic">Estado de Stock Operativo Local</h3>
                        <span class="text-[8px] text-slate-500 font-bold uppercase">Sincronizado en Tiempo Real</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-inter">
                            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                <tr>
                                    <th class="px-8 py-6 text-left">Insumo Magistral</th>
                                    <th class="px-8 py-6">Cant. Actual</th>
                                    <th class="px-8 py-6">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($inventory as $i): ?>
                                <tr class="hover:bg-slate-50 transition-all group">
                                    <td class="px-8 py-5">
                                        <p class="font-black text-sm text-[#111111] uppercase italic leading-none mb-1"><?= strtoupper($i['nombre_generico']) ?></p>
                                        <p class="text-[9px] text-slate-400 font-bold tracking-tighter uppercase"><?= $i['laboratorio'] ?> | <?= $i['concentracion_presentacion'] ?></p>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="text-lg font-black text-[#111111] tabular-nums"><?= $i['stock_actual'] ?></span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="scale-90 flex justify-center">
                                            <?php 
                                                echo InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']);
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario Solicitud Manual Ã‰lite -->
                <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-slate-100 relative overflow-hidden">
                    <div class="mb-10 relative z-10 border-l-4 border-l-[#d4af37] pl-6">
                        <h3 class="text-[10px] font-black text-[#111111] uppercase tracking-[0.4em] mb-2 italic">Solicitud Especial Manual</h3>
                        <p class="text-slate-400 text-[11px] font-bold leading-relaxed uppercase tracking-tighter">Requerimientos extraordinarios de VademÃ©cum</p>
                    </div>
                    
                    <form method="POST" class="space-y-8 relative z-10">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Seleccionar Insumo del CatÃ¡logo</label>
                            <select name="producto_id" class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-[11px] font-black text-[#111111] uppercase italic cursor-pointer shadow-inner" required>
                                <option value="" class="text-slate-300 italic">--- BUSCAR EN EL VADEMÃ‰CUM ---</option>
                                <?php foreach ($productos_todos as $p): ?>
                                    <option value="<?= $p['id'] ?>" class="font-black"><?= strtoupper($p['nombre_generico']) ?> (<?= $p['laboratorio'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Cantidad Requerida</label>
                            <input type="number" name="cantidad" min="1" placeholder="CANTIDAD DE UNIDADES" class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-black text-[#111111] placeholder:text-slate-200 shadow-inner" required>
                        </div>

                        <button type="submit" name="btnSolicitudManual" class="w-full py-6 bg-[#111111] text-white font-black rounded-3xl shadow-[0_15px_40px_rgba(0,0,0,0.15)] transition-all transform hover:scale-[1.02] active:scale-95 uppercase text-[11px] tracking-[0.3em] border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37]">
                            ðŸ“¤ Radicar Solicitud de Despacho
                        </button>
                    </form>

                    <div class="mt-12 pt-8 border-t border-slate-50 relative z-10">
                        <div class="flex items-start gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100 italic group hover:bg-[#111111] transition-all duration-500">
                            <span class="text-2xl group-hover:rotate-12 transition-transform">ðŸ›Žï¸</span>
                            <div class="text-[9px] text-slate-400 font-bold leading-relaxed uppercase tracking-widest group-hover:text-[#d4af37]">
                                <strong class="text-slate-600 group-hover:text-white">Nota TÃ©cnica de LogÃ­stica:</strong><br>
                                Las solicitudes manuales ingresan a la cola de auditorÃ­a del CEDIS central. Tiempo de trÃ¡nsito estimado: 24-72 horas hÃ¡biles.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="mt-20 pt-8 border-t border-slate-100 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center pb-12 italic">
                CONTROL DE LOGÃSTICA MUNICIPAL â€” SISFARMA Ã‰LITE v7.5
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    </div>

    <script src="../assets/js/theme-toggle.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
