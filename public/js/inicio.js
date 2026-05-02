/**
 * Lógica del Inicio Pro - ESE Fabio Jaramillo v2.0
 * Web Animations API con movimientos orgánicos y naturales.
 */
document.addEventListener('DOMContentLoaded', () => {
    
    // Easing functions orgánicos
    const EASING = {
        expo: 'cubic-bezier(0.16, 1, 0.3, 1)',
        back: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
        spring: 'cubic-bezier(0.68, -0.15, 0.265, 1.25)',
        smooth: 'cubic-bezier(0.4, 0, 0.2, 1)'
    };

    // Delay orgánico no uniforme
    function organicDelay(index, base = 40, variance = 25) {
        return base + (index * 80) + (Math.random() * variance - variance/2);
    }

    // 1. Animación Escalonada de Filas de Tabla (WAAPI mejorado)
    const rows = document.querySelectorAll('.ips-row, tbody tr');
    rows.forEach((row, index) => {
        const delay = organicDelay(index, 50, 30);
        row.animate([
            { opacity: 0, transform: 'translateX(-25px) scale(0.98)' },
            { opacity: 0.6, transform: 'translateX(5px) scale(1.005)', offset: 0.6 },
            { opacity: 1, transform: 'translateX(0) scale(1)' }
        ], {
            duration: 550,
            delay: delay,
            easing: EASING.expo,
            fill: 'forwards'
        });
    });

    // 2. Efecto "Glow" orgánico en alertas críticas
    const criticalAlerts = document.querySelectorAll('.alert-pulse-medical, [class*="critico"], [class*="alert"]');
    criticalAlerts.forEach((alert, i) => {
        // Cada alerta tiene su propio timing para no lucir robótico
        const duration = 2000 + (i * 200);
        alert.animate([
            { filter: 'brightness(1) saturate(1)' },
            { filter: 'brightness(1.4) saturate(1.2)', offset: 0.4 },
            { filter: 'brightness(1.1) saturate(1.1)', offset: 0.7 },
            { filter: 'brightness(1) saturate(1)' }
        ], {
            duration: duration,
            iterations: Infinity,
            easing: EASING.smooth
        });
    });

    // 3. Cards de resumen con efecto 3D sutil
    const summaryCards = document.querySelectorAll('.card-clinical, .content-card');
    if (!window.matchMedia('(pointer: coarse)').matches) {
        summaryCards.forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -2;
                const rotateY = ((x - centerX) / centerX) * 2;

                card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
                card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) translateY(0)';
                setTimeout(() => card.style.transition = '', 400);
            });
        });
    }

    // 4. Botones de acción con ripple y feedback
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-primary-ESEFJL, button[type="submit"], .btn-gold');
        if (!btn) return;

        // Ripple effect orgánico
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            top: ${y}px;
            left: ${x}px;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.35);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        `;
        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        btn.appendChild(ripple);

        ripple.animate([
            { width: '0px', height: '0px', opacity: 0.5 },
            { width: '200px', height: '200px', opacity: 0 }
        ], { duration: 500, easing: EASING.expo }).onfinish = () => ripple.remove();

        // Animación de carga si es botón de acción
        if (btn.classList.contains('btn-primary-ESEFJL')) {
            const row = btn.closest('tr');
            if (row) {
                const municipio = row.querySelector('td:nth-child(2)')?.innerText || '';
                const insumo = row.querySelector('td:nth-child(3)')?.innerText || '';

                btn.style.pointerEvents = 'none';
                const originalText = btn.innerText;
                btn.innerText = 'PROCESANDO...';
                
                // Efecto de "procesando" con pulso
                btn.animate([
                    { opacity: 1 },
                    { opacity: 0.7 },
                    { opacity: 1 }
                ], { duration: 800, iterations: 1.5 });
                
                setTimeout(() => {
                    btn.innerHTML = '✓ DESPACHADO';
                    btn.style.background = '#2e7d32';
                    btn.style.transform = 'scale(0.98)';
                    
                    // Toast de confirmación
                    if (window.showToast) {
                        window.showToast(`Orden generada: ${insumo} → ${municipio}`, 'success');
                    }
                }, 1200);
            }
        }
    });

    // 5. Entrada animada de elementos del dashboard
    const dashboardSections = document.querySelectorAll('section');
    dashboardSections.forEach((section, i) => {
        const delay = 80 + (i * 120);
        section.animate([
            { opacity: 0, transform: 'translateY(30px)' },
            { opacity: 1, transform: 'translateY(0)' }
        ], {
            duration: 600,
            delay: delay,
            easing: EASING.expo,
            fill: 'both'
        });
    });

    // 6. Efecto de focus en inputs de filtro
    const filterInputs = document.querySelectorAll('input[type="text"], select');
    filterInputs.forEach((input) => {
        input.addEventListener('focus', () => {
            input.animate([
                { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(212, 175, 55, 0)' },
                { transform: 'scale(1.02)', boxShadow: '0 4px 12px rgba(212, 175, 55, 0.2)' }
            ], { duration: 250, easing: EASING.spring, fill: 'forwards' });
        });
        
        input.addEventListener('blur', () => {
            input.animate([
                { transform: 'scale(1.02)', boxShadow: '0 4px 12px rgba(212, 175, 55, 0.2)' },
                { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(212, 175, 55, 0)' }
            ], { duration: 200, easing: EASING.expo, fill: 'forwards' });
        });
    });

    console.group('FARMACIA ESEFJL - Diagnóstico UI v2.0');
    console.log('✅ WAAPI: Activo con easing orgánico');
    console.log('✅ Animaciones 3D: ' + (window.matchMedia('(pointer: coarse)').matches ? 'Deshabilitado (touch)' : 'Activo'));
    console.log('✅ Micro-interacciones: Cargadas');
    console.groupEnd();
});

