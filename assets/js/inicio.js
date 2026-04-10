/**
 * Lógica del Inicio Pro - ESE Fabio Jaramillo
 * Implementación de WAAPI (Web Animation API) para interactividad Elite.
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Animación Escalonada de Filas de Tabla (WAAPI)
    const rows = document.querySelectorAll('.ips-row');
    rows.forEach((row, index) => {
        row.animate([
            { opacity: 0, transform: 'translateX(-20px)' },
            { opacity: 1, transform: 'translateX(0)' }
        ], {
            duration: 600,
            delay: 400 + (index * 100),
            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
            fill: 'forwards'
        });
    });

    // 2. Efecto de "Glow" en Alertas Críticas (JS)
    const criticalAlerts = document.querySelectorAll('.alert-pulse-medical');
    criticalAlerts.forEach(alert => {
        alert.animate([
            { filter: 'brightness(1)' },
            { filter: 'brightness(1.5)', color: '#ff5252' },
            { filter: 'brightness(1)' }
        ], {
            duration: 2000,
            iterations: Infinity,
            easing: 'ease-in-out'
        });
    });

    // 3. Manejo de Despacho Regional con Feedback Premium
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-primary-elite');
        if (btn) {
            const row = btn.closest('tr');
            const municipio = row.querySelector('td:nth-child(2)').innerText;
            const insumo = row.querySelector('td:nth-child(3)').innerText;

            btn.style.pointerEvents = 'none';
            btn.innerText = 'PROCESANDO...';
            
            // Animación de éxito local
            setTimeout(() => {
                btn.innerHTML = '✅ DESPACHADO';
                btn.style.background = '#388e3c';
                console.log(`Logística: Orden generada para ${insumo} en ${municipio}`);
            }, 1200);
        }
    });

    console.group('Sisfarma Pro - Diagnóstico UI');
    console.log('✅ WAAPI: Activo');
    console.log('✅ Rediseño Elite: Aplicado');
    console.groupEnd();
});

