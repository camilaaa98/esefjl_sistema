<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    die("Acceso restringido a Regencia CEDIS.");
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();

// Procesar AprobaciÃ³n
if (isset($_GET['approve'])) {
    $pedido_id = $_GET['approve'];
    $db->prepare("UPDATE pedidos_municipios SET estado = 'DESPACHADO' WHERE id = ?")->execute([$pedido_id]);
    header('Location: aprobacion_pedidos.php?msg=despachado');
}

$pedidos = $db->query("
    SELECT p.*, s.nombre as sede 
    FROM pedidos_municipios p 
    JOIN sedes s ON p.sede_solicitante_id = s.id 
    WHERE p.estado = 'PENDIENTE'
")->fetchAll();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despacho EstratÃ©gico CEDIS - SISFARMA Ã‰LITE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 flex overflow-hidden">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="content-area fade-in-institutional">
            <header class="mb-12">
                <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter">Centro de Despacho <span class="text-[#d4af37]">CEDIS</span></h2>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">GestiÃ³n de Ã“rdenes de Reabastecimiento Regional</p>
            </header>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'despachado'): ?>
                <div class="mb-10 p-5 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-bounce shadow-xl">
                    ORDEN DE DESPACHO PROCESADA EXITOSAMENTE âœ“
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php if (empty($pedidos)): ?>
                    <div class="py-32 text-center bg-white rounded-[3rem] border border-slate-100 shadow-xl">
                        <div class="opacity-10 text-7xl mb-6">ðŸ“¦</div>
                        <p class="text-slate-300 text-xs font-black italic tracking-[0.2em] uppercase">No hay solicitudes de reabastecimiento en cola.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($pedidos as $p): ?>
                    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl border border-slate-100 border-l-[6px] border-l-[#111111] hover:border-l-[#d4af37] transition-all flex flex-col md:flex-row justify-between items-center group">
                        <div class="mb-6 md:mb-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-slate-900 text-white text-[8px] font-black rounded-full uppercase tracking-widest">SKU-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">RECIBIDO EL: <?php echo date('d/M/Y', strtotime($p['fecha_pedido'])); ?></span>
                            </div>
                            <h3 class="text-lg font-black text-[#111111] uppercase italic tracking-tight">IPS SOLICITANTE: <span class="text-[#d4af37]"><?php echo strtoupper($p['sede']); ?></span></h3>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Nivel de Prioridad: <span class="text-amber-600">CRÃTICO OPERATIVO</span></p>
                        </div>
                        
                        <a href="?approve=<?php echo $p['id']; ?>" class="w-full md:w-auto px-8 py-4 bg-[#111111] text-white text-[10px] font-black rounded-2xl shadow-lg border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37] transition-all uppercase tracking-widest text-center group-hover:scale-105">
                            Autorizar y Despachar
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <footer class="mt-20 pt-8 border-t border-slate-100 text-[9px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center pb-12 italic">
                CONTROL DE DESPACHOS CEDIS FLORENCIA â€” SISFARMA Ã‰LITE v7.5
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
