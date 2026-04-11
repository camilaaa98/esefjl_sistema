<?php
/**
 * Clase Base para Repositorios (Principios SOLID)
 * Garantiza una interfaz común para el acceso a datos.
 */
abstract class BaseRepository {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Helper para preparar y ejecutar consultas de forma segura.
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>
