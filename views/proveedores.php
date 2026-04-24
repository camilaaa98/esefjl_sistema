<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Infrastructure/Database.php';
require_once __DIR__ . '/../core/Controllers/InventoryController.php';

$db = Database::getInstance();
$rol = $_SESSION['rol'];
$vencidos_count = count(InventoryController::getInstance()->getExpiredInventory());

// Solo directivos pueden ver proveedores
if (!in_array($rol, ['Gerente', 'Regente Farmacia', 'Subgerente Administrativa y Financiera'])) {
    header('Location: inicio.php');
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
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto fade-in-institutional">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-black text-[#111111] tracking-tight italic uppercase">Gestión Abastecimiento <span class="text-[#d4af37]">Global</span></h2>
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">Directorio Jurídico y Control de Cartera SISFARMA</p>
                </div>
                <button class="btn-institutional">
                    <span class="text-lg">ðŸ›’+</span>
                    Registrar Nueva Compra
                </button>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-[#111111] flex justify-between items-center">
                            <span class="text-[10px] font-black text-[#d4af37] uppercase tracking-widest leading-none underline decoration-2 underline-offset-4">Historial de Compras CEDIS</span>
                            <span class="text-[9px] font-black text-slate-400 uppercase italic">Validación Tesorería Central</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Operativa</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Entidad Proveedora</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Suministros</th>
                                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado Auditoría</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php if (empty($compras)): ?>
                                        <tr><td colspan="4" class="px-6 py-24 text-center text-slate-300 italic uppercase font-black text-xs tracking-widest opacity-30">No se registran compras en este ciclo.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($compras as $c): ?>
                                    <tr class="hover:bg-slate-50 transition-all">
                                        <td class="px-6 py-5 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                            <?= date('d/M/Y', strtotime($c['fecha_compra'])) ?>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-sm font-black text-[#111111] uppercase italic border-b border-[#d4af37]/30"><?= strtoupper($c['razon_social']) ?></span>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-black text-[#111111] tabular-nums">
                                            $<?= number_format($c['total'], 0) ?>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase <?= $c['estado_pago'] == 'PAGADO' ? 'bg-[#111111] text-[#d4af37] border border-[#d4af37]/30' : 'bg-red-50 text-red-600 border border-red-100' ?> shadow-sm">
                                                <?= $c['estado_pago'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sugerencia de Reposición a Proveedor Premium -->
                    <div class="bg-[#111111] rounded-[2.5rem] p-10 text-white border border-[#d4af37]/20 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 text-white/5 text-9xl font-black rotate-12 group-hover:rotate-0 transition-transform duration-700">BOX</div>
                        <h4 class="text-[10px] font-black text-[#d4af37] uppercase tracking-[0.4em] mb-8 flex items-center gap-3">
                            <span class="w-2 h-2 bg-[#d4af37] rounded-full animate-pulse"></span>
                            Insumos Críticos Pendientes de Compra
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-5 bg-white/5 rounded-2xl border border-white/10 hover:border-[#d4af37]/40 transition-all group/item">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-white italic group-hover/item:text-[#d4af37]">Cilindros de Oxígeno Medicinal</p>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tighter mt-1">Proveedor Sugerido: CLINICAL GAS SAS</p>
                                </div>
                                <span class="text-sm font-black text-[#d4af37]">50 <span class="text-[9px] opacity-50 uppercase">Balas</span></span>
                            </div>
                            <div class="flex items-center justify-between p-5 bg-white/5 rounded-2xl border border-white/10 hover:border-[#d4af37]/40 transition-all group/item">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-white italic group-hover/item:text-[#d4af37]">Kits Citología Estéril</p>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tighter mt-1">Proveedor Sugerido: MEDICAL SURGICAL LTDA</p>
                                </div>
                                <span class="text-sm font-black text-[#d4af37]">200 <span class="text-[9px] opacity-50 uppercase">Kits</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar derecha Premium -->
                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl border border-slate-100">
                        <h3 class="text-[10px] font-black text-[#111111] uppercase tracking-[0.3em] mb-8 italic border-b-2 border-[#d4af37] pb-3">Directorio Jurídico</h3>
                        <div class="space-y-5">
                            <?php foreach ($proveedores as $p): ?>
                                <div class="p-5 bg-slate-50 border-l-4 border-l-[#111111] hover:border-l-[#d4af37] rounded-2xl transition-all group cursor-pointer hover:shadow-lg">
                                    <div class="text-[10px] font-black text-[#111111] uppercase leading-tight italic mb-1 transition-colors group-hover:text-[#d4af37]"><?= strtoupper($p['razon_social']) ?></div>
                                    <div class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">NIT: <?= $p['nit'] ?></div>
                                    <p class="text-[9px] text-slate-500 font-bold leading-relaxed border-t border-slate-100 pt-3 group-hover:text-slate-700"><?= $p['descripcion'] ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
