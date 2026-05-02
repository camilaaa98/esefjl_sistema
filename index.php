<?php
/**
 * Index Centralizado - ESE Fabio Jaramillo
 * Punto de entrada compatible con WAMP Server.
 */
session_start();

// Redirigir al punto de entrada oficial en public/
header('Location: public/index.php');
exit();
?>
