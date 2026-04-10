<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "📊 Iniciando carga masiva de datos (1,500 registros)...\n";

    // 1. Obtener IDs de referencia
    $sedes = $db->query("SELECT id, tipo FROM sedes")->fetchAll();
    $roles = $db->query("SELECT id, nombre FROM roles")->fetchAll();
    
    $roleIds = [];
    foreach($roles as $r) $roleIds[$r['nombre']] = $r['id'];
    
    $sedeIds = ['PRINCIPAL' => [], 'MUNICIPIO' => []];
    foreach($sedes as $s) $sedeIds[$s['tipo']][] = $s['id'];

    $epsList = ['Nueva EPS', 'Sanitas', 'Famac', 'Asmet Salud', 'FF.MM. y Policía Nacional', 'Coosalud', 'AIC'];
    $regimenes = ['CONTRIBUTIVO', 'SUBSIDIADO'];

    // 2. Generar 1,000 Pacientes
    echo "👥 Generando 1,000 pacientes...\n";
    $stmtPac = $db->prepare("INSERT INTO pacientes (documento, nombres, apellidos, eps, regimen, es_desplazado, sisben) VALUES (?, ?, ?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
    
    for($i = 1; $i <= 1000; $i++) {
        $doc = "100" . str_pad($i, 7, '0', STR_PAD_LEFT);
        $nom = "Paciente_" . $i;
        $ape = "Apellido_" . $i;
        $eps = $epsList[array_rand($epsList)];
        $reg = $regimenes[array_rand($regimenes)];
        $desp = (rand(1, 10) > 8) ? 'true' : 'false'; // 20% desplazados
        $sis = "Nivel " . rand(1, 4);
        
        $stmtPac->execute([$doc, $nom, $ape, $eps, $reg, $desp, $sis]);
    }

    // 3. Generar 500 Funcionarios
    echo "💼 Generando 500 funcionarios...\n";
    $stmtUser = $db->prepare("INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) VALUES (?, ?, ?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
    $pass = password_hash('Ese2026*', PASSWORD_DEFAULT);

    for($i = 1; $i <= 500; $i++) {
        $doc = "200" . str_pad($i, 7, '0', STR_PAD_LEFT);
        $nom = "Funcionario_" . $i;
        $ape = "Apellido_" . $i;
        $user = "user_" . $i;
        
        // Lógica de Sede vs Rol
        $esAdminSede = (rand(1, 10) > 7); // 30% en la central
        $targetSede = $esAdminSede ? $sedeIds['PRINCIPAL'][0] : $sedeIds['MUNICIPIO'][array_rand($sedeIds['MUNICIPIO'])];
        
        if ($esAdminSede) {
            // Sede central: Solo Administrativos
            $validRoles = [$roleIds['Gerente'], $roleIds['Subgerente Administrativa y Financiera'], $roleIds['Administrativo']];
            $targetRol = $validRoles[array_rand($validRoles)];
        } else {
            // IPS: Puede ser Salud o Administrativo
            $validRoles = [$roleIds['Regente Farmacia'], $roleIds['Salud'], $roleIds['Administrativo']];
            $targetRol = $validRoles[array_rand($validRoles)];
        }

        $stmtUser->execute([$doc, $nom, $ape, $user, $pass, $targetRol, $targetSede]);
    }

    $db->commit();
    echo "✅ CARGA EXITOSA: 1,000 pacientes y 500 funcionarios creados.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
