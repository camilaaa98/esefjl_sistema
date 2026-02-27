<!DOCTYPE html>
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

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 dark:border-slate-700 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
                    <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tighter">Inventario en Sede: <?= strtoupper($_SESSION['sede']) ?></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-slate-800/50 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">
                            <tr>
                                <th class="px-8 py-5">Insumo / Medicamento</th>
                                <th class="px-8 py-5 border-x border-gray-100 dark:border-slate-700">Stock Actual</th>
                                <th class="px-8 py-5">Estado Operativo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                            <?php foreach ($inventory as $i): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-8 py-5">
                                    <p class="font-bold text-gray-800 dark:text-gray-200"><?= strtoupper($i['nombre_generico']) ?></p>
                                    <p class="text-[10px] text-gray-400">Lote: <span class="font-mono text-medical-500"><?= $i['lote'] ?? 'L-01' ?></span></p>
                                </td>
                                <td class="px-8 py-5 text-center border-x border-gray-100 dark:border-slate-700">
                                    <span class="text-xl font-black text-gray-900 dark:text-white"><?= $i['stock_actual'] ?></span>
                                    <span class="text-[9px] font-bold text-gray-400 block uppercase">Unidades</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <?php 
                                        $badge = InventoryController::getStatusBadge($i['stock_actual'], $i['stock_minimo'], $i['fecha_vencimiento']);
                                        if (strpos($badge, 'VENCIDO') !== false || strpos($badge, 'CRíTICO') !== false):
                                    ?>
                                        <span class="inline-block px-4 py-1.5 bg-red-50 dark:bg-red-500/10 text-red-500 text-[10px] font-black rounded-full border border-red-100 dark:border-red-500/20 uppercase tracking-widest">Agotado / Crítico</span>
                                    <?php else: ?>
                                        <span class="inline-block px-4 py-1.5 bg-medical-50 dark:bg-medical-500/10 text-medical-500 text-[10px] font-black rounded-full border border-medical-100 dark:border-medical-500/20 uppercase tracking-widest">Disponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($inventory)): ?>
                                <tr>
                                    <td colspan="3" class="px-8 py-20 text-center">
                                        <div class="opacity-30 mb-4 text-4xl">📭</div>
                                        <p class="text-gray-400 font-bold italic tracking-tight uppercase">No hay inventario cargado en esta sede municipal.</p>
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
