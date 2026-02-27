<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "📊 RE-INICIANDO CARGA MASIVA (Versión Corregida)...\n";

    // 1. Obtener IDs exactos
    $rolesData = $db->query("SELECT id, nombre FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
    $rolesMap = array_flip($rolesData);
    
    $sedes = $db->query("SELECT id, nombre, tipo FROM sedes")->fetchAll();
    $sedeIds = ['PRINCIPAL' => [], 'MUNICIPIO' => []];
    foreach($sedes as $s) $sedeIds[$s['tipo']][] = $s['id'];

    $epsList = ['Nueva EPS', 'Sanitas', 'Famac', 'Asmet Salud', 'FF.MM. y Policía Nacional', 'Coosalud', 'AIC'];
    $regimenes = ['CONTRIBUTIVO', 'SUBSIDIADO'];

    // Limpiar previo para asegurar pureza
    $db->exec("DELETE FROM pacientes;");
    $db->exec("DELETE FROM usuarios WHERE username NOT IN ('admin', 'jefe_solita');");

    // 2. Generar 1,000 Pacientes
    echo "👥 Creando 1,000 pacientes...\n";
    $stmtPac = $db->prepare("INSERT INTO pacientes (documento, nombres, apellidos, eps, regimen, es_desplazado, sisben) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    for($i = 1; $i <= 1000; $i++) {
        $doc = "100" . str_pad($i, 7, '0', STR_PAD_LEFT);
        $nom = "Paciente " . $i;
        $ape = "Apellido " . $i;
        $eps = $epsList[array_rand($epsList)];
        $reg = $regimenes[array_rand($regimenes)];
        $desp = (rand(1, 10) > 8) ? 'true' : 'false';
        $sis = "SISBEN " . rand(1, 3);
        $stmtPac->execute([$doc, $nom, $ape, $eps, $reg, $desp, $sis]);
    }

    // 3. Generar 500 Funcionarios
    echo "💼 Creando 500 funcionarios jerárquicos...\n";
    $stmtUser = $db->prepare("INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $pass = password_hash('Ese2026*', PASSWORD_DEFAULT);

    // 3.1 Garantizar Altos Cargos en Principal
    $cargosAltos = [
        ['Gerente', 'G_Gerente'],
        ['Subgerente de Servicios de Salud', 'S_Salud'],
        ['Subgerente Administrativa y Financiera', 'S_Adm']
    ];

    foreach($cargosAltos as $cargo) {
        if(isset($rolesMap[$cargo[0]])) {
            $stmtUser->execute([
                "300".rand(100,999), "Titular", $cargo[0], strtolower($cargo[1]), $pass, $rolesMap[$cargo[0]], $sedeIds['PRINCIPAL'][0]
            ]);
        }
    }

    // 3.2 Resto de funcionarios
    for($i = 1; $i <= 497; $i++) {
        $doc = "200" . str_pad($i, 7, '0', STR_PAD_LEFT);
        $user = "funcionario_" . $i;
        
        $esAdminSede = (rand(1, 10) > 7);
        $targetSede = $esAdminSede ? $sedeIds['PRINCIPAL'][0] : $sedeIds['MUNICIPIO'][array_rand($sedeIds['MUNICIPIO'])];
        
        if ($esAdminSede) {
            $validRoles = ['Administrativo', 'Seguridad'];
        } else {
            $validRoles = ['Regente Farmacia', 'Salud', 'Administrativo'];
        }
        
        $rolNombre = $validRoles[array_rand($validRoles)];
        $stmtUser->execute([$doc, "Nombre_" . $i, "Apellido_" . $i, $user, $pass, $rolesMap[$rolNombre], $targetSede]);
    }

    $db->commit();
    echo "✅ ÉXITO TOTAL: 1.500 registros operativos cargados.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
