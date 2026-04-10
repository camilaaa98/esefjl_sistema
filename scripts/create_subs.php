<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $rolesMap = $db->query("SELECT nombre, id FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
    $sede_id = $db->query("SELECT id FROM sedes WHERE tipo = 'PRINCIPAL'")->fetchColumn();
    $pass = password_hash('Ese2026*', PASSWORD_DEFAULT);

    $cargos = [
        ['Subgerente de Servicios de Salud', 's_salud'],
        ['Subgerente Administrativa y Financiera', 's_adm']
    ];

    foreach($cargos as $c) {
        $stmt = $db->prepare("
            INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) 
            VALUES (?, 'Subgerente', ?, ?, ?, ?, ?)
            ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password
        ");
        $stmt->execute([
            rand(40000000, 50000000), 
            $c[0], 
            $c[1], 
            $pass, 
            $rolesMap[$c[0]], 
            $sede_id
        ]);
    }

    echo "✅ SUBGERENTES CREADOS.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
