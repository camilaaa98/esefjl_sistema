/**
 * FARMACIA ESEFJL — Web Animations API v2.0
 * Aplica a: Sistema principal, Manuales, Artículo
 * Excluye: login.php
 *
 * Principios: Movimientos orgánicos, no perfectos.
 * Easing functions naturales, delays variables, micro-interacciones.
 */

(function () {
    'use strict';

    /* ─── CONFIGURACIÓN GLOBAL CON EASING ORGÁNICO ─── */
    const EASING = {
        expo: 'cubic-bezier(0.16, 1, 0.3, 1)',        // Salida suave
        back: 'cubic-bezier(0.34, 1.56, 0.64, 1)',   // Elástico sutil
        spring: 'cubic-bezier(0.68, -0.15, 0.265, 1.25)', // Rebote natural
        smooth: 'cubic-bezier(0.4, 0, 0.2, 1)',     // Estándar suave
        dramatic: 'cubic-bezier(0.87, 0, 0.13, 1)'   // Entrada dramática
    };

    /* ─── UTILIDADES ─── */
    
    // Genera delays escalonados no uniformes para efecto natural
    function organicDelay(index, baseDelay = 50, variance = 30) {
        const randomOffset = Math.random() * variance - (variance / 2);
        return baseDelay + (index * 65) + randomOffset;
    }

    // IntersectionObserver con threshold variable
    function observeElements(selector, callback, options = {}) {
        const elements = document.querySelectorAll(selector);
        if (!elements.length) return;

        const config = {
            threshold: [0.1, 0.25, 0.5],
            rootMargin: '0px 0px -50px 0px',
            ...options
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    entry.target.dataset.animated = 'true';
                    callback(entry.target, i);
                }
            });
        }, config);

        elements.forEach((el) => observer.observe(el));
    }

    /* ─── 1. ANIMACIONES DE ENTRADA ORGÁNICAS ─── */
    document.addEventListener('DOMContentLoaded', function () {
        
        // Cards con entrada escalonada y variación
        const cards = document.querySelectorAll(
            'section.content-card, .card-clinical, .abstract-container'
        );
        cards.forEach((card, i) => {
            const delay = organicDelay(i, 60, 40);
            card.animate(
                [
                    { opacity: 0, transform: 'translateY(35px) scale(0.98)' },
                    { opacity: 0.5, transform: 'translateY(15px) scale(0.995)', offset: 0.5 },
                    { opacity: 1, transform: 'translateY(0) scale(1)' },
                ],
                {
                    duration: 700,
                    delay: delay,
                    easing: EASING.expo,
                    fill: 'both',
                }
            );
        });

        // Títulos con slide desde izquierda
        const titles = document.querySelectorAll('.section-title, h2.section-title, h1');
        titles.forEach((el, i) => {
            el.animate(
                [
                    { opacity: 0, transform: 'translateX(-25px) scale(0.98)' },
                    { opacity: 0.7, transform: 'translateX(5px) scale(1.01)', offset: 0.6 },
                    { opacity: 1, transform: 'translateX(0) scale(1)' },
                ],
                { 
                    duration: 550, 
                    delay: 80 + (i * 45), 
                    easing: EASING.expo, 
                    fill: 'both' 
                }
            );
        });

        // Sidebar con entrada desde izquierda
        const navs = document.querySelectorAll('.sidebar-medical, .manual-nav');
        navs.forEach((nav) => {
            nav.animate(
                [
                    { opacity: 0, transform: 'translateX(-35px)' },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 600, delay: 40, easing: EASING.expo, fill: 'both' }
            );
        });

        // Header con slide desde arriba
        const headers = document.querySelectorAll('header, .manual-header');
        headers.forEach((h) => {
            h.animate(
                [
                    { opacity: 0, transform: 'translateY(-25px)' },
                    { opacity: 0.6, transform: 'translateY(5px)', offset: 0.5 },
                    { opacity: 1, transform: 'translateY(0)' },
                ],
                { duration: 650, easing: EASING.expo, fill: 'both' }
            );
        });

        // Tablas con scale y elasticidad
        const tables = document.querySelectorAll('table, .checklist-table, .table-clinical');
        tables.forEach((t, i) => {
            const delay = 100 + (i * 90);
            t.animate(
                [
                    { opacity: 0, transform: 'scale(0.94) translateY(15px)' },
                    { opacity: 0.8, transform: 'scale(1.01)', offset: 0.6 },
                    { opacity: 1, transform: 'scale(1) translateY(0)' },
                ],
                { duration: 650, delay: delay, easing: EASING.back, fill: 'both' }
            );
        });

        // Alertas con bounce
        const alerts = document.querySelectorAll('.alert, .alert-important, .alert-info');
        alerts.forEach((el, i) => {
            el.animate(
                [
                    { opacity: 0, transform: 'scale(0.9) translateY(15px)' },
                    { opacity: 0.8, transform: 'scale(1.03) translateY(-3px)', offset: 0.5 },
                    { opacity: 1, transform: 'scale(1) translateY(0)' },
                ],
                { duration: 550, delay: 150 + (i * 70), easing: EASING.spring, fill: 'both' }
            );
        });

        // TOC items escalonados
        const tocItems = document.querySelectorAll('.toc-item');
        tocItems.forEach((item, i) => {
            item.animate(
                [
                    { opacity: 0, transform: 'translateX(-15px)' },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 450, delay: 250 + (i * 55), easing: EASING.expo, fill: 'both' }
            );
        });

        // Tech tags con scale y rotación sutil
        const tags = document.querySelectorAll('.tech-tag');
        tags.forEach((tag, i) => {
            tag.animate(
                [
                    { opacity: 0, transform: 'scale(0.7) rotate(-5deg)' },
                    { opacity: 1, transform: 'scale(1.05) rotate(1deg)', offset: 0.6 },
                    { opacity: 1, transform: 'scale(1) rotate(0deg)' },
                ],
                { duration: 500, delay: 350 + (i * 70), easing: EASING.spring, fill: 'both' }
            );
        });

        // Badges y chips
        const badges = document.querySelectorAll('.badge, .chip, [class*="badge"]');
        badges.forEach((badge, i) => {
            badge.animate(
                [
                    { opacity: 0, transform: 'scale(0.8)' },
                    { opacity: 1, transform: 'scale(1.1)', offset: 0.5 },
                    { opacity: 1, transform: 'scale(1)' },
                ],
                { duration: 400, delay: 200 + (i * 50), easing: EASING.spring, fill: 'both' }
            );
        });

        // Formularios - inputs con fade suave
        const formGroups = document.querySelectorAll('form > div, .form-group');
        formGroups.forEach((group, i) => {
            group.animate(
                [
                    { opacity: 0, transform: 'translateY(20px)' },
                    { opacity: 1, transform: 'translateY(0)' },
                ],
                { duration: 500, delay: 100 + (i * 80), easing: EASING.expo, fill: 'both' }
            );
        });
    });

    /* ─── 2. ANIMACIONES ON-SCROLL CON OBSERVER ─── */
    
    observeElements('.reveal-on-scroll', (el, i) => {
        el.animate(
            [
                { opacity: 0, transform: 'translateY(45px) scale(0.97)' },
                { opacity: 1, transform: 'translateY(0) scale(1)' },
            ],
            { duration: 750, delay: i * 60, easing: EASING.expo, fill: 'both' }
        );
    });

    observeElements('.reveal-left', (el, i) => {
        el.animate(
            [
                { opacity: 0, transform: 'translateX(-30px)' },
                { opacity: 1, transform: 'translateX(0)' },
            ],
            { duration: 600, delay: i * 50, easing: EASING.expo, fill: 'both' }
        );
    });

    observeElements('.reveal-right', (el, i) => {
        el.animate(
            [
                { opacity: 0, transform: 'translateX(30px)' },
                { opacity: 1, transform: 'translateX(0)' },
            ],
            { duration: 600, delay: i * 50, easing: EASING.expo, fill: 'both' }
        );
    });

    observeElements('.reveal-scale', (el, i) => {
        el.animate(
            [
                { opacity: 0, transform: 'scale(0.9)' },
                { opacity: 1, transform: 'scale(1)' },
            ],
            { duration: 550, delay: i * 70, easing: EASING.back, fill: 'both' }
        );
    });

    /* ─── 3. MICRO-INTERACCIONES HOVER ─── */
    
    // Botones - ripple orgánico
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-institutional, .btn-gold, button[type="submit"]');
        if (!btn) return;

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
            background: rgba(255,255,255,0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        `;
        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        btn.appendChild(ripple);

        ripple.animate(
            [
                { width: '0px', height: '0px', opacity: 0.5 },
                { width: '300px', height: '300px', opacity: 0 },
            ],
            { duration: 600, easing: EASING.expo }
        ).onfinish = () => ripple.remove();
    });

    // Cards - tilt sutil on mouse move (solo en desktop)
    if (!window.matchMedia('(pointer: coarse)').matches) {
        document.querySelectorAll('.card-clinical, .content-card').forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -3;
                const rotateY = ((x - centerX) / centerX) * 3;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.01)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transition = 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
                setTimeout(() => {
                    card.style.transition = '';
                }, 500);
            });
        });
    }

    /* ─── 4. TRANSICIONES DE PÁGINA ─── */
    
    // Prevenir transición en clicks con modificadores
    document.addEventListener('click', function (e) {
        if (e.ctrlKey || e.metaKey || e.shiftKey) return;
        
        const link = e.target.closest('a[href]');
        if (!link) return;
        
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto') || href.startsWith('tel')) return;
        if (link.hasAttribute('download') || link.target === '_blank') return;

        e.preventDefault();
        
        document.body.animate(
            [{ opacity: 1 }, { opacity: 0 }],
            { duration: 300, easing: 'ease-in', fill: 'both' }
        ).onfinish = () => { window.location.href = href; };
    });

    // Entrada de página
    window.addEventListener('pageshow', () => {
        document.body.animate(
            [{ opacity: 0 }, { opacity: 1 }],
            { duration: 400, easing: EASING.expo, fill: 'both' }
        );
    });

    /* ─── 5. ANIMACIONES DE TABLA ─── */
    
    // Filas con hover animado
    document.querySelectorAll('tbody tr').forEach((row) => {
        let hoverAnim = null;
        
        row.addEventListener('mouseenter', () => {
            if (hoverAnim) hoverAnim.cancel();
            hoverAnim = row.animate(
                [
                    { backgroundColor: 'transparent' },
                    { backgroundColor: 'rgba(212, 175, 55, 0.08)' },
                ],
                { duration: 250, fill: 'forwards', easing: EASING.smooth }
            );
        });
        
        row.addEventListener('mouseleave', () => {
            if (hoverAnim) hoverAnim.cancel();
            hoverAnim = row.animate(
                [
                    { backgroundColor: 'rgba(212, 175, 55, 0.08)' },
                    { backgroundColor: 'transparent' },
                ],
                { duration: 250, fill: 'forwards', easing: EASING.smooth }
            );
        });
    });

    /* ─── 6. ANIMACIONES DE PAGINACIÓN ─── */
    
    const paginationLinks = document.querySelectorAll('.pagination a, .page-link');
    paginationLinks.forEach((link) => {
        link.addEventListener('click', function (e) {
            const container = this.closest('.table-clinical-wrapper') || document.querySelector('table');
            if (!container) return;
            
            container.animate(
                [{ opacity: 1 }, { opacity: 0.5 }, { opacity: 1 }],
                { duration: 300, easing: EASING.smooth }
            );
        });
    });

    /* ─── 7. EFECTOS ESPECIALES ─── */
    
    // Parallax sutil en elementos decorativos
    if (!window.matchMedia('(pointer: coarse)').matches) {
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    const scrolled = window.pageYOffset;
                    document.querySelectorAll('.parallax-slow').forEach((el) => {
                        el.style.transform = `translateY(${scrolled * 0.15}px)`;
                    });
                    document.querySelectorAll('.parallax-fast').forEach((el) => {
                        el.style.transform = `translateY(${scrolled * 0.25}px)`;
                    });
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    // Toast notifications
    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: ${type === 'error' ? '#ef5350' : type === 'success' ? '#66bb6a' : '#111'};
            color: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 600;
            font-size: 14px;
        `;
        document.body.appendChild(toast);

        toast.animate(
            [
                { opacity: 0, transform: 'translateX(100px) scale(0.9)' },
                { opacity: 1, transform: 'translateX(0) scale(1)' },
            ],
            { duration: 400, easing: EASING.back, fill: 'both' }
        );

        setTimeout(() => {
            toast.animate(
                [
                    { opacity: 1, transform: 'translateX(0)' },
                    { opacity: 0, transform: 'translateX(100px)' },
                ],
                { duration: 300, easing: EASING.expo }
            ).onfinish = () => toast.remove();
        }, 3000);
    };

    /* ─── 8. LOADING STATES ─── */
    
    // Skeleton loading para contenido async
    window.showSkeleton = function(selector, count = 3) {
        const container = document.querySelector(selector);
        if (!container) return;
        
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const skeleton = document.createElement('div');
            skeleton.className = 'skeleton';
            skeleton.style.cssText = `
                height: 60px;
                margin: 8px 0;
                background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                border-radius: 8px;
                animation: shimmer 1.5s infinite;
            `;
            container.appendChild(skeleton);
        }
    };

    // Expose easing functions para uso externo
    window.ESEFJL_EASING = EASING;

})();
