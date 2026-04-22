<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/Controllers/PatientController.php';
require_once __DIR__ . '/../core/ViewHelper.php';

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientCtrl = new PatientController();
    $result = $patientCtrl->register([
        'nombre_completo' => $_POST['nombres'] . ' ' . ($_POST['apellidos'] ?? ''),
        'tipo_documento' => 'CC', // Valor por defecto para simplificar
        'numero_documento' => $_POST['documento'],
        'fecha_nacimiento' => '1900-01-01', // Valor temporal
        'genero' => 'O', // Valor temporal
        'direccion' => 'Sede: ' . ($_SESSION['sede'] ?? 'N/A'),
        'telefono' => $_POST['celular'],
        'entidad_salud' => $_POST['eps'] ?? '',
        'sede_id' => $_SESSION['sede_id']
    ]);
    $mensaje = ($result['status'] === 'success') ? "âœ… " : "âŒ ";
    $mensaje .= $result['message'];
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinculaciÃ³n de Pacientes - SISFARMA PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
    <div class="main-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <!-- Main content -->
        <main class="content-area fade-in-institutional flex justify-center items-center py-12">
            <div class="max-w-2xl w-full bg-white p-10 md:p-16 rounded-[4rem] shadow-2xl border border-slate-100 relative overflow-hidden">
                <!-- DecoraciÃ³n -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[#d4af37]/5 rounded-full blur-3xl"></div>

                <header class="text-center mb-12 relative z-10">
                    <div class="flex justify-center mb-8">
                        <img src="../img/logoesefjl.jpg" alt="Logo" class="w-20 h-20 rounded-3xl shadow-2xl ring-4 ring-[#d4af37]/20">
                    </div>
                    <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-6 border border-[#d4af37]/30">Censo Poblacional Regional</span>
                    <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter leading-none mb-3">VinculaciÃ³n de <span class="text-[#d4af37]">Pacientes</span></h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Empadronamiento Digital Red IPS ESE Fabio Jaramillo</p>
                </header>
                
                <?php if($mensaje): ?>
                    <div class="mb-10 p-5 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-pulse shadow-xl">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
        
                <form method="POST" class="space-y-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Documento de Identidad</label>
                            <input type="text" name="documento" placeholder="ID CIUDADANO" 
                                class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase placeholder:text-slate-200 shadow-inner" required>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nivel SisbÃ©n IV</label>
                            <input type="text" name="sisben" placeholder="CATEGORÃA (Ej: A1)" 
                                class="w-full p-5 bg-white border border-[#d4af37]/30 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#d4af37] uppercase shadow-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nombres Propios</label>
                            <input type="text" name="nombres" placeholder="NOMBRES COMPLETOS" 
                                class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic placeholder:text-slate-200 shadow-inner" required>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Apellidos ConsanguÃ­neos</label>
                            <input type="text" name="apellidos" placeholder="APELLIDOS COMPLETOS" 
                                class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase italic placeholder:text-slate-200 shadow-inner" required>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Terminal MÃ³vil de NotificaciÃ³n (SMS)</label>
                        <input type="text" name="celular" placeholder="NÃšMERO DE TELÃ‰FONO" 
                            class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-sm font-black text-[#111111] placeholder:text-slate-200 shadow-inner" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Entidad de Salud Responsable (EPS)</label>
                            <select name="eps" class="w-full p-5 bg-white border border-[#d4af37]/20 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase cursor-pointer" required>
                                <option value="" class="italic opacity-30">--- SELECCIONAR EPS ---</option>
                                <option value="Nueva EPS">NUEVA EPS</option>
                                <option value="Sanitas">SANITAS</option>
                                <option value="Asmet Salud">ASMET SALUD</option>
                                <option value="Fuerzas Militares">FUERZAS MILITARES</option>
                                <option value="PolicÃ­a Nacional">POLICÃA NACIONAL</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">RÃ©gimen JurÃ­dico</label>
                            <select name="regimen" class="w-full p-5 bg-white border border-[#d4af37]/20 rounded-3xl outline-none focus:ring-4 focus:ring-[#d4af37]/10 focus:border-[#d4af37] transition-all text-xs font-black text-[#111111] uppercase cursor-pointer" required>
                                <option value="" class="italic opacity-30">--- SELECCIONAR RÃ‰GIMEN ---</option>
                                <option value="CONTRIBUTIVO">CONTRIBUTIVO (GENERA COPAGO)</option>
                                <option value="SUBSIDIADO">SUBSIDIADO (EXENTO)</option>
                                <option value="ESPECIAL">ESPECIAL JURÃDICO</option>
                            </select>
                        </div>
                    </div>
        
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 hover:border-[#d4af37]/40 transition-all group/box">
                        <label class="flex items-center gap-5 cursor-pointer">
                            <input type="checkbox" name="es_desplazado" class="w-6 h-6 rounded-lg border-slate-200 text-[#111111] focus:ring-[#d4af37]">
                            <span class="text-[9px] font-black text-slate-400 group-hover/box:text-[#111111] transition-colors uppercase tracking-[0.2em] italic">
                                Â¿PoblaciÃ³n Desplazada / VÃ­ctima? <br>
                                <span class="text-[8px] text-[#d4af37]">(ExenciÃ³n de pagos Ley 1448)</span>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-6 bg-[#111111] text-white font-black rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] transition-all transform hover:scale-[1.02] active:scale-95 uppercase text-[11px] tracking-[0.4em] mt-8 border border-transparent hover:border-[#d4af37]/40 hover:text-[#d4af37]">
                        Validar y Vincular al Sistema Ã‰lite
                    </button>
                </form>

                <footer class="mt-12 pt-8 border-t border-slate-50 text-[8px] font-bold text-slate-300 uppercase tracking-[0.5em] text-center italic">
                    PROCEDIMIENTO DE VINCULACIÃ“N â€” SISFARMA Ã‰LITE v7.5
                </footer>
            </div>
        </main>
    </div>
    <script src="../assets/js/inicio.js"></script>
    </div>
    <script src="../assets/js/theme-toggle.js"></script>
    <script src="../assets/js/animations.js" defer></script>
</body>
</html>
