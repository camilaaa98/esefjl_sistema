<?php
/**
 * SecurityHeaders Middleware - ESE Fabio Jaramillo
 * Aplica cabeceras de seguridad HTTP para mitigar ataques comunes.
 */
class SecurityHeaders {
    public static function apply() {
        // Evita Clickjacking
        header("X-Frame-Options: DENY");
        
        // Previene MIME Sniffing
        header("X-Content-Type-Options: nosniff");
        
        // Política de Seguridad de Contenido (CSP)
        // Permite scripts de CDN confiables (Tailwind, Google Fonts) y de la propia base
        header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https://img.icons8.com https://images.unsplash.com;");
        
        // HSTS (Solo si hay HTTPS)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }

        // Eliminar cabeceras que revelan tecnología
        header_remove("X-Powered-By");
    }
}

