<?php
require_once __DIR__ . '/core/Database.php';
$db = Database::getInstance();

$ips_rol = 19; // Confirmado
$password_hash = password_hash('ips2025', PASSWORD_DEFAULT);

// Mapeo confirmado con IDs reales
$ips_users = [
    'jefe_milan'       => ['sede_id' => 4, 'nombre' => 'Milán',                   'doc' => '1099001001'],
    'jefe_solano'      => ['sede_id' => 3, 'nombre' => 'Solano',                  'doc' => '1099001002'],
    'jefe_solita'      => ['sede_id' => 2, 'nombre' => 'Solita',                  'doc' => '1099001003'],
    'jefe_valparaiso'  => ['sede_id' => 6, 'nombre' => 'Valparaíso',              'doc' => '1099001004'],
    'jefe_getucha'     => ['sede_id' => 5, 'nombre' => 'San Antonio de Getucha',  'doc' => '1099001005'],
];

echo "=== CREANDO/ACTUALIZANDO JEFES IPS ===\n";

foreach ($ips_users as $username => $info) {
    // Verificar si existe por username
    $existing = $db->prepare("SELECT id FROM usuarios WHERE username = ?");
    $existing->execute([$username]);
    $uid = $existing->fetchColumn();

    if ($uid) {
        // Solo actualizar rol y sede
        $db->prepare("UPDATE usuarios SET rol_id=?, sede_id=?, password=? WHERE id=?")->execute(
            [$ips_rol, $info['sede_id'], $password_hash, $uid]
        );
        echo "  ✅ ACTUALIZADO: $username → sede={$info['nombre']}\n";
    } else {
        // Verificar si el documento ya existe y eliminarlo primero si es de un usuario fantasma
        $db->prepare("DELETE FROM usuarios WHERE documento = ? AND username IS NULL")->execute([$info['doc']]);

        $ins = $db->prepare("
            INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id, correo)
            VALUES (?, ?, 'ESE FJL', ?, ?, ?, ?, ?)
        ");
        $ins->execute([
            $info['doc'],
            'Jefe ' . $info['nombre'],
            $username,
            $password_hash,
            $ips_rol,
            $info['sede_id'],
            "$username@esefjl.gov.co"
        ]);
        echo "  ✅ CREADO: $username → sede={$info['nombre']}\n";
    }
}

echo "\n=== VERIFICACIÓN FINAL ===\n";
$veri = $db->query("
    SELECT u.username, r.nombre as rol, s.nombre as sede, u.sede_id
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    LEFT JOIN sedes s ON u.sede_id = s.id
    WHERE u.rol_id = $ips_rol
    ORDER BY s.nombre
")->fetchAll();

foreach ($veri as $v) {
    echo "  {$v['username']} | sede={$v['sede']} (id={$v['sede_id']})\n";
}

echo "\n✅ ACCESO LISTO:\n";
foreach ($ips_users as $u => $info) {
    echo "  $u / ips2025 → " . $info['nombre'] . "\n";
}
