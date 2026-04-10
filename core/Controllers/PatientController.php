<?php
/**
 * Controlador de Pacientes - ESE Fabio Jaramillo
 * Lógica de negocio y validación de entrada (SRP).
 */
require_once __DIR__ . '/../Models/PatientModel.php';

class PatientController {
    
    private $model;

    public function __construct() {
        $this->model = new PatientModel();
    }

    public function register($data) {
        // Validaciones básicas
        if (empty($data['nombre_completo']) || empty($data['numero_documento'])) {
            return ['status' => 'error', 'message' => 'Campos obligatorios faltantes.'];
        }

        // Verificar si ya existe
        if ($this->model->getByDocument($data['numero_documento'])) {
            return ['status' => 'error', 'message' => 'El paciente ya se encuentra registrado.'];
        }

        // Inserción
        if ($this->model->create($data)) {
            return ['status' => 'success', 'message' => 'Paciente registrado correctamente.'];
        }

        return ['status' => 'error', 'message' => 'Error interno al registrar paciente.'];
    }

    public function listPatients($sede_id) {
        return $this->model->getAllBySede($sede_id);
    }
}
