<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/InventoryController.php';

$rol = $_SESSION['rol'];
$vencidos = InventoryController::getExpiredInventory();

// Lógica de Baja (Solo Gerente)
if (isset($_POST['btnBaja']) && $rol === 'Gerente') {
    require_once __DIR__ . '/../core/Database.php';
    $db = Database::getInstance();
    $id = $_POST['inventario_id'];
    $db->prepare("DELETE FROM inventario WHERE id = ?")->execute([$id]);
    header('Location: vencidos.php?msg=Baja autorizada exitosamente');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vencidos - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col items-center justify-center text-center fade-in-institutional">
                <div>
                    <h2 class="text-3xl font-black text-[#111111] tracking-tight italic uppercase">Módulo de Saneamiento y <span class="text-[#d4af37]">Bajas</span></h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">Control de Farmacovigilancia Central</p>
                </div>
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mt-4 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 px-6 py-2 rounded-2xl font-bold animate-bounce shadow-xl">âœ… <?= $_GET['msg'] ?></div>
                <?php endif; ?>
            </header>

            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden fade-in-institutional" style="animation-delay: 0.1s">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-[#111111]">
                    <span class="text-[10px] font-black text-[#d4af37] uppercase tracking-widest underline decoration-2 underline-offset-4">Lista de Cuarentena (Destrucción Autorizada)</span>
                    <span class="text-[9px] font-black text-slate-400 italic">Excluido del inventario de suministro activo</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5">Medicamento</th>
                                <th class="px-8 py-5">Sede Origen</th>
                                <th class="px-8 py-5 text-center">Cantidad</th>
                                <th class="px-8 py-5">Vencimiento</th>
                                <th class="px-8 py-5 text-right">Acción Gerencial</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($vencidos as $v): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-black text-[#111111] uppercase leading-tight italic"><?= $v['nombre_generico'] ?></div>
                                    <div class="text-[9px] text-slate-400 font-bold tracking-widest mt-1">LAB: <?= $v['laboratorio'] ?> | LOTE: <?= $v['lote'] ?></div>
                                </td>
                                <td class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase italic"><?= $v['sede_nombre'] ?></td>
                                <td class="px-8 py-5 text-center text-sm font-black text-red-600 tabular-nums"><?= $v['stock_actual'] ?> <span class="text-[10px] opacity-50">UND</span></td>
                                <td class="px-8 py-5">
                                    <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter italic shadow-sm">Venció: <?= date('d/M/Y', strtotime($v['fecha_vencimiento'])) ?></span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <?php if ($rol === 'Gerente'): ?>
                                        <form method="POST" onsubmit="return confirm('Â¿Confirma la baja definitiva y destrucción física del lote?')">
                                            <input type="hidden" name="inventario_id" value="<?= $v['id'] ?>">
                                            <button type="submit" name="btnBaja" class="bg-[#111111] text-white text-[10px] font-black px-5 py-2.5 rounded-xl hover:bg-black hover:text-[#d4af37] transition-all border border-transparent hover:border-[#d4af37]/30 shadow-lg uppercase tracking-widest">Autorizar Baja</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-[9px] font-black text-slate-300 uppercase italic">Pendiente de Gerencia</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($vencidos)): ?>
                                <tr>
                                    <td colspan="5" class="py-24 text-center">
                                        <div class="opacity-10 text-6xl mb-6">âœ¨</div>
                                        <p class="text-slate-300 text-xs font-black italic tracking-[0.2em] uppercase">No hay productos en cuarentena.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
