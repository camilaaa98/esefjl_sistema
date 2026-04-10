<?php
/**
 * Ayudante de Vista - ESE Fabio Jaramillo
 * Siguiendo SRP: Lógica de presentación separada de la lógica de negocio.
 */
class ViewHelper {
    
    public static function getStatusBadge($current, $min, $expiry = null) {
        $today = date('Y-m-d');
        $warning_date = date('Y-m-d', strtotime('+3 months'));

        if ($expiry && $expiry < $today) {
            return '<span class="badge sema-red">VENCIDO</span>';
        } elseif ($expiry && $expiry < $warning_date) {
            return '<span class="badge sema-yellow">POR VENCER</span>';
        }

        if ($current <= ($min * 0.25)) {
            return '<span class="badge sema-red">STOCK CRíTICO</span>';
        } elseif ($current < $min) {
            return '<span class="badge sema-yellow">STOCK BAJO</span>';
        } else {
            return '<span class="badge sema-green">ÓPTIMO</span>';
        }
    }

    public static function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}
