/**
 * FARMACIA ESEFJL — Web Animations API v1.0
 * Aplica a: Sistema principal, Manuales, Artículo
 * Excluye: login.php
 *
 * Principios: no modifica HTML ni CSS existente.
 * Solo añade animaciones programáticas via Web Animations API.
 */

(function () {
    'use strict';

    /* ─── CONFIGURACIÓN GLOBAL ─── */
    const EASING_STANDARD   = 'cubic-bezier(0.4, 0, 0.2, 1)';
    const EASING_DECELERATE = 'cubic-bezier(0, 0, 0.2, 1)';

    /** Aplica fadeInUp con IntersectionObserver a cualquier selector */
    function animateOnScroll(selector, keyframes, options) {
        const elements = document.querySelectorAll(selector);
        if (!elements.length) return;

        const defaults = {
            duration: 600,
            easing: EASING_DECELERATE,
            fill: 'both',
        };
        const config = Object.assign({}, defaults, options);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.animate(keyframes, config);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        elements.forEach((el) => observer.observe(el));
    }

    /* ─── 1. ANIMACIONES DE ENTRADA — SECCIONES ─── */
    document.addEventListener('DOMContentLoaded', function () {

        // Secciones y cards — fadeInUp escalonado
        const cards = document.querySelectorAll(
            'section.content-card, .card-clinical, .abstract-container'
        );
        cards.forEach((card, i) => {
            card.animate(
                [
                    { opacity: 0, transform: 'translateY(28px)' },
                    { opacity: 1, transform: 'translateY(0)' },
                ],
                {
                    duration: 650,
                    delay: 80 + i * 70,
                    easing: EASING_DECELERATE,
                    fill: 'both',
                }
            );
        });

        // Títulos de sección — slideInLeft
        const titles = document.querySelectorAll('.section-title, h2.section-title');
        titles.forEach((el, i) => {
            el.animate(
                [
                    { opacity: 0, transform: 'translateX(-20px)' },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 500, delay: 100 + i * 60, easing: EASING_DECELERATE, fill: 'both' }
            );
        });

        // Sidebar/nav — fadeInLeft
        const navs = document.querySelectorAll('.sidebar-medical, .manual-nav');
        navs.forEach((nav) => {
            nav.animate(
                [
                    { opacity: 0, transform: 'translateX(-30px)' },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 500, delay: 50, easing: EASING_DECELERATE, fill: 'both' }
            );
        });

        // Header principal — slideDown
        const headers = document.querySelectorAll('header, .manual-header');
        headers.forEach((h) => {
            h.animate(
                [
                    { opacity: 0, transform: 'translateY(-20px)' },
                    { opacity: 1, transform: 'translateY(0)' },
                ],
                { duration: 550, easing: EASING_DECELERATE, fill: 'both' }
            );
        });

        // Tablas — scaleIn
        const tables = document.querySelectorAll('table, .checklist-table');
        tables.forEach((t, i) => {
            t.animate(
                [
                    { opacity: 0, transform: 'scale(0.95)' },
                    { opacity: 1, transform: 'scale(1)' },
                ],
                { duration: 500, delay: 150 + i * 80, easing: EASING_DECELERATE, fill: 'both' }
            );
        });

        // Alertas — fadeIn con bounce final
        const alerts = document.querySelectorAll('.alert, .alert-important, .alert-info');
        alerts.forEach((el, i) => {
            el.animate(
                [
                    { opacity: 0, transform: 'scale(0.96) translateY(10px)' },
                    { opacity: 0.8, transform: 'scale(1.02) translateY(-2px)' },
                    { opacity: 1, transform: 'scale(1) translateY(0)' },
                ],
                { duration: 600, delay: 200 + i * 80, easing: EASING_STANDARD, fill: 'both' }
            );
        });

        // TOC items — fadeIn escalonado
        const tocItems = document.querySelectorAll('.toc-item');
        tocItems.forEach((item, i) => {
            item.animate(
                [
                    { opacity: 0, transform: 'translateX(-12px)' },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 400, delay: 300 + i * 45, easing: EASING_DECELERATE, fill: 'both' }
            );
        });

        // Tech tags — scaleIn con pulse
        const tags = document.querySelectorAll('.tech-tag');
        tags.forEach((tag, i) => {
            tag.animate(
                [
                    { opacity: 0, transform: 'scale(0.7)' },
                    { opacity: 1, transform: 'scale(1.05)' },
                    { opacity: 1, transform: 'scale(1)' },
                ],
                { duration: 400, delay: 400 + i * 60, easing: EASING_STANDARD, fill: 'both' }
            );
        });

    });

    /* ─── 2. ANIMACIONES ON-SCROLL (IntersectionObserver) ─── */
    // Se activan cuando el elemento entra al viewport
    animateOnScroll(
        '.recommendations-panel',
        [
            { opacity: 0, transform: 'translateY(40px)' },
            { opacity: 1, transform: 'translateY(0)' },
        ],
        { duration: 700, easing: EASING_DECELERATE }
    );

    animateOnScroll(
        '.citation-block',
        [
            { opacity: 0, transform: 'translateX(-16px)' },
            { opacity: 1, transform: 'translateX(0)' },
        ],
        { duration: 500, easing: EASING_DECELERATE }
    );

    animateOnScroll(
        'dl dt',
        [
            { opacity: 0, transform: 'translateY(10px)' },
            { opacity: 1, transform: 'translateY(0)' },
        ],
        { duration: 450 }
    );

    /* ─── 3. HOVER CON WEB ANIMATIONS API ─── */
    // Botones — ripple suave
    document.addEventListener('mouseover', function (e) {
        const btn = e.target.closest('button, .btn-institutional, .btn-gold, input[type="submit"]');
        if (!btn) return;

        btn.animate(
            [{ transform: 'scale(1)' }, { transform: 'scale(1.04)' }, { transform: 'scale(1)' }],
            { duration: 300, easing: EASING_STANDARD }
        );
    });

    /* ─── 4. TRANSICIÓN DE PÁGINA ─── */
    // Fade-out suave al salir de la página
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[href]');
        if (!link) return;
        const href = link.getAttribute('href');
        // Solo aplica a links internos que no sean anclas
        if (!href || href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto')) return;

        e.preventDefault();
        document.body.animate(
            [{ opacity: 1 }, { opacity: 0 }],
            { duration: 250, easing: 'ease-in', fill: 'both' }
        ).onfinish = () => { window.location.href = href; };
    });

    /* ─── 5. LÍNEAS DE TABLA — highlight al hover ─── */
    document.querySelectorAll('tbody tr').forEach((row) => {
        row.addEventListener('mouseenter', () => {
            row.animate(
                [
                    { backgroundColor: 'transparent' },
                    { backgroundColor: 'rgba(212, 175, 55, 0.07)' },
                ],
                { duration: 200, fill: 'forwards', easing: 'ease-out' }
            );
        });
        row.addEventListener('mouseleave', () => {
            row.animate(
                [
                    { backgroundColor: 'rgba(212, 175, 55, 0.07)' },
                    { backgroundColor: 'transparent' },
                ],
                { duration: 200, fill: 'forwards', easing: 'ease-out' }
            );
        });
    });

})();
