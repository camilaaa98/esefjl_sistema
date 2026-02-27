/**
 * Lógica de Autenticación Pro - ESE Fabio Jaramillo
 * Versión corregida: Sin alertas de prueba, conexión real.
 */
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const inputs = document.querySelectorAll('input');

    // Audio Context para sonidos de impacto premium
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    const playSound = (freq, type, dur) => {
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
        gain.gain.linearRampToValueAtTime(0, audioCtx.currentTime + dur);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + dur);
    };

    inputs.forEach(input => {
        input.addEventListener('focus', () => playSound(500, 'sine', 0.1));
    });

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = loginForm.querySelector('.btn-login');
        const span = btn.querySelector('span');
        const originalText = span.innerText;

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        btn.disabled = true;
        span.innerText = 'AUTENTICANDO...';
        playSound(800, 'square', 0.15);

        try {
            const response = await fetch('../core/auth_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();

            if (result.success) {
                span.innerText = 'ACCESO CONCEDIDO';
                playSound(1200, 'sine', 0.2);
                setTimeout(() => window.location.href = result.redirect, 800);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            playSound(150, 'sawtooth', 0.4);
            alert("Error: " + (error.message || "Credenciales inválidas"));
            span.innerText = originalText;
            btn.disabled = false;
        }
    });
});
