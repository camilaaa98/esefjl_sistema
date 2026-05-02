/**
 * Lógica de Autenticación Pro - ESE Fabio Jaramillo v2.0
 * Animaciones orgánicas y efectos visuales mejorados.
 */
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const inputs = document.querySelectorAll('input');
    const container = loginForm?.closest('.max-w-md') || document.body;

    // Easing functions orgánicos
    const EASING = {
        expo: 'cubic-bezier(0.16, 1, 0.3, 1)',
        spring: 'cubic-bezier(0.68, -0.15, 0.265, 1.25)',
        smooth: 'cubic-bezier(0.4, 0, 0.2, 1)'
    };

    // Audio Context diferido para evitar advertencias de autoplay
    let audioCtx = null;

    const playSound = (freq, type, dur, vol = 0.03) => {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(freq * 0.8, audioCtx.currentTime + dur);
        gain.gain.setValueAtTime(vol, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + dur);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + dur);
    };

    // 1. Animación de entrada del formulario
    const formElements = container.querySelectorAll('div, input, button, h2, p');
    formElements.forEach((el, i) => {
        el.animate([
            { opacity: 0, transform: 'translateY(30px) scale(0.95)' },
            { opacity: 0.6, transform: 'translateY(10px) scale(1.01)', offset: 0.5 },
            { opacity: 1, transform: 'translateY(0) scale(1)' }
        ], {
            duration: 700,
            delay: 100 + (i * 80),
            easing: EASING.expo,
            fill: 'both'
        });
    });

    // 2. Efecto focus en inputs con animación orgánica
    inputs.forEach((input) => {
        input.addEventListener('focus', () => {
            playSound(600, 'sine', 0.1, 0.02);
            
            input.animate([
                { transform: 'scale(1)', boxShadow: '0 0 0 rgba(212, 175, 55, 0)' },
                { transform: 'scale(1.02)', boxShadow: '0 0 20px rgba(212, 175, 55, 0.3)' }
            ], {
                duration: 300,
                easing: EASING.spring,
                fill: 'forwards'
            });
        });

        input.addEventListener('blur', () => {
            input.animate([
                { transform: 'scale(1.02)', boxShadow: '0 0 20px rgba(212, 175, 55, 0.3)' },
                { transform: 'scale(1)', boxShadow: '0 0 0 rgba(212, 175, 55, 0)' }
            ], {
                duration: 250,
                easing: EASING.expo,
                fill: 'forwards'
            });
        });
    });

    // 3. Botón con ripple orgánico
    const btn = loginForm?.querySelector('button[type="submit"]');
    if (btn) {
        btn.addEventListener('click', (e) => {
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
                background: rgba(0,0,0,0.2);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                pointer-events: none;
            `;
            btn.style.position = 'relative';
            btn.style.overflow = 'hidden';
            btn.appendChild(ripple);

            ripple.animate([
                { width: '0px', height: '0px', opacity: 0.6 },
                { width: '250px', height: '250px', opacity: 0 }
            ], { duration: 500, easing: EASING.expo }).onfinish = () => ripple.remove();
        });
    }

    // 4. Submit con animaciones de estado
    loginForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const loginError = document.getElementById('login-error');
        const originalText = submitBtn.innerText;

        const username = document.getElementById('username')?.value;
        const password = document.getElementById('password')?.value;

        const csrf_token = document.querySelector('input[name="csrf_token"]')?.value;

        // Animación de loading
        submitBtn.disabled = true;
        submitBtn.innerText = 'AUTENTICANDO...';
        loginError?.classList.add('hidden');
        playSound(800, 'square', 0.15, 0.02);

        // Pulso de loading orgánico
        const loadingPulse = submitBtn.animate([
            { opacity: 1, transform: 'scale(1)' },
            { opacity: 0.7, transform: 'scale(0.98)' },
            { opacity: 1, transform: 'scale(1)' }
        ], { duration: 1000, iterations: Infinity, easing: EASING.smooth });

        try {
            const response = await fetch('do-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password, csrf_token })
            });

            const result = await response.json();
            loadingPulse.cancel();

            if (result.success) {
                // Éxito: animación de transición
                submitBtn.innerText = 'ACCESO CONCEDIDO';
                playSound(1200, 'sine', 0.3, 0.03);

                // Efecto de éxito visual
                submitBtn.animate([
                    { transform: 'scale(1)', boxShadow: '0 0 0 rgba(212, 175, 55, 0)' },
                    { transform: 'scale(1.05)', boxShadow: '0 0 30px rgba(212, 175, 55, 0.5)' },
                    { transform: 'scale(1)', boxShadow: '0 0 50px rgba(212, 175, 55, 0.8)' }
                ], { duration: 600, easing: EASING.spring });

                // Fade out del formulario
                setTimeout(() => {
                    container.animate([
                        { opacity: 1, transform: 'scale(1)' },
                        { opacity: 0, transform: 'scale(1.1)' }
                    ], { duration: 400, easing: EASING.expo }).onfinish = () => {
                        window.location.href = result.redirect;
                    };
                }, 800);

            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            loadingPulse.cancel();
            playSound(200, 'sawtooth', 0.4, 0.03);

            // Animación de error (shake orgánico)
            submitBtn.innerText = 'ERROR';
            container.animate([
                { transform: 'translateX(0)' },
                { transform: 'translateX(-8px)' },
                { transform: 'translateX(8px)' },
                { transform: 'translateX(-6px)' },
                { transform: 'translateX(6px)' },
                { transform: 'translateX(-3px)' },
                { transform: 'translateX(0)' }
            ], { duration: 500, easing: EASING.expo });

            loginError.innerText = error.message || "Credenciales inválidas";
            loginError.classList.remove('hidden');

            // Reset del botón
            setTimeout(() => {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }, 1500);
        }
    });

    // 5. Efecto de "flotación" sutil en el logo
    const logo = document.querySelector('img[alt*="Logo"]');
    if (logo) {
        logo.animate([
            { transform: 'translateY(0) rotate(0deg)' },
            { transform: 'translateY(-4px) rotate(0.5deg)' },
            { transform: 'translateY(0) rotate(0deg)' },
            { transform: 'translateY(-2px) rotate(-0.3deg)' },
            { transform: 'translateY(0) rotate(0deg)' }
        ], {
            duration: 5000,
            iterations: Infinity,
            easing: EASING.smooth
        });
    }

    // 6. Efecto parallax en fondos decorativos
    const bgElements = document.querySelectorAll('.absolute.rounded-full');
    if (!window.matchMedia('(pointer: coarse)').matches) {
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;

            bgElements.forEach((el, i) => {
                const factor = (i + 1) * 0.5;
                el.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
                el.style.transition = 'transform 0.3s ease-out';
            });
        });
    }
});
