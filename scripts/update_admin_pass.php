<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();
$password = password_hash('Admin2026', PASSWORD_BCRYPT);
$db->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'")->execute([$password]);
echo "Password updated for 'admin' to: Admin2026";
?>
