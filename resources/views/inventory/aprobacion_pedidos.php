<?php

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['Gerente', 'Regente Farmacia', 'Subgerente de Servicios de Salud', 'Administrador'])) {
    die("Acceso restringido a Regencia CEDIS.");
}
$root_path = dirname(__DIR__, 3);
require_once $root_path . '/app/config/Database.php';
require_once $root_path . '/app/Controllers/RequestController.php';

$db = Database::getInstance();
$resultado = null;

// Procesar Aprobación
if (isset($_GET['approve'])) {
    $pedido_id = $_GET['approve'];
    $resultado = RequestController::approveOrder($pedido_id);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despacho Estratégico CEDIS - SISFARMA Central</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../../../resources/views/partials/sidebar.php'; ?>

        <!-- Main content -->
        <main class="content-area  fade-in-institutional">
            <header class="mb-12">
                <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter">Centro de Despacho <span class="text-[#d4af37]">CEDIS</span></h2>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Gestión de í“rdenes de Reabastecimiento Regional</p>
            </header>
            
            <?php if ($resultado): ?>
                <div class="mb-10 p-5 <?= $resultado['success'] ? 'bg-[#111111] text-[#d4af37]' : 'bg-red-600 text-white' ?> border <?= $resultado['success'] ? 'border-[#d4af37]/30' : 'border-red-400' ?> rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-bounce shadow-xl">
                    <?= $resultado['message'] ?>
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php if (empty($pedidos)): ?>
                    <div class="py-32 text-center bg-white rounded-[2.5rem] border border-slate-100 shadow-xl">
                        <div class="opacity-10 text-7xl mb-6">📦</div>
                        <p class="text-slate-300 text-xs font-black italic tracking-[0.2em] uppercase">No hay solicitudes de reabastecimiento en cola.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($pedidos as $p): ?>
                    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl border border-slate-100 border-l-[6px] border-l-[#111111] hover:border-l-[#d4af37] transition-all flex flex-col md:flex-row justify-between items-center group">
                        <div class="mb-6 md:mb-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-slate-900 text-white text-[8px] font-black rounded-full uppercase tracking-widest">SKU-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">RECIBIDO EL: <?php echo date('d/M/Y', strtotime($p['fecha_solicitud'])); ?></span>
                            </div>
                            <h3 class="text-lg font-black text-[#111111] uppercase italic tracking-tight">IPS SOLICITANTE: <span class="text-[#d4af37]"><?php echo strtoupper($p['sede']); ?></span></h3>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Nivel de Prioridad: <span class="text-amber-600">CRíTICO OPERATIVO</span></p>
                        </div>
                        
                        <a href="?approve=<?php echo $p['id']; ?>" class="w-full md:w-auto px-8 py-4 bg-[#111111] text-white text-[10px] font-black rounded-2xl shadow-lg border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37] transition-all uppercase tracking-widest text-center group-hover:scale-105">
                            Autorizar y Despachar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <footer class="mt-20 pt-8 border-t border-slate-100 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center pb-12 italic">
                CONTROL DE DESPACHOS CEDIS FLORENCIA - SISFARMA Central
            </footer>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/inicio.js"></script>
    <script src="<?= BASE_URL ?>/public/js/animations.js" defer></script>
</body>
</html>




