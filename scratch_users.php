<?php
require 'app/config/Database.php';
$db = Database::getInstance();
$hash = password_hash('12345', PASSWORD_DEFAULT);
$db->query("UPDATE usuarios SET password = '$hash' WHERE username = 'admin'");
echo "Password updated for admin to '12345'\n";
?>
