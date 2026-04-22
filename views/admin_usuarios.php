<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    die("Acceso restringido: Solo para Administradores.");
}
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$usuarios = $db->query("
    SELECT u.*, r.nombre as rol, s.nombre as sede 
    FROM usuarios u 
    JOIN roles r ON u.rol_id = r.id 
    JOIN sedes s ON u.sede_id = s.id
    ORDER BY u.rol_id ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiÃ³n de Talento Humano - SISFARMA Ã‰LITE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50">
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content-area fade-in-institutional">
            <header class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
                <div>
                    <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter">GestiÃ³n de <span class="text-[#d4af37]">Talento Humano</span></h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">AsignaciÃ³n de Responsabilidades y Roles IPS</p>
                </div>
                <button class="btn-institutional">
                    <span class="text-lg">ðŸ‘¤+</span>
                    Vincular Nuevo Funcionario
                </button>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($usuarios as $u): ?>
                    <div class="card-clinical bg-white border-l-4 border-l-[#111111] hover:border-l-[#d4af37] transition-all group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="p-3 bg-slate-50 rounded-2xl group-hover:bg-[#111111] group-hover:text-[#d4af37] transition-colors">
                                <span class="text-xl">ðŸ’¼</span>
                            </div>
                            <span class="px-3 py-1 bg-slate-900 text-white text-[8px] font-black uppercase rounded-full border border-black shadow-sm">Auditado âœ“</span>
                        </div>
                        
                        <h3 class="text-sm font-black text-[#111111] uppercase italic mb-1 mb-2 border-b border-slate-50 pb-2"><?php echo strtoupper($u['nombres'] . ' ' . $u['apellidos']); ?></h3>
                        
                        <div class="space-y-2 mb-6">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="text-[#d4af37]">ID:</span> <?php echo $u['documento']; ?>
                            </p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="text-[#d4af37]">USUARIO:</span> @<?php echo $u['username']; ?>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-[#111111] text-[#d4af37] text-[8px] font-black uppercase rounded-lg border border-[#d4af37]/20">
                                <?php echo strtoupper($u['rol']); ?>
                            </span>
                            <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[8px] font-black uppercase rounded-lg border border-slate-100">
                                SEDE: <?php echo strtoupper($u['sede']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <footer class="mt-20 pt-8 border-t border-slate-100 flex justify-between items-center text-[9px] font-bold text-slate-300 uppercase tracking-[0.4em] italic pb-12">
                <div>CONTROL DE CONTROL DE TALENTO HUMANO â€” SISFARMA Ã‰LITE v7.5</div>
                <div>AUTORIZADO POR SUBGERENTE ADMINISTRATIVA</div>
            </footer>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
