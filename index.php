<?php
/**
 * Index Centralizado - ESE Fabio Jaramillo
 * Punto de entrada compatible con WAMP Server.
 */
session_start();

// Si el usuario no está autenticado, redirigir al Login Extraordinario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: pages/login.php');
    exit();
}

// Si está autenticado, llevar al Dashboard
header('Location: pages/dashboard.php');
exit();
?>
