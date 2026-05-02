<?php
/**
 * Script para generar 50 proveedores ficticios con clasificación de productos
 * ESE Fabio Jaramillo - Sistema Farmacéutico
 */

require_once __DIR__ . '/../app/config/Database.php';

echo "=== CONECTANDO A BASE DE DATOS ===\n";

$db = Database::getInstance();

// Verificar/crear tabla de proveedores
$db->exec("CREATE TABLE IF NOT EXISTS proveedores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nit TEXT UNIQUE NOT NULL,
    razon_social TEXT NOT NULL,
    contacto TEXT,
    telefono TEXT,
    email TEXT
)");

echo "✅ Tabla proveedores verificada\n\n";

// Array de proveedores con sus clasificaciones
$proveedores = [
    // Laboratorios farmacéuticos nacionales
    ['nit' => '900123456-1', 'razon_social' => 'LABORATORIOS GENFARCO S.A.S', 'contacto' => 'Carlos Rodríguez', 'telefono' => '6012345678', 'email' => 'contacto@genfarco.com', 'categoria' => 'Medicamentos Genéricos'],
    ['nit' => '900234567-2', 'razon_social' => 'NOVAMED COLOMBIA LTDA', 'contacto' => 'María Fernanda López', 'telefono' => '6013456789', 'email' => 'pedidos@novamed.co', 'categoria' => 'Medicamentos de Marca'],
    ['nit' => '900345678-3', 'razon_social' => 'FARMACOL S.A.', 'contacto' => 'Jorge Hernández', 'telefono' => '6014567890', 'email' => 'ventas@farmacol.com', 'categoria' => 'Antibióticos'],
    ['nit' => '900456789-4', 'razon_social' => 'BIOSALUD PHARMA', 'contacto' => 'Ana Patricia Gómez', 'telefono' => '6015678901', 'email' => 'distribucion@biosalud.com', 'categoria' => 'Biológicos y Vacunas'],
    ['nit' => '900567890-5', 'razon_social' => 'INMUNOFAR LTDA', 'contacto' => 'Luis Alberto Castro', 'telefono' => '6016789012', 'email' => 'pedidos@inmunofar.com', 'categoria' => 'Inmunoglobulinas'],
    ['nit' => '900678901-6', 'razon_social' => 'DROGUERíA EL SALVADOR', 'contacto' => 'Diana Marcela Ruiz', 'telefono' => '6017890123', 'email' => 'drogueria@es.com', 'categoria' => 'Drogas y Principios Activos'],
    ['nit' => '900789012-7', 'razon_social' => 'COLOMBIANA DE ANTIBIí“TICOS', 'contacto' => 'Roberto Carlos Pérez', 'telefono' => '6018901234', 'email' => 'ventas@coantibioticos.com', 'categoria' => 'Antibióticos'],
    ['nit' => '900890123-8', 'razon_social' => 'DIABETES CARE COLOMBIA', 'contacto' => 'Lucía Elena Martínez', 'telefono' => '6019012345', 'email' => 'insumos@diabetescare.co', 'categoria' => 'Insumos para Diabetes'],
    ['nit' => '900901234-9', 'razon_social' => 'ONCOFARMA S.A.S', 'contacto' => 'Fernando José Díaz', 'telefono' => '6010123456', 'email' => 'onco@oncofarma.co', 'categoria' => 'Medicamentos Oncológicos'],
    ['nit' => '901012345-0', 'razon_social' => 'NEUROLOGíA PHARMA', 'contacto' => 'Carmen Rosa Vega', 'telefono' => '6011234567', 'email' => 'neuro@neuropharma.com', 'categoria' => 'Medicamentos Neurológicos'],
    
    // Equipos médicos
    ['nit' => '901123456-1', 'razon_social' => 'MEDICAL EQUIPMENT SAS', 'contacto' => 'Andrés Felipe Torres', 'telefono' => '6022345678', 'email' => 'equipos@medicaleq.com', 'categoria' => 'Equipos Médicos'],
    ['nit' => '901234567-2', 'razon_social' => 'SUMINISTROS HOSPITALARIOS LTDA', 'contacto' => 'Patricia Elena Morales', 'telefono' => '6023456789', 'email' => 'hospital@suministros.com', 'categoria' => 'Material de Curación'],
    ['nit' => '901345678-3', 'razon_social' => 'OXíGENO MEDICAL COLOMBIA', 'contacto' => 'Juan David Ramírez', 'telefono' => '6024567890', 'email' => 'oxigeno@oximedical.co', 'categoria' => 'Gases Medicinales'],
    ['nit' => '901456789-4', 'razon_social' => 'ORTHOPEDIA Tí‰CNICA', 'contacto' => 'Silvia Juliana Ortiz', 'telefono' => '6025678901', 'email' => 'ortesis@orthopedia.com', 'categoria' => 'Prótesis y í“rtesis'],
    ['nit' => '901567890-5', 'razon_social' => 'DIAGNí“STICA LTDA', 'contacto' => 'Miguel íngel Suárez', 'telefono' => '6026789012', 'email' => 'reactivos@diagnostica.com', 'categoria' => 'Reactivos de Laboratorio'],
    
    // Dispositivos médicos
    ['nit' => '901678901-6', 'razon_social' => 'DISPOSMED S.A.S', 'contacto' => 'Laura Carolina Mendoza', 'telefono' => '6027890123', 'email' => 'dispositivos@disposmed.co', 'categoria' => 'Dispositivos Médicos'],
    ['nit' => '901789012-7', 'razon_social' => 'CATí‰TERES Y SONDA COLOMBIA', 'contacto' => 'Pedro Antonio Ríos', 'telefono' => '6028901234', 'email' => 'cateteres@cyc.com', 'categoria' => 'Catéteres y Sondas'],
    ['nit' => '901890123-8', 'razon_social' => 'INFUSIí“N Mí‰DICA LTDA', 'contacto' => 'Marcela Andrea Flórez', 'telefono' => '6029012345', 'email' => 'equipos@infusionmedica.com', 'categoria' => 'Equipos de Infusión'],
    ['nit' => '901901234-9', 'razon_social' => 'CIRUGíA ESPECIALIZADA SAS', 'contacto' => 'Diego Armando Sánchez', 'telefono' => '6020123456', 'email' => 'cirugia@cirugiaesp.com', 'categoria' => 'Material de Cirugía'],
    ['nit' => '902012345-0', 'razon_social' => 'ESTí‰RIL COLOMBIA', 'contacto' => 'Natalia Fernanda Castro', 'telefono' => '6031234567', 'email' => 'esteril@esterilcolombia.com', 'categoria' => 'Esterilización y Antisépticos'],
    
    // Nutrición y suplementos
    ['nit' => '902123456-1', 'razon_social' => 'NUTRICIí“N CLíNICA SAS', 'contacto' => 'Gabriel Ernesto Vargas', 'telefono' => '6032345678', 'email' => 'nutricion@nutriclinica.com', 'categoria' => 'Fórmulas Nutricionales'],
    ['nit' => '902234567-2', 'razon_social' => 'SUPLEMENTOS HOSPITALARIOS', 'contacto' => 'Verónica Alejandra Quintero', 'telefono' => '6033456789', 'email' => 'suplementos@suphosp.com', 'categoria' => 'Suplementos Nutricionales'],
    ['nit' => '902345678-3', 'razon_social' => 'LACTEOS ESPECIALIZADOS LTDA', 'contacto' => 'Ricardo José Mendez', 'telefono' => '6034567890', 'email' => 'formulas@lacteosesp.com', 'categoria' => 'Fórmulas Infantiles'],
    ['nit' => '902456789-4', 'razon_social' => 'VITAMINAS COLOMBIA', 'contacto' => 'Andrea Catalina Peña', 'telefono' => '6035678901', 'email' => 'vitaminas@vitco.com', 'categoria' => 'Vitaminas y Minerales'],
    ['nit' => '902567890-5', 'razon_social' => 'ENTERAL NUTRITION', 'contacto' => 'Sebastián Alberto Cruz', 'telefono' => '6036789012', 'email' => 'enteral@enteralnutrition.com', 'categoria' => 'Nutrición Enteral'],
    
    // Anestesia y UCI
    ['nit' => '902678901-6', 'razon_social' => 'ANESTESIA TOTAL LTDA', 'contacto' => 'Carolina del Pilar Guzmán', 'telefono' => '6037890123', 'email' => 'anestesia@anestotal.com', 'categoria' => 'Medicamentos Anestésicos'],
    ['nit' => '902789012-7', 'razon_social' => 'UCI PHARMA S.A.S', 'contacto' => 'Eduardo Alonso Palacios', 'telefono' => '6038901234', 'email' => 'uci@ucipharma.com', 'categoria' => 'Medicamentos UCI'],
    ['nit' => '902890123-8', 'razon_social' => 'VENTILACIí“N Mí‰DICA', 'contacto' => 'Isabella Cristina Medina', 'telefono' => '6039012345', 'email' => 'ventilacion@ventmedica.com', 'categoria' => 'Equipos de Ventilación'],
    ['nit' => '902901234-9', 'razon_social' => 'MONITORíA HOSPITALARIA', 'contacto' => 'Alejandro Enrique Duarte', 'telefono' => '6030123456', 'email' => 'monitores@monitoreahosp.com', 'categoria' => 'Monitores Multiparamétricos'],
    ['nit' => '903012345-0', 'razon_social' => 'DESFIBRILADORES COLOMBIA', 'contacto' => 'Diana Michelle Briceño', 'telefono' => '6041234567', 'email' => 'desfibriladores@desfcol.com', 'categoria' => 'Desfibriladores'],
    
    // Cardiología
    ['nit' => '903123456-1', 'razon_social' => 'CARDIOFARMA LTDA', 'contacto' => 'Mauricio Javier Valencia', 'telefono' => '6042345678', 'email' => 'cardio@cardiofarma.com', 'categoria' => 'Medicamentos Cardiovasculares'],
    ['nit' => '903234567-2', 'razon_social' => 'STENTS CARDIOVASCULAR', 'contacto' => 'Lorena Patricia Cárdenas', 'telefono' => '6043456789', 'email' => 'stents@stentscardio.com', 'categoria' => 'Stents y Implantes Cardíacos'],
    ['nit' => '903345678-3', 'razon_social' => 'HEMOdinámica S.A.S', 'contacto' => 'Iván Darío Barrera', 'telefono' => '6044567890', 'email' => 'hemo@hemodinamica.com', 'categoria' => 'Material de Hemodinámica'],
    ['nit' => '903456789-4', 'razon_social' => 'MARCAPASOS COLOMBIA', 'contacto' => 'Paola Andrea Nieto', 'telefono' => '6045678901', 'email' => 'marcapasos@marcapasosco.com', 'categoria' => 'Marcapasos y Dispositivos Cardíacos'],
    ['nit' => '903567890-5', 'razon_social' => 'ECOGRAFíA DIAGNí“STICA', 'contacto' => 'Felipe Andrés Miranda', 'telefono' => '6046789012', 'email' => 'ecografia@ecodiag.com', 'categoria' => 'Equipos de Ecografía'],
    
    // Pediatría y neonatología
    ['nit' => '903678901-6', 'razon_social' => 'NEONATAL CARE LTDA', 'contacto' => 'María Teresa Ospina', 'telefono' => '6047890123', 'email' => 'neonatal@neonatalcare.com', 'categoria' => 'Incubadoras y Equipos Neonatales'],
    ['nit' => '903789012-7', 'razon_social' => 'PEDIATRíA PHARMA', 'contacto' => 'Camilo Ernesto Rincón', 'telefono' => '6048901234', 'email' => 'pediatria@pediatriapharma.com', 'categoria' => 'Medicamentos Pediátricos'],
    ['nit' => '903890123-8', 'razon_social' => 'INCUBADORAS COLOMBIA', 'contacto' => 'Lina Marcela Parra', 'telefono' => '6049012345', 'email' => 'incubadoras@incubadorasco.com', 'categoria' => 'Incubadoras y Radiantes'],
    ['nit' => '903901234-9', 'razon_social' => 'FOTOTERAPIA NEONATAL', 'contacto' => 'Germán Augusto León', 'telefono' => '6040123456', 'email' => 'fototerapia@fotoneonatal.com', 'categoria' => 'Lámparas de Fototerapia'],
    ['nit' => '904012345-0', 'razon_social' => 'RESUCITACIí“N PEDIíTRICA', 'contacto' => 'Gloria Elena Salazar', 'telefono' => '6051234567', 'email' => 'resucitacion@resucitacionpeds.com', 'categoria' => 'Equipos de Resucitación Pediátrica'],
    
    // Rayos X y Diagnóstico por Imagen
    ['nit' => '904123456-1', 'razon_social' => 'RAYOS X MEDICAL', 'contacto' => 'Julián Andrés Cifuentes', 'telefono' => '6052345678', 'email' => 'rayosx@rayosxmedical.com', 'categoria' => 'Equipos de Rayos X'],
    ['nit' => '904234567-2', 'razon_social' => 'RESONANCIA IMAGEN', 'contacto' => 'Natalia Andrea Cano', 'telefono' => '6053456789', 'email' => 'rm@resonanciaimagen.com', 'categoria' => 'Equipos de Resonancia Magnética'],
    ['nit' => '904345678-3', 'razon_social' => 'TOMOGRAFíA AVANZADA', 'contacto' => 'Héctor Fabián Acosta', 'telefono' => '6054567890', 'email' => 'tomografia@tomoavanzada.com', 'categoria' => 'Tomógrafos'],
    ['nit' => '904456789-4', 'razon_social' => 'MAMOGRAFíA ESPECIALIZADA', 'contacto' => 'Luz Adriana Fuentes', 'telefono' => '6055678901', 'email' => 'mamografia@mamoesp.com', 'categoria' => 'Equipos de Mamografía'],
    ['nit' => '904567890-5', 'razon_social' => 'ULTRASONIDO DIAGNí“STICO', 'contacto' => 'í“scar Leonardo Jiménez', 'telefono' => '6056789012', 'email' => 'ultrasonido@ultradiag.com', 'categoria' => 'Equipos de Ultrasonido'],
    
    // Consumibles y misceláneos
    ['nit' => '904678901-6', 'razon_social' => 'PAPELERíA HOSPITALARIA', 'contacto' => 'Blanca Nubia Arias', 'telefono' => '6057890123', 'email' => 'papeleria@papehosp.com', 'categoria' => 'Papelería y Formularios'],
    ['nit' => '904789012-7', 'razon_social' => 'LIMPIEZA HOSPITALARIA LTDA', 'contacto' => 'Wilson Fernando Peña', 'telefono' => '6058901234', 'email' => 'limpieza@limpiezahosp.com', 'categoria' => 'Productos de Aseo Hospitalario'],
    ['nit' => '904890123-8', 'razon_social' => 'TEXTIL Mí‰DICO COLOMBIA', 'contacto' => 'Yolanda del Carmen Silva', 'telefono' => '6059012345', 'email' => 'textil@textilmedico.com', 'categoria' => 'Ropa Quirúrgica y Textil'],
    ['nit' => '904901234-9', 'razon_social' => 'MOBILIARIO HOSPITALARIO', 'contacto' => 'Ramiro Antonio Delgado', 'telefono' => '6050123456', 'email' => 'mobiliario@mobhosp.com', 'categoria' => 'Mobiliario Hospitalario'],
    ['nit' => '905012345-0', 'razon_social' => 'EMPAQUES ESTí‰RILES', 'contacto' => 'Esperanza Milena Ortega', 'telefono' => '6061234567', 'email' => 'empaques@empaquesesteriles.com', 'categoria' => 'Empaques y Empaque Estéril'],
];

// Insertar proveedores
$stmt = $db->prepare("INSERT INTO proveedores (nit, razon_social, contacto, telefono, email) VALUES (?, ?, ?, ?, ?)");

echo "=== GENERANDO 50 PROVEEDORES ===\n\n";

$categorias = [];
$exitosos = 0;

foreach ($proveedores as $prov) {
    try {
        $stmt->execute([
            $prov['nit'],
            $prov['razon_social'],
            $prov['contacto'],
            $prov['telefono'],
            $prov['email']
        ]);
        
        $categoria = $prov['categoria'];
        if (!isset($categorias[$categoria])) {
            $categorias[$categoria] = 0;
        }
        $categorias[$categoria]++;
        
        echo "✅ {$prov['razon_social']} - {$categoria}\n";
        $exitosos++;
    } catch (Exception $e) {
        echo "âš ï¸  Error con {$prov['razon_social']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "Total proveedores creados: {$exitosos}/50\n\n";

echo "CLASIFICACIí“N POR CATEGORíA:\n";
foreach ($categorias as $cat => $cantidad) {
    echo "  â€¢ {$cat}: {$cantidad} proveedores\n";
}

echo "\n✅ Proceso completado exitosamente!\n";
?>
