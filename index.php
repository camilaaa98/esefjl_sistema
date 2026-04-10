<?php
/**
 * Index Centralizado - ESE Fabio Jaramillo
 * Punto de entrada compatible con WAMP Server.
 */
session_start();

// Si el usuario no está autenticado, redirigir al Login Extraordinario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: views/login.php');
    exit();
}

// Si está autenticado, llevar al Inicio (Inicio anterior)
header('Location: views/inicio.php');
exit();
?>
