<?php
/**
 * Modelo de Pacientes - ESE Fabio Jaramillo
 * Siguiendo SRP: Encapsulación de lógica de datos.
 */
require_once __DIR__ . '/../Database.php';

class PatientModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO pacientes (
                nombre_completo, tipo_documento, numero_documento, 
                fecha_nacimiento, genero, direccion, telefono, 
                entidad_salud, sede_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre_completo'], $data['tipo_documento'], $data['numero_documento'],
            $data['fecha_nacimiento'], $data['genero'], $data['direccion'],
            $data['telefono'], $data['entidad_salud'], $data['sede_id']
        ]);
    }

    public function getByDocument($numero_documento) {
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE numero_documento = ?");
        $stmt->execute([$numero_documento]);
        return $stmt->fetch();
    }

    public function getAllBySede($sede_id) {
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE sede_id = ? ORDER BY nombre_completo ASC");
        $stmt->execute([$sede_id]);
        return $stmt->fetchAll();
    }
}
