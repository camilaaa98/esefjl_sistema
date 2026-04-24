<?php
require_once __DIR__ . '/BaseRepository.php';

class PatientRepository extends BaseRepository {
    
    public function getByDocument($documento) {
        return $this->query("SELECT * FROM pacientes WHERE documento = ?", [$documento])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO pacientes (documento, nombres, apellidos, fecha_nacimiento, genero, direccion, telefono, regimen, sede_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['documento'], $data['nombres'], $data['apellidos'], 
            $data['fecha_nacimiento'], $data['genero'], $data['direccion'], 
            $data['telefono'], $data['regimen'], $data['sede_id']
        ]);
    }

    public function getAllBySede($sede_id) {
        return $this->query("SELECT * FROM pacientes WHERE sede_id = ? ORDER BY nombres ASC", [$sede_id])->fetchAll();
    }
}
?>
