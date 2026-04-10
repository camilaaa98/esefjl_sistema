<?php
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();

echo "=== DIAGNÓSTICO ACTUAL ===\n";
$stmt = $db->query("
    SELECT u.id, u.usuario, u.rol, u.sede_id, s.nombre as sede_nombre 
    FROM usuarios u 
    LEFT JOIN sedes s ON u.sede_id = s.id 
    WHERE u.rol = 'IPS (Municipio)' 
    ORDER BY s.nombre
");
$ips_users = $stmt->fetchAll();
foreach($ips_users as $row) {
    echo "  usuario=" . $row['usuario'] . " | sede_id=" . $row['sede_id'] . " | sede=" . $row['sede_nombre'] . "\n";
}

echo "\n=== SEDES DISPONIBLES ===\n";
$sedes = $db->query("SELECT id, nombre, tipo FROM sedes ORDER BY nombre")->fetchAll();
foreach($sedes as $s) {
    echo "  ID=" . $s['id'] . " | " . $s['nombre'] . " (" . $s['tipo'] . ")\n";
}

// Mapeo correcto: username => nombre de sede
$ips_map = [
    'jefe_solita'    => 'Solita',
    'jefe_solano'    => 'Solano',
    'jefe_milan'     => 'Milán',
    'jefe_valparaiso'=> 'Valparaíso',
    'jefe_getucha'   => 'San Antonio de Getucha',
];

// Obtener IDs de sedes
$sede_ids = [];
foreach($sedes as $s) {
    $sede_ids[$s['nombre']] = $s['id'];
}

echo "\n=== CREANDO / ACTUALIZANDO USUARIOS IPS ===\n";

// Primero, marcar todos los anteriores como inactivos (rol viejo)
$db->exec("UPDATE usuarios SET activo = false WHERE rol = 'IPS (Municipio)'");

$password_hash = password_hash('ips2025', PASSWORD_DEFAULT);

foreach ($ips_map as $username => $sede_nombre) {
    $sede_id = $sede_ids[$sede_nombre] ?? null;
    if (!$sede_id) {
        echo "  ⚠️  Sede no encontrada: $sede_nombre\n";
        continue;
    }

    // Verificar si ya existe
    $check = $db->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->execute([$username]);
    $existing = $check->fetchColumn();

    if ($existing) {
        // Actualizar
        $upd = $db->prepare("
            UPDATE usuarios 
            SET contrasena = ?, sede_id = ?, rol = 'IPS (Municipio)', activo = true,
                nombres = ?, cargo = 'Jefe de Enfermería'
            WHERE id = ?
        ");
        $nombre_sede = strtoupper($sede_nombre);
        $upd->execute([$password_hash, $sede_id, "Jefe $sede_nombre", $existing]);
        echo "  ✅ ACTUALIZADO: $username → Sede: $sede_nombre (ID=$sede_id)\n";
    } else {
        // Insertar nuevo
        $ins = $db->prepare("
            INSERT INTO usuarios (usuario, contrasena, nombres, apellidos, email, rol, sede_id, cargo, activo)
            VALUES (?, ?, ?, 'ESE FJL', ?, 'IPS (Municipio)', ?, 'Jefe de Enfermería', true)
        ");
        $ins->execute([
            $username,
            $password_hash,
            "Jefe $sede_nombre",
            "$username@esefjl.gov.co",
            $sede_id
        ]);
        echo "  ✅ CREADO: $username → Sede: $sede_nombre (ID=$sede_id)\n";
    }
}

echo "\n=== VERIFICACIÓN FINAL ===\n";
$final = $db->query("
    SELECT u.usuario, u.rol, u.activo, s.nombre as sede, s.id as sede_id
    FROM usuarios u 
    LEFT JOIN sedes s ON u.sede_id = s.id 
    WHERE u.rol = 'IPS (Municipio)'
    ORDER BY s.nombre
")->fetchAll();
foreach($final as $r) {
    echo "  " . $r['usuario'] . " | " . $r['sede'] . " (sede_id=" . $r['sede_id'] . ") | activo=" . ($r['activo'] ? 'SI' : 'NO') . "\n";
}

echo "\n✅ CREDENCIALES IPS:\n";
foreach($ips_map as $user => $sede) {
    echo "  Usuario: $user | Contraseña: ips2025 | Sede: $sede\n";
}
