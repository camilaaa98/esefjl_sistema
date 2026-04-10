<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
$rol = $_SESSION['rol'];
if ($rol !== 'Subgerente de Servicios de Salud' && $rol !== 'Gerente') {
    header('Location: inicio.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Personal - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/Inicio.css">
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="w-full md:w-64 bg-white dark:bg-slate-800 p-6 flex flex-col border-r">
            <div class="flex items-center gap-3 mb-10">
                <img src="../img/logoesefjl.jpg" alt="Logo" class="w-10 h-10 rounded-lg">
                <h1 class="text-medical-500 font-extrabold text-lg">SISFARMA</h1>
            </div>
            <nav class="space-y-1">
                <a href="inicio.php" class="flex items-center gap-3 p-3 text-gray-600 rounded-xl">🏠 Inicio</a>
                <a href="asignacion_personal.php" class="flex items-center gap-3 p-3 bg-medical-50 text-medical-500 font-bold rounded-xl font-bold">🤝 Gestión Talento</a>
            </nav>
        </aside>

        <main class="flex-1 p-6 md:p-10 space-y-8">
            <header>
                <h2 class="text-3xl font-black text-gray-900 italic uppercase">Gestión de Talento Humano</h2>
                <p class="text-gray-500 text-sm font-medium">Asignación Directa de Personal de Salud a IPS Municipales</p>
            </header>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Seleccionar Funcionario</label>
                        <select class="w-full p-3 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-medical-500">
                            <option>Cargando lista de personal (500)...</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Destino (IPS)</label>
                        <select class="w-full p-3 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-medical-500">
                            <option>Solita</option>
                            <option>Solano</option>
                            <option>Milán</option>
                            <option>San Antonio de Getucha</option>
                            <option>Valparaíso</option>
                        </select>
                    </div>
                    <button class="md:col-span-2 py-3 bg-medical-500 text-white font-black rounded-2xl shadow-lg hover:shadow-medical-500/30 transition-all uppercase text-xs tracking-widest">
                        Confirmar Asignación
                    </button>
                </div>
            </div>

            <div class="bg-slate-900 p-8 rounded-3xl shadow-xl text-white">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Estado de Cobertura Salud</p>
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-black">98%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Solita</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black">85%</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Solano</p>
                    </div>
                    <!-- ... -->
                </div>
            </div>
        </main>
    </div>
</body>
</html>
