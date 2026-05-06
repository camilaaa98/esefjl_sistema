<?php

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
require_once __DIR__ . '/../../app/Controllers/PatientController.php';
require_once __DIR__ . '/../../app/Helpers/ViewHelper.php';

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
    $mensaje = ($result['status'] === 'success') ? "✅ " : "í¢ÂÅ’ ";
    $mensaje .= $result['message'];
}
?>
<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinculación de Pacientes - FARMACIA ESEFJL</title>
    <script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>
    <script src="<?= BASE_URL ?>/public/js/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        
        <!-- Main content -->
        <main class="content-area  fade-in-institutional flex justify-center items-center py-12">
            <div class="max-w-3xl w-full bg-white p-12 md:p-16 rounded-[2.5rem] shadow-2xl border border-slate-100 relative overflow-hidden">
                <!-- Decoración -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[#d4af37]/5 rounded-full blur-3xl"></div>

                <header class="text-center mb-12 relative z-10">
                    <div class="flex justify-center mb-8">
                        <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="Logo" class="w-20 h-20 rounded-3xl shadow-2xl ring-4 ring-[#d4af37]/20">
                    </div>
                    <span class="inline-block px-4 py-1.5 bg-[#111111] text-[#d4af37] text-[8px] font-black rounded-full uppercase tracking-[0.4em] mb-6 border border-[#d4af37]/30">Censo Poblacional Regional</span>
                    <h2 class="text-3xl font-black text-[#111111] italic uppercase tracking-tighter leading-none mb-3">Vinculación de <span class="text-[#d4af37]">Pacientes</span></h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Empadronamiento Digital Red IPS ESE Fabio Jaramillo</p>
                </header>
                
                <?php if($mensaje): ?>
                    <div class="mb-10 p-5 bg-[#111111] text-[#d4af37] border border-[#d4af37]/30 rounded-3xl text-center font-black text-[10px] tracking-widest uppercase animate-pulse shadow-xl">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
        
                <form method="POST" class="space-y-6 relative z-10">
                    <!-- Fila 1: Documento y Sisbén -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Documento de Identidad</label>
                            <input type="text" name="documento" placeholder="Ej: 1234567890" pattern="[0-9]+" title="Solo se permiten números"
                                class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 hover:border-slate-300" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Nivel Sisbén IV</label>
                            <input type="text" name="sisben" placeholder="Ej: A1, B2, C3..." oninput="this.value = this.value.toUpperCase()"
                                class="w-full p-4 bg-white border-2 border-[#111111]/10 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-[#111111] placeholder:text-slate-300 hover:border-[#d4af37]/40">
                        </div>
                    </div>
                    
                    <!-- Fila 2: Nombres y Apellidos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Nombres Propios</label>
                            <input type="text" name="nombres" placeholder="Ej: JUAN CARLOS" oninput="this.value = this.value.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ ]/g, '')"
                                class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 hover:border-slate-300" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Apellidos Consanguíneos</label>
                            <input type="text" name="apellidos" placeholder="Ej: PÉREZ GARCíA" oninput="this.value = this.value.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ ]/g, '')"
                                class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 hover:border-slate-300" required>
                        </div>
                    </div>
                    
                    <!-- Fila 3: Celular -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Teléfono Móvil (SMS)</label>
                        <input type="text" name="celular" placeholder="Ej: 3123456789" pattern="[0-9]+" title="Solo se permiten números"
                            class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 hover:border-slate-300" required>
                    </div>
                    
                    <!-- Fila 4: EPS y Régimen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">EPS Responsable</label>
                            <select name="eps" class="w-full p-4 bg-white border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 cursor-pointer hover:border-slate-300" required>
                                <option value="" disabled selected>Seleccione EPS...</option>
                                <option value="Nueva EPS">NUEVA EPS</option>
                                <option value="Sanitas">SANITAS</option>
                                <option value="Asmet Salud">ASMET SALUD</option>
                                <option value="Fuerzas Militares">FUERZAS MILITARES</option>
                                <option value="Policía Nacional">POLICíA NACIONAL</option>
                                <option value="Coomeva">COOMEVA</option>
                                <option value="Sura">SURA</option>
                                <option value="Famisanar">FAMISANAR</option>
                                <option value="Aliansalud">ALIANSALUD</option>
                                <option value="Compensar">COMPENSAR</option>
                                <option value="Salud Total">SALUD TOTAL</option>
                                <option value="Cafesalud">CAFESALUD</option>
                                <option value="Colsanitas">COLSANITAS</option>
                                <option value="Medimás">MEDIMAS</option>
                                <option value="Capital Salud">CAPITAL SALUD</option>
                                <option value="Servicio Occidental de Salud">SOS</option>
                                <option value="Convida">CONVIDA</option>
                                <option value="Humana Vivir">HUMANA VIVIR</option>
                                <option value="Cruz Blanca">CRUZ BLANCA</option>
                                <option value="Mallamas">MALLAMAS</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Régimen</label>
                            <select name="regimen" class="w-full p-4 bg-white border-2 border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all text-sm font-bold text-slate-800 cursor-pointer hover:border-slate-300" required>
                                <option value="" disabled selected>Seleccione régimen...</option>
                                <option value="CONTRIBUTIVO">CONTRIBUTIVO (Copago)</option>
                                <option value="SUBSIDIADO">SUBSIDIADO (Sin copago)</option>
                                <option value="VINCULADO">VINCULADO</option>
                                <option value="ESPECIAL">RÉGIMEN ESPECIAL</option>
                                <option value="PARTICULAR">PARTICULAR</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Checkbox Desplazado -->
                    <div class="p-4 bg-gradient-to-r from-slate-50 to-white rounded-2xl border-2 border-slate-200 hover:border-[#d4af37]/50 transition-all group/box cursor-pointer">
                        <label class="flex items-center gap-4 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="es_desplazado" class="peer sr-only">
                                <div class="w-6 h-6 rounded-lg border-2 border-slate-300 peer-checked:bg-[#d4af37] peer-checked:border-[#d4af37] transition-all"></div>
                                <svg class="absolute top-1 left-1 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.15em]">¿Población Desplazada / Víctima del Conflicto?</span>
                                <span class="text-[9px] text-[#d4af37] font-semibold">Exención de pagos según Ley 1448 de 2011</span>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Botón Submit -->
                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-[#111111] to-[#1a1a1a] text-white font-black rounded-2xl shadow-lg shadow-black/20 transition-all transform hover:scale-[1.02] hover:shadow-xl active:scale-[0.98] uppercase text-sm tracking-[0.3em] border-2 border-transparent hover:border-[#d4af37] hover:text-[#d4af37] relative overflow-hidden group">
                        <span class="relative z-10">Validar y Vincular al Sistema</span>
                        <div class="absolute inset-0 bg-[#d4af37] transform -translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>
                    </button>
                </form>

                <footer class="mt-10 pt-6 border-t border-slate-200">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] text-center">
                        Sistema de Gestión Farmacéutica — ESE Fabio Jaramillo
                    </p>
                    <p class="text-[9px] text-slate-300 text-center mt-1">Versión 2.0 — 2024</p>
                </footer>
            </div>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/animations.js" defer></script>
    
    <!-- Validaciones de Formulario -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para mostrar error
        function mostrarError(input, mensaje) {
            // Remover error anterior si existe
            const errorAnterior = input.parentNode.querySelector('.error-mensaje');
            if (errorAnterior) errorAnterior.remove();
            
            // Crear mensaje de error
            const error = document.createElement('p');
            error.className = 'error-mensaje text-red-500 text-xs font-bold mt-1';
            error.textContent = mensaje;
            input.parentNode.appendChild(error);
            input.classList.add('border-red-500');
            
            // Remover después de 3 segundos
            setTimeout(() => {
                error.remove();
                input.classList.remove('border-red-500');
            }, 3000);
        }
        
        // Función para limpiar error
        function limpiarError(input) {
            const error = input.parentNode.querySelector('.error-mensaje');
            if (error) error.remove();
            input.classList.remove('border-red-500');
        }
        
        // 1. Campo Documento - Solo números
        const inputDocumento = document.querySelector('input[name="documento"]');
        if (inputDocumento) {
            inputDocumento.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                if (!/[0-9]/.test(char)) {
                    e.preventDefault();
                    mostrarError(this, 'Solo se permiten números');
                }
            });
            
            inputDocumento.addEventListener('input', function() {
                limpiarError(this);
                // Remover cualquier letra que se haya colado
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            inputDocumento.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                const soloNumeros = texto.replace(/[^0-9]/g, '');
                this.value = soloNumeros;
                if (soloNumeros !== texto) {
                    mostrarError(this, 'Se eliminaron caracteres no numéricos');
                }
            });
        }
        
        // 2. Campo Celular - Solo números
        const inputCelular = document.querySelector('input[name="celular"]');
        if (inputCelular) {
            inputCelular.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                if (!/[0-9]/.test(char)) {
                    e.preventDefault();
                    mostrarError(this, 'Solo se permiten números');
                }
            });
            
            inputCelular.addEventListener('input', function() {
                limpiarError(this);
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            inputCelular.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                const soloNumeros = texto.replace(/[^0-9]/g, '');
                this.value = soloNumeros;
            });
        }
        
        // 3. Campo Nombres - Solo letras y espacios, siempre mayúsculas
        const inputNombres = document.querySelector('input[name="nombres"]');
        if (inputNombres) {
            inputNombres.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                // Permitir letras (incluyendo tildes), espacios, y teclas de control
                if (!/[a-zA-Záéíóúíí‰íí“íšñÑ\s]/.test(char) && e.which !== 8 && e.which !== 0) {
                    e.preventDefault();
                    mostrarError(this, 'Solo se permiten letras');
                }
            });
            
            inputNombres.addEventListener('input', function() {
                limpiarError(this);
                // Convertir a mayúsculas y eliminar caracteres no permitidos
                this.value = this.value.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ\s]/g, '');
            });
            
            inputNombres.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                const soloLetras = texto.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ\s]/g, '');
                this.value = soloLetras;
                if (soloLetras !== texto.toUpperCase()) {
                    mostrarError(this, 'Se eliminaron caracteres no válidos');
                }
            });
        }
        
        // 4. Campo Apellidos - Solo letras y espacios, siempre mayúsculas
        const inputApellidos = document.querySelector('input[name="apellidos"]');
        if (inputApellidos) {
            inputApellidos.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                if (!/[a-zA-Záéíóúíí‰íí“íšñÑ\s]/.test(char) && e.which !== 8 && e.which !== 0) {
                    e.preventDefault();
                    mostrarError(this, 'Solo se permiten letras');
                }
            });
            
            inputApellidos.addEventListener('input', function() {
                limpiarError(this);
                this.value = this.value.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ\s]/g, '');
            });
            
            inputApellidos.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                const soloLetras = texto.toUpperCase().replace(/[^A-Zíí‰íí“íšÑ\s]/g, '');
                this.value = soloLetras;
            });
        }
        
        // 5. Campo Sisbén - Letras y números, siempre mayúsculas
        const inputSisben = document.querySelector('input[name="sisben"]');
        if (inputSisben) {
            inputSisben.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }
        
        // Validación antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            let errores = [];
            
            // Validar documento
            if (inputDocumento && !/^\d+$/.test(inputDocumento.value)) {
                errores.push('El documento debe contener solo números');
                mostrarError(inputDocumento, 'Solo números permitidos');
            }
            
            // Validar celular
            if (inputCelular && !/^\d+$/.test(inputCelular.value)) {
                errores.push('El celular debe contener solo números');
                mostrarError(inputCelular, 'Solo números permitidos');
            }
            
            // Validar nombres
            if (inputNombres && !/^[A-Zíí‰íí“íšÑ\s]+$/.test(inputNombres.value)) {
                errores.push('Los nombres solo deben contener letras');
                mostrarError(inputNombres, 'Solo letras permitidas');
            }
            
            // Validar apellidos
            if (inputApellidos && !/^[A-Zíí‰íí“íšÑ\s]+$/.test(inputApellidos.value)) {
                errores.push('Los apellidos solo deben contener letras');
                mostrarError(inputApellidos, 'Solo letras permitidas');
            }
            
            if (errores.length > 0) {
                e.preventDefault();
                // Mostrar alerta general
                const alerta = document.createElement('div');
                alerta.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 font-bold text-sm';
                alerta.innerHTML = 'âŒ ' + errores.join('<br>');
                document.body.appendChild(alerta);
                setTimeout(() => alerta.remove(), 5000);
            }
        });
    });
    </script>
</body>
</html>

