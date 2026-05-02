<?php
/**
 * Script para generar 50 proveedores - Versión 3 con PDO e inserciones directas
 */

require_once __DIR__ . '/../app/config/Database.php';

echo "=== CONECTANDO A BASE DE DATOS ===\n";

try {
    $db = Database::getInstance();
    echo "✅ Conexión exitosa\n\n";
} catch (Exception $e) {
    die("âŒ Error de conexión: " . $e->getMessage() . "\n");
}

// Crear tabla si no existe
try {
    $db->exec("CREATE TABLE IF NOT EXISTS proveedores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nit TEXT UNIQUE NOT NULL,
        razon_social TEXT NOT NULL,
        contacto TEXT,
        telefono TEXT,
        email TEXT
    )");
    echo "✅ Tabla proveedores verificada\n\n";
} catch (Exception $e) {
    echo "âš ï¸  Error creando tabla: " . $e->getMessage() . "\n";
}

// Datos de proveedores
$proveedores = [
    ['900123456-1', 'LABORATORIOS GENFARCO S.A.S', 'Carlos Rodríguez', '6012345678', 'contacto@genfarco.com', 'Medicamentos Genéricos'],
    ['900234567-2', 'NOVAMED COLOMBIA LTDA', 'María Fernanda López', '6013456789', 'pedidos@novamed.co', 'Medicamentos de Marca'],
    ['900345678-3', 'FARMACOL S.A.', 'Jorge Hernández', '6014567890', 'ventas@farmacol.com', 'Antibióticos'],
    ['900456789-4', 'BIOSALUD PHARMA', 'Ana Patricia Gómez', '6015678901', 'distribucion@biosalud.com', 'Biológicos y Vacunas'],
    ['900567890-5', 'INMUNOFAR LTDA', 'Luis Alberto Castro', '6016789012', 'pedidos@inmunofar.com', 'Inmunoglobulinas'],
    ['900678901-6', 'DROGUERíA EL SALVADOR', 'Diana Marcela Ruiz', '6017890123', 'drogueria@es.com', 'Drogas y Principios Activos'],
    ['900789012-7', 'COLOMBIANA DE ANTIBIí“TICOS', 'Roberto Carlos Pérez', '6018901234', 'ventas@coantibioticos.com', 'Antibióticos'],
    ['900890123-8', 'DIABETES CARE COLOMBIA', 'Lucía Elena Martínez', '6019012345', 'insumos@diabetescare.co', 'Insumos para Diabetes'],
    ['900901234-9', 'ONCOFARMA S.A.S', 'Fernando José Díaz', '6010123456', 'onco@oncofarma.co', 'Medicamentos Oncológicos'],
    ['901012345-0', 'NEUROLOGíA PHARMA', 'Carmen Rosa Vega', '6011234567', 'neuro@neuropharma.com', 'Medicamentos Neurológicos'],
    ['901123456-1', 'MEDICAL EQUIPMENT SAS', 'Andrés Felipe Torres', '6022345678', 'equipos@medicaleq.com', 'Equipos Médicos'],
    ['901234567-2', 'SUMINISTROS HOSPITALARIOS LTDA', 'Patricia Elena Morales', '6023456789', 'hospital@suministros.com', 'Material de Curación'],
    ['901345678-3', 'OXíGENO MEDICAL COLOMBIA', 'Juan David Ramírez', '6024567890', 'oxigeno@oximedical.co', 'Gases Medicinales'],
    ['901456789-4', 'ORTHOPEDIA Tí‰CNICA', 'Silvia Juliana Ortiz', '6025678901', 'ortesis@orthopedia.com', 'Prótesis y í“rtesis'],
    ['901567890-5', 'DIAGNí“STICA LTDA', 'Miguel íngel Suárez', '6026789012', 'reactivos@diagnostica.com', 'Reactivos de Laboratorio'],
    ['901678901-6', 'DISPOSMED S.A.S', 'Laura Carolina Mendoza', '6027890123', 'dispositivos@disposmed.co', 'Dispositivos Médicos'],
    ['901789012-7', 'CATí‰TERES Y SONDA COLOMBIA', 'Pedro Antonio Ríos', '6028901234', 'cateteres@cyc.com', 'Catéteres y Sondas'],
    ['901890123-8', 'INFUSIí“N Mí‰DICA LTDA', 'Marcela Andrea Flórez', '6029012345', 'equipos@infusionmedica.com', 'Equipos de Infusión'],
    ['901901234-9', 'CIRUGíA ESPECIALIZADA SAS', 'Diego Armando Sánchez', '6030123456', 'cirugia@cirugiaesp.com', 'Material de Cirugía'],
    ['902012345-0', 'ESTí‰RIL COLOMBIA', 'Natalia Fernanda Castro', '6031234567', 'esteril@esterilcolombia.com', 'Esterilización y Antisépticos'],
    ['902123456-1', 'NUTRICIí“N CLíNICA SAS', 'Gabriel Ernesto Vargas', '6032345678', 'nutricion@nutriclinica.com', 'Fórmulas Nutricionales'],
    ['902234567-2', 'SUPLEMENTOS HOSPITALARIOS', 'Verónica Alejandra Quintero', '6033456789', 'suplementos@suphosp.com', 'Suplementos Nutricionales'],
    ['902345678-3', 'LACTEOS ESPECIALIZADOS LTDA', 'Ricardo José Mendez', '6034567890', 'formulas@lacteosesp.com', 'Fórmulas Infantiles'],
    ['902456789-4', 'VITAMINAS COLOMBIA', 'Andrea Catalina Peña', '6035678901', 'vitaminas@vitco.com', 'Vitaminas y Minerales'],
    ['902567890-5', 'ENTERAL NUTRITION', 'Sebastián Alberto Cruz', '6036789012', 'enteral@enteralnutrition.com', 'Nutrición Enteral'],
    ['902678901-6', 'ANESTESIA TOTAL LTDA', 'Carolina del Pilar Guzmán', '6037890123', 'anestesia@anestotal.com', 'Medicamentos Anestésicos'],
    ['902789012-7', 'UCI PHARMA S.A.S', 'Eduardo Alonso Palacios', '6038901234', 'uci@ucipharma.com', 'Medicamentos UCI'],
    ['902890123-8', 'VENTILACIí“N Mí‰DICA', 'Isabella Cristina Medina', '6039012345', 'ventilacion@ventmedica.com', 'Equipos de Ventilación'],
    ['902901234-9', 'MONITORíA HOSPITALARIA', 'Alejandro Enrique Duarte', '6040123456', 'monitores@monitoreahosp.com', 'Monitores Multiparamétricos'],
    ['903012345-0', 'DESFIBRILADORES COLOMBIA', 'Diana Michelle Briceño', '6041234567', 'desfibriladores@desfcol.com', 'Desfibriladores'],
    ['903123456-1', 'CARDIOFARMA LTDA', 'Mauricio Javier Valencia', '6042345678', 'cardio@cardiofarma.com', 'Medicamentos Cardiovasculares'],
    ['903234567-2', 'STENTS CARDIOVASCULAR', 'Lorena Patricia Cárdenas', '6043456789', 'stents@stentscardio.com', 'Stents y Implantes Cardíacos'],
    ['903345678-3', 'HEMOdinámica S.A.S', 'Iván Darío Barrera', '6044567890', 'hemo@hemodinamica.com', 'Material de Hemodinámica'],
    ['903456789-4', 'MARCAPASOS COLOMBIA', 'Paola Andrea Nieto', '6045678901', 'marcapasos@marcapasosco.com', 'Marcapasos y Dispositivos Cardíacos'],
    ['903567890-5', 'ECOGRAFíA DIAGNí“STICA', 'Felipe Andrés Miranda', '6046789012', 'ecografia@ecodiag.com', 'Equipos de Ecografía'],
    ['903678901-6', 'NEONATAL CARE LTDA', 'María Teresa Ospina', '6047890123', 'neonatal@neonatalcare.com', 'Incubadoras y Equipos Neonatales'],
    ['903789012-7', 'PEDIATRíA PHARMA', 'Camilo Ernesto Rincón', '6048901234', 'pediatria@pediatriapharma.com', 'Medicamentos Pediátricos'],
    ['903890123-8', 'INCUBADORAS COLOMBIA', 'Lina Marcela Parra', '6049012345', 'incubadoras@incubadorasco.com', 'Incubadoras y Radiantes'],
    ['903901234-9', 'FOTOTERAPIA NEONATAL', 'Germán Augusto León', '6050123456', 'fototerapia@fotoneonatal.com', 'Lámparas de Fototerapia'],
    ['904012345-0', 'RESUCITACIí“N PEDIíTRICA', 'Gloria Elena Salazar', '6051234567', 'resucitacion@resucitacionpeds.com', 'Equipos de Resucitación Pediátrica'],
    ['904123456-1', 'RAYOS X MEDICAL', 'Julián Andrés Cifuentes', '6052345678', 'rayosx@rayosxmedical.com', 'Equipos de Rayos X'],
    ['904234567-2', 'RESONANCIA IMAGEN', 'Natalia Andrea Cano', '6053456789', 'rm@resonanciaimagen.com', 'Equipos de Resonancia Magnética'],
    ['904345678-3', 'TOMOGRAFíA AVANZADA', 'Héctor Fabián Acosta', '6054567890', 'tomografia@tomoavanzada.com', 'Tomógrafos'],
    ['904456789-4', 'MAMOGRAFíA ESPECIALIZADA', 'Luz Adriana Fuentes', '6055678901', 'mamografia@mamoesp.com', 'Equipos de Mamografía'],
    ['904567890-5', 'ULTRASONIDO DIAGNí“STICO', 'í“scar Leonardo Jiménez', '6056789012', 'ultrasonido@ultradiag.com', 'Equipos de Ultrasonido'],
    ['904678901-6', 'PAPELERíA HOSPITALARIA', 'Blanca Nubia Arias', '6057890123', 'papeleria@papehosp.com', 'Papelería y Formularios'],
    ['904789012-7', 'LIMPIEZA HOSPITALARIA LTDA', 'Wilson Fernando Peña', '6058901234', 'limpieza@limpiezahosp.com', 'Productos de Aseo Hospitalario'],
    ['904890123-8', 'TEXTIL Mí‰DICO COLOMBIA', 'Yolanda del Carmen Silva', '6059012345', 'textil@textilmedico.com', 'Ropa Quirúrgica y Textil'],
    ['904901234-9', 'MOBILIARIO HOSPITALARIO', 'Ramiro Antonio Delgado', '6060123456', 'mobiliario@mobhosp.com', 'Mobiliario Hospitalario'],
    ['905012345-0', 'EMPAQUES ESTí‰RILES', 'Esperanza Milena Ortega', '6061234567', 'empaques@empaquesesteriles.com', 'Empaques y Empaque Estéril'],
];

echo "=== GENERANDO 50 PROVEEDORES ===\n\n";

$categorias = [];
$exitosos = 0;
$errores = [];

foreach ($proveedores as $prov) {
    try {
        // Usar consulta SQL directa en lugar de prepared statement
        $sql = sprintf(
            "INSERT INTO proveedores (nit, razon_social, contacto, telefono, email) VALUES (%s, %s, %s, %s, %s)",
            $db->quote($prov[0]),
            $db->quote($prov[1]),
            $db->quote($prov[2]),
            $db->quote($prov[3]),
            $db->quote($prov[4])
        );
        
        $db->exec($sql);
        
        $categoria = $prov[5];
        if (!isset($categorias[$categoria])) {
            $categorias[$categoria] = 0;
        }
        $categorias[$categoria]++;
        
        echo "✅ {$prov[1]}\n";
        $exitosos++;
    } catch (Exception $e) {
        echo "âš ï¸  Error con {$prov[1]}: " . $e->getMessage() . "\n";
        $errores[] = $prov[1];
    }
}

echo "\n=== RESUMEN ===\n";
echo "Total proveedores creados: {$exitosos}/50\n";
if (count($errores) > 0) {
    echo "Errores: " . count($errores) . "\n";
}
echo "\n";

echo "CLASIFICACIí“N POR CATEGORíA:\n";
arsort($categorias);
foreach ($categorias as $cat => $cantidad) {
    echo "  â€¢ {$cat}: {$cantidad}\n";
}

echo "\n✅ Proceso completado!\n";
?>
