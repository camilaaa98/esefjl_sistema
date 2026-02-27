<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/InventoryController.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$vencidos_count = count(InventoryController::getExpiredInventory());

// Solo directivos pueden ver proveedores
if (!in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente Administrativa y Financiera'])) {
    header('Location: dashboard.php');
    exit();
}

$proveedores = $db->query("SELECT * FROM proveedores ORDER BY razon_social ASC")->fetchAll();
$compras = $db->query("
    SELECT c.*, p.razon_social 
    FROM compras c 
    JOIN proveedores p ON c.proveedor_id = p.id 
    ORDER BY fecha_compra DESC
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores Estratégicos - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Gestión Abastecimiento Global</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">Directorio Jurídico de Proveedores y Control de Cartera CEDIS</p>
                </div>
                <button class="px-6 py-3 bg-medical-500 hover:bg-medical-600 text-white font-black rounded-2xl shadow-lg shadow-medical-500/20 transition-all uppercase text-xs tracking-widest italic">
                    + Registrar Nueva Compra
                </button>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Cuentas por Pagar / Historial Compras</span>
                            <span class="text-[9px] font-black text-medical-500 uppercase italic">Validación Tesorería</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-gray-100 dark:border-slate-700">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Proveedor</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Suministros</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado Pago</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                                    <?php if (empty($compras)): ?>
                                        <tr><td colspan="4" class="px-6 py-20 text-center text-gray-400 italic text-xs">No se registran compras internacionales en este periodo.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($compras as $c): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-all">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 font-mono">
                                            <?= date('d/m/Y', strtotime($c['fecha_compra'])) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-black text-gray-800 dark:text-white uppercase"><?= strtoupper($c['razon_social']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-black text-medical-500">
                                            $<?= number_format($c['total'], 0) ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $c['estado_pago'] == 'PAGADO' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                                <?= $c['estado_pago'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sugerencia de Reposición a Proveedor -->
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white">
                        <h4 class="text-[10px] font-black text-medical-400 uppercase tracking-[0.2em] mb-4">Insumos Críticos Pendientes de Compra</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 italic">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-tighter text-white">Cilindros de Oxígeno Medicinal</p>
                                    <p class="text-[9px] text-gray-500 font-bold">Proveedor Sugerido: CLINICAL GAS SAS</p>
                                </div>
                                <span class="text-sm font-black text-medical-500">Sugerido: 50 Balas</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 italic">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-tighter text-white">Kits Citología Estéril</p>
                                    <p class="text-[9px] text-gray-400 font-bold">Proveedor Sugerido: MEDICAL SURGICAL LTDA</p>
                                </div>
                                <span class="text-sm font-black text-medical-500">Sugerido: 200 Kits</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar derecha -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 italic">Directorio de Proveedores Jurídicos</h3>
                        <div class="space-y-4">
                            <?php foreach ($proveedores as $p): ?>
                                <div class="p-4 bg-gray-50 dark:bg-slate-900 border border-transparent hover:border-medical-500/30 rounded-2xl transition-all group cursor-pointer">
                                    <div class="text-[11px] font-black text-gray-800 dark:text-white group-hover:text-medical-500 transition-colors uppercase leading-tight italic"><?= strtoupper($p['razon_social']) ?></div>
                                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIT: <?= $p['nit'] ?></div>
                                    <p class="text-[8px] text-gray-350 dark:text-gray-500 font-medium mt-2 leading-relaxed"><?= $p['descripcion'] ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
</body>
</html>
