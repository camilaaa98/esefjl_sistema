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
    <title>Sisfarma Pro - Logística IPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300 min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Copy From Dashboard -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-medical-500 font-extrabold text-lg leading-tight">SISFARMA</h1>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold tracking-widest uppercase">ESE Fabio Jaramillo</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>📊</span> Resumen Operativo
                </a>
                <a href="solicitud_municipio.php" class="flex items-center gap-3 p-3 bg-medical-50 dark:bg-medical-500/10 text-medical-500 font-bold rounded-xl transition-all">
                    <span>🚚</span> Pedido de Insumos
                </a>
                <a href="registro_entrega.php" class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-xl transition-all">
                    <span>💊</span> Entregas Pacientes
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-700">
                <button id="theme-toggle" class="w-full flex items-center justify-center gap-2 p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-xs font-bold text-gray-600 dark:text-gray-300 transition-all">
                    <span class="dark:hidden">🌙 Modo Oscuro</span>
                    <span class="hidden dark:block">☀️ Modo Claro</span>
                </button>
                <a href="../core/logout.php" class="block text-center mt-2 text-[10px] font-bold text-red-500 tracking-widest">⏻ SALIR</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <?php if ($mensaje_res): ?>
                <div class="bg-medical-500 text-white p-4 rounded-2xl shadow-lg font-bold text-center animate-bounce">
                    🎉 <?= $mensaje_res ?>
                </div>
            <?php endif; ?>

            <header class="bg-medical-500 rounded-[30px] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl shadow-medical-500/20">
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-4xl font-black mb-4 tracking-tighter italic uppercase">Abastecimiento IPS</h2>
                    <p class="max-w-xl text-medical-50 font-medium leading-relaxed mb-8 opacity-90">
                        Sincroniza el inventario local de la IPS con el CEDIS central para garantizar el stock de insumos críticos.
                    </p>
                    <form method="POST">
                        <button type="submit" name="btnManualRequest" 
                            class="px-8 py-4 bg-white text-medical-500 font-black rounded-2xl shadow-xl hover:scale-105 transition-transform active:scale-95 uppercase tracking-widest text-xs">
                            ⚡ Enviar Pedido Automático al CEDIS
                        </button>
                    </form>
                </div>
                <!-- Decor -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Tabla de Inventario -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter">Estado de Stock Local</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-inter">
                            <thead class="bg-gray-50 dark:bg-slate-800/50 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">
                                <tr>
                                    <th class="px-6 py-5">Insumo</th>
                                    <th class="px-6 py-5">Stock</th>
                                    <th class="px-6 py-5">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                                <?php foreach ($inventory as $i): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-black text-xs text-gray-800 dark:text-gray-200"><?= strtoupper($i['nombre_generico']) ?></p>
                                        <p class="text-[9px] text-gray-400 italic"><?= $i['laboratorio'] ?> - <?= $i['concentracion_presentacion'] ?></p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-gray-900 dark:text-white"><?= $i['stock_actual'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                            echo InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']);
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario Solicitud Manual -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="mb-8">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2 italic">Solicitud Especial Manual</h3>
                        <p class="text-gray-500 text-[11px] leading-relaxed">Use este formulario para requerimientos extraordinarios no cubiertos por el sistema automático.</p>
                    </div>
                    
                    <form method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Seleccionar Insumo del Catálogo</label>
                            <select name="producto_id" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-xs font-bold text-gray-700 dark:text-gray-300" required>
                                <option value="">— Buscar en el Vademécum —</option>
                                <?php foreach ($productos_todos as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= strtoupper($p['nombre_generico']) ?> (<?= $p['laboratorio'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cantidad Requerida</label>
                            <input type="number" name="cantidad" min="1" placeholder="Ej: 50" class="w-full p-4 bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-medical-500/10 focus:border-medical-500 transition-all text-sm font-black dark:text-white" required>
                        </div>

                        <button type="submit" name="btnSolicitudManual" class="w-full py-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-3xl shadow-xl transition-all transform hover:scale-[1.01] uppercase text-[10px] tracking-widest">
                            📤 Radicar Solicitud Manual
                        </button>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-500/5 rounded-2xl border border-blue-100 dark:border-blue-500/20">
                            <span class="text-xl">ℹ️</span>
                            <div class="text-[10px] text-blue-600 dark:text-blue-400 leading-relaxed italic">
                                <strong>Nota Técnica:</strong> Los pedidos radicados ingresan a la cola de despacho del Regente en Florencia. El tiempo estimado es de 24-48 horas según disponibilidad de transporte.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
