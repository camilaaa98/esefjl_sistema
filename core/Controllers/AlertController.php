<?php
require_once __DIR__ . '/../Infrastructure/Database.php';

class AlertController {

    public static function getInactivityAlerts() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, nombre, ultima_pedido_at FROM sedes WHERE tipo = 'MUNICIPIO'");
        $sedes = $stmt->fetchAll();

        $alerts = [];
        $now = new DateTime();

        foreach ($sedes as $sede) {
            $lastDate = new DateTime($sede['ultima_pedido_at']);
            $diff = $now->diff($lastDate)->days;

            $level = 0; // 0: Normal, 1: Regente (7d), 2: Subgerente (15d), 3: Gerente (20d)
            $msg = "";

            if ($diff >= 20) {
                $level = 3;
                $msg = "CRÍTICO: Sede {$sede['nombre']} sin pedidos hace {$diff} días. (GERENCIA)";
            } elseif ($diff >= 15) {
                $level = 2;
                $msg = "ALERTA: Sede {$sede['nombre']} sin actividad hace {$diff} días. (SUBGERENCIA SALUD)";
            } elseif ($diff >= 7) {
                $level = 1;
                $msg = "AVISO: Sede {$sede['nombre']} requiere revisión de stock ({$diff} días). (REGENTE)";
            }

            if ($level > 0) {
                $alerts[] = [
                    'sede_id' => $sede['id'],
                    'sede_nombre' => $sede['nombre'],
                    'dias' => $diff,
                    'nivel' => $level,
                    'mensaje' => $msg
                ];
            }
        }
        return $alerts;
    }

    public static function getAlertCountByLevel($level) {
        $alerts = self::getInactivityAlerts();
        $count = 0;
        foreach($alerts as $a) if($a['nivel'] >= $level) $count++;
        return $count;
    }
}
?>
