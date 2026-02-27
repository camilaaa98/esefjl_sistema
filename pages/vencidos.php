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
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight tracking-tighter">SISFARMA</h1>
                    <p class="text-[8px] text-gray-400 font-black uppercase tracking-widest">ESE Fabio Jaramillo</p>
                </div>
            </div>
            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>🏠</span> Inicio
                </a>
                <a href="vencidos.php" class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-500/10 text-red-500 font-bold rounded-xl transition-all">
                    <span>⚠️</span> Medicamentos Vencidos
                </a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col items-center justify-center text-center">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Módulo de Saneamiento y Bajas</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">Control de productos caducados y segregación de inventario</p>
                </div>
                <?php if (isset($_GET['msg'])): ?>
                    <div class="mt-4 bg-green-500 text-white px-6 py-2 rounded-2xl font-bold animate-bounce shadow-lg">✅ <?= $_GET['msg'] ?></div>
                <?php endif; ?>
            </header>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-700/50 flex justify-between items-center bg-gray-50/50 dark:bg-slate-900/50">
                    <span class="text-[10px] font-black text-red-500 uppercase tracking-widest underline decoration-2 underline-offset-4">Lista de Cuarentena (Para Destrucción)</span>
                    <span class="text-[9px] font-black text-gray-400 italic">Los productos aquí listados están fuera del stock de suministro</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-slate-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-700">
                            <tr>
                                <th class="px-8 py-5">Medicamento</th>
                                <th class="px-8 py-5">Sede Origen</th>
                                <th class="px-8 py-5 text-center">Cantidad</th>
                                <th class="px-8 py-5">Vencimiento</th>
                                <th class="px-8 py-5 text-right">Acción Gerencial</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php foreach ($vencidos as $v): ?>
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-black text-gray-900 dark:text-white uppercase leading-tight"><?= $v['nombre_generico'] ?></div>
                                    <div class="text-[9px] text-gray-400 font-bold tracking-widest mt-1 italic">LAB: <?= $v['laboratorio'] ?> | LOTE: <?= $v['lote'] ?></div>
                                </td>
                                <td class="px-8 py-5 text-xs font-bold text-gray-600 dark:text-gray-400"><?= strtoupper($v['sede_nombre']) ?></td>
                                <td class="px-8 py-5 text-center text-sm font-black text-red-600 tabular-nums"><?= $v['stock_actual'] ?> UND</td>
                                <td class="px-8 py-5">
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter italic">Venció: <?= date('d/M/Y', strtotime($v['fecha_vencimiento'])) ?></span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <?php if ($rol === 'Gerente'): ?>
                                        <form method="POST" onsubmit="return confirm('¿Confirma la baja definitiva y destrucción física del lote?')">
                                            <input type="hidden" name="inventario_id" value="<?= $v['id'] ?>">
                                            <button type="submit" name="btnBaja" class="bg-slate-900 text-white text-[10px] font-black px-4 py-2 rounded-xl hover:bg-black transition-all shadow-lg hover:shadow-red-500/20 uppercase">Autorizar Baja</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-[9px] font-black text-gray-300 uppercase italic">Esperando Gerente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($vencidos)): ?>
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="opacity-20 text-4xl mb-4">🍃</div>
                                        <p class="text-gray-400 font-bold italic tracking-tight uppercase">No hay productos vencidos registrados.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
