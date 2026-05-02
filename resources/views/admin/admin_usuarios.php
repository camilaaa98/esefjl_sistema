<?php
/**
 * Gestión de Talento Humano - ESE Fabio Jaramillo
 * Versión 9.5: Estabilización de Rutas Absolutas.
 */

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['Gerente', 'Regente Farmacia', 'Administrador'])) {
    die("Acceso restringido: Nivel de autoridad insuficiente.");
}

$root_path = dirname(__DIR__, 3);
require_once $root_path . '/app/config/Database.php';

$db = Database::getInstance();
$usuarios = $db->query("
    SELECT u.*, r.nombre as rol, s.nombre as sede 
    FROM usuarios u 
    JOIN roles r ON u.rol_id = r.id 
    JOIN sedes s ON u.sede_id = s.id
    ORDER BY u.apellidos ASC
")->fetchAll();

$roles = $db->query("SELECT * FROM roles ORDER BY nombre ASC")->fetchAll();
$sedes = $db->query("SELECT * FROM sedes ORDER BY nombre ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal - ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/../../../resources/views/partials/sidebar.php'; ?>

        <main class="content-area ">
            <header class="mb-12 flex justify-between items-center fade-in-institutional">
                <div>
                    <h2 class="text-3xl font-black text-[#111111] tracking-tight italic uppercase">Talento Humano <span class="text-[#d4af37]">ESEFJL</span></h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Control de Acceso y Roles de Seguridad</p>
                </div>
                <button class="px-8 py-4 bg-[#111111] text-[#d4af37] text-[10px] font-black rounded-2xl uppercase tracking-widest hover:scale-105 transition-all shadow-xl">Vincular Usuario</button>
            </header>

            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden fade-in-institutional">
                <div class="overflow-x-auto">
                    <table class="table-clinical w-full">
                        <thead>
                            <tr class="bg-[#1a202c] border-b-2 border-[#d4af37]">
                                <th class="px-8 py-6 text-left text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Colaborador</th>
                                <th class="px-8 py-6 text-left text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Usuario / Red</th>
                                <th class="px-8 py-6 text-left text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Rol / Perfil</th>
                                <th class="px-8 py-6 text-left text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Sede Asignada</th>
                                <th class="px-8 py-6 text-center text-[11px] font-black text-[#d4af37] uppercase tracking-[0.2em]">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($usuarios as $u): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-[#111111] uppercase italic leading-none mb-1"><?= $u['nombres'] . ' ' . $u['apellidos'] ?></p>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase italic">ID: <?= $u['documento'] ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold text-slate-600"><?= $u['username'] ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-black text-[#d4af37] text-[9px] font-black rounded-lg uppercase italic"><?= $u['rol'] ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase"><?= $u['sede'] ?></span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

