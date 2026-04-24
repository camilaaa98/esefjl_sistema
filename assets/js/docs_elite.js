/**
 * ESEFJL DOCUMENTATION ENGINE 4.0
 * Implementación de WAAPI e Intersection Observer para interactividad premium.
 */
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Trigger WAAPI animation
                animateSection(entry.target);
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Animate only once
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => {
        section.classList.add('reveal-section');
        observer.observe(section);
    });

    function animateSection(el) {
        el.animate([
            { opacity: 0, transform: 'translateY(40px) scale(0.95)' },
            { opacity: 1, transform: 'translateY(0) scale(1)' }
        ], {
            duration: 800,
            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
            fill: 'forwards'
        });
    }

    // Efecto de Parallax suave en el fondo
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        document.body.style.backgroundPositionY = -(scrolled * 0.1) + 'px';
    });
});
