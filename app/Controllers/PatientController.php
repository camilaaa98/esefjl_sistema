<?php
/**
 * Controlador de Pacientes - ESE Fabio Jaramillo
 */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Repositories/PatientRepository.php';

class PatientController {
    private $patientRepo;

    public function __construct() {
        $this->patientRepo = new PatientRepository(Database::getInstance());
    }

    public function register($data) {
        try {
            // Adaptar datos del formulario al repositorio
            $patientData = [
                'documento' => $data['numero_documento'],
                'nombres' => explode(' ', $data['nombre_completo'])[0],
                'apellidos' => implode(' ', array_slice(explode(' ', $data['nombre_completo']), 1)),
                'celular' => $data['telefono'],
                'eps' => $data['entidad_salud'],
                'sede_id' => $data['sede_id'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'genero' => $data['genero']
            ];

            $this->patientRepo->create($patientData);
            return ['status' => 'success', 'message' => 'Paciente vinculado exitosamente a la red ESEFJL.'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Error al vincular paciente: ' . $e->getMessage()];
        }
    }
}
