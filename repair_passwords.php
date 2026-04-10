<?php
try {
    $db = new PDO('sqlite:core/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Hash para Admin e IPS
    $hashAdmin = password_hash('Admin2026*', PASSWORD_DEFAULT);
    $hashIPS   = password_hash('ips2025', PASSWORD_DEFAULT);
    
    // Actualizar 5 sedes IPS
    $ips_users = ['jefe_solita', 'jefe_solano', 'jefe_milan', 'jefe_valparaiso', 'jefe_getucha'];
    
    foreach ($ips_users as $user) {
        $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
        $stmt->execute([$hashIPS, $user]);
        echo "✅ REPARADO: Accesso para $user (Sede) -> Clave: ips2025\n";
    }
    
    // Actualizar Admin
    $stmtAdmin = $db->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
    $stmtAdmin->execute([$hashAdmin]);
    echo "✅ REPARADO: Acceso para admin -> Clave: Admin2026*\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
