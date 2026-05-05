/**
 * FARMACIA ESEFJL — Docs Premium Animations v3.0
 * CSS Transitions + CSS Animations + CSS Transforms + Web Animations API
 */
(function () {
    'use strict';

    const EASE_OUT    = 'cubic-bezier(0, 0, 0.2, 1)';
    const EASE_SPRING = 'cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    const EASE_STD    = 'cubic-bezier(0.4, 0, 0.2, 1)';

    /* ══════════════════════════════════════════
       1. PROGRESS BAR — línea superior al scroll
    ══════════════════════════════════════════ */
    const bar = document.createElement('div');
    bar.id = 'esefjl-progress';
    bar.style.cssText = `
        position: fixed; top: 0; left: 0; height: 3px; width: 0%;
        background: linear-gradient(90deg, #00695c, #d4af37, #00695c);
        background-size: 200% 100%;
        z-index: 9999; transition: width 0.1s linear;
        animation: shimmerBar 3s linear infinite;
        pointer-events: none;
    `;
    document.documentElement.prepend(bar);

    window.addEventListener('scroll', () => {
        const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        bar.style.width = Math.min(pct, 100) + '%';
    });

    /* ══════════════════════════════════════════
       2. NAV ACTIVO — highlight del link según scroll
    ══════════════════════════════════════════ */
    const navLinks = document.querySelectorAll('.manual-nav a[href^="#"]');
    const sections = [];
    navLinks.forEach(link => {
        const target = document.querySelector(link.getAttribute('href'));
        if (target) sections.push({ link, target });
    });

    if (sections.length) {
        const activateLink = () => {
            let current = sections[0];
            sections.forEach(({ link, target }) => {
                const rect = target.getBoundingClientRect();
                if (rect.top <= 120) current = { link, target };
            });
            navLinks.forEach(l => {
                l.style.removeProperty('font-weight');
                l.style.removeProperty('color');
                l.style.removeProperty('border-left');
                l.style.removeProperty('padding-left');
            });
            if (current) {
                current.link.style.fontWeight = '800';
                current.link.style.color = '#00695c';
                current.link.style.borderLeft = '3px solid #d4af37';
                current.link.style.paddingLeft = '12px';
            }
        };
        window.addEventListener('scroll', activateLink, { passive: true });
        activateLink();
    }

    /* ══════════════════════════════════════════
       3. ENTRADA — IntersectionObserver para scroll reveal
    ══════════════════════════════════════════ */
    function revealOnScroll(selector, keyframes, opts = {}) {
        const els = document.querySelectorAll(selector);
        if (!els.length) return;
        const observer = new IntersectionObserver(entries => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    const delay = (opts.stagger || 0) * i;
                    entry.target.animate(keyframes, {
                        duration: opts.duration || 650,
                        delay: (opts.delay || 0) + delay,
                        easing: opts.easing || EASE_OUT,
                        fill: 'both'
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: opts.threshold || 0.1 });
        els.forEach(el => {
            el.style.opacity = '0';
            observer.observe(el);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {

        /* ─ Header: caída desde arriba ─ */
        document.querySelectorAll('.manual-header, header').forEach(h => {
            h.animate(
                [{ opacity: 0, transform: 'translateY(-30px) scale(0.98)' },
                 { opacity: 1, transform: 'translateY(0) scale(1)' }],
                { duration: 800, easing: EASE_OUT, fill: 'both' }
            );
        });

        /* ─ Sidebar: deslizar desde izquierda ─ */
        document.querySelectorAll('.manual-nav').forEach(nav => {
            nav.animate(
                [{ opacity: 0, transform: 'translateX(-40px)' },
                 { opacity: 1, transform: 'translateX(0)' }],
                { duration: 700, delay: 100, easing: EASE_OUT, fill: 'both' }
            );
        });

        /* ─ Secciones: fadeInUp escalonado ─ */
        document.querySelectorAll('section.content-card').forEach((sec, i) => {
            sec.animate(
                [{ opacity: 0, transform: 'translateY(40px)' },
                 { opacity: 1, transform: 'translateY(0)' }],
                { duration: 700, delay: 100 + i * 90, easing: EASE_OUT, fill: 'both' }
            );
        });

        /* ─ Tech-tags: pop con spring ─ */
        document.querySelectorAll('.tech-tag').forEach((tag, i) => {
            tag.animate(
                [{ opacity: 0, transform: 'scale(0.5) rotate(-8deg)' },
                 { opacity: 1, transform: 'scale(1.08) rotate(2deg)' },
                 { opacity: 1, transform: 'scale(1) rotate(0deg)' }],
                { duration: 500, delay: 400 + i * 80, easing: EASE_SPRING, fill: 'both' }
            );
        });

        /* ─ TOC items: deslizar desde izquierda ─ */
        document.querySelectorAll('.toc-item').forEach((item, i) => {
            item.animate(
                [{ opacity: 0, transform: 'translateX(-20px)' },
                 { opacity: 1, transform: 'translateX(0)' }],
                { duration: 450, delay: 300 + i * 55, easing: EASE_OUT, fill: 'both' }
            );
        });

        /* ─ Imágenes: fade + rise ─ */
        revealOnScroll('.responsive-img',
            [{ opacity: 0, transform: 'translateY(30px) scale(0.97)' },
             { opacity: 1, transform: 'translateY(0) scale(1)' }],
            { duration: 750, stagger: 60 }
        );

        /* ─ Alertas: zoom con bounce ─ */
        revealOnScroll('.alert, .alert-important, .alert-info',
            [{ opacity: 0, transform: 'scale(0.9) translateX(-10px)' },
             { opacity: 1, transform: 'scale(1.02) translateX(0)' },
             { opacity: 1, transform: 'scale(1) translateX(0)' }],
            { duration: 600, stagger: 80 }
        );

        /* ─ Listas de pasos: fadeInRight ─ */
        revealOnScroll('.step, ol li, ul li',
            [{ opacity: 0, transform: 'translateX(20px)' },
             { opacity: 1, transform: 'translateX(0)' }],
            { duration: 500, stagger: 40 }
        );

        /* ─ Tablas: scale desde 0.93 ─ */
        revealOnScroll('table',
            [{ opacity: 0, transform: 'scaleY(0.92) translateY(15px)' },
             { opacity: 1, transform: 'scaleY(1) translateY(0)' }],
            { duration: 600, stagger: 100 }
        );

        /* ─ DL terms: fadeIn individual ─ */
        revealOnScroll('dl dt',
            [{ opacity: 0, transform: 'translateY(10px)' },
             { opacity: 1, transform: 'translateY(0)' }],
            { duration: 400, stagger: 50 }
        );

        /* ─ Semáforos / color cards ─ */
        revealOnScroll('[style*="border: 2px solid #ef5350"], [style*="border: 2px solid #ffca28"], [style*="border: 2px solid #66bb6a"]',
            [{ opacity: 0, transform: 'translateY(25px) scale(0.95)' },
             { opacity: 1, transform: 'translateY(0) scale(1)' }],
            { duration: 600, stagger: 120 }
        );

        /* ─ Code blocks: máquina de escribir visual ─ */
        document.querySelectorAll('.code-block').forEach(block => {
            block.animate(
                [{ opacity: 0, transform: 'translateX(-8px)', filter: 'blur(4px)' },
                 { opacity: 1, transform: 'translateX(0)', filter: 'blur(0)' }],
                { duration: 700, delay: 300, easing: EASE_OUT, fill: 'both' }
            );
        });

        /* ─ Tablas filas: highlight hover ─ */
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.animate(
                    [{ backgroundColor: 'transparent' },
                     { backgroundColor: 'rgba(212, 175, 55, 0.08)' }],
                    { duration: 200, fill: 'forwards', easing: 'ease-out' }
                );
            });
            row.addEventListener('mouseleave', () => {
                row.animate(
                    [{ backgroundColor: 'rgba(212, 175, 55, 0.08)' },
                     { backgroundColor: 'transparent' }],
                    { duration: 200, fill: 'forwards', easing: 'ease-out' }
                );
            });
        });

        /* ─ Links nav: pulse hover ─ */
        document.querySelectorAll('.manual-nav a').forEach(link => {
            link.addEventListener('mouseenter', () => {
                link.animate(
                    [{ transform: 'translateX(0)' },
                     { transform: 'translateX(6px)' }],
                    { duration: 200, fill: 'forwards', easing: EASE_STD }
                );
            });
            link.addEventListener('mouseleave', () => {
                link.animate(
                    [{ transform: 'translateX(6px)' },
                     { transform: 'translateX(0)' }],
                    { duration: 200, fill: 'forwards', easing: EASE_STD }
                );
            });
        });

        /* ─ Imágenes: tilt 3D suave ─ */
        document.querySelectorAll('.responsive-img').forEach(img => {
            const parent = img.parentElement;
            parent.addEventListener('mousemove', e => {
                const rect = img.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width - 0.5) * 10;
                const y = ((e.clientY - rect.top) / rect.height - 0.5) * -10;
                img.style.transform = `perspective(800px) rotateY(${x}deg) rotateX(${y}deg) scale(1.03)`;
                img.style.boxShadow = `${-x}px ${y}px 30px rgba(0,0,0,0.2)`;
            });
            parent.addEventListener('mouseleave', () => {
                img.style.transform = '';
                img.style.boxShadow = '';
            });
        });

        /* ─ Sección activa: resaltar suave ─ */
        revealOnScroll('.recommendations-panel',
            [{ opacity: 0, transform: 'translateY(50px)' },
             { opacity: 1, transform: 'translateY(0)' }],
            { duration: 800 }
        );

    }); /* end DOMContentLoaded */

    /* ══════════════════════════════════════════
       4. PAGE TRANSITION — fade out al navegar
    ══════════════════════════════════════════ */
    document.addEventListener('click', e => {
        const link = e.target.closest('a[href]');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto')) return;
        e.preventDefault();
        document.body.animate(
            [{ opacity: 1, transform: 'translateY(0)' },
             { opacity: 0, transform: 'translateY(-10px)' }],
            { duration: 300, easing: 'ease-in', fill: 'both' }
        ).onfinish = () => { window.location.href = href; };
    });

    /* ══════════════════════════════════════════
       5. TYPING CURSOR en el h1 del header
    ══════════════════════════════════════════ */
    const mainH1 = document.querySelector('.manual-header h1, header h1');
    if (mainH1) {
        const cursor = document.createElement('span');
        cursor.style.cssText = `
            display: inline-block; width: 3px; height: 0.85em;
            background: #d4af37; margin-left: 6px; vertical-align: middle;
            animation: blinkCursor 1.1s step-end infinite;
        `;
        cursor.id = 'typing-cursor';
        mainH1.appendChild(cursor);
        // Quitar cursor después de 4s
        setTimeout(() => cursor.remove(), 4000);
    }

    /* ══════════════════════════════════════════
       6. CONTADOR animado en números de sección
    ══════════════════════════════════════════ */
    document.querySelectorAll('.toc-number').forEach(numEl => {
        const finalVal = parseInt(numEl.textContent, 10);
        if (isNaN(finalVal)) return;
        let start = 0;
        const step = () => {
            if (start <= finalVal) {
                numEl.textContent = start++;
                setTimeout(step, 60);
            }
        };
        setTimeout(step, 500);
    });

})();
