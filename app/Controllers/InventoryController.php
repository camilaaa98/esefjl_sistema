<?php
/**
 * Controlador de Inventario - ESE Fabio Jaramillo
 * Siguiendo principios SOLID (SRP, DIP).
 * La clase depende de la abstracciÛn del Repositorio.
 */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Repositories/InventoryRepository.php';

class InventoryController {
    
    private $repository;

    /**
     * InyecciÛn de Dependencias para desacoplar el acceso a datos.
     */
    public function __construct(InventoryRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Factory Method para compatibilidad con llamadas r·pidas (o usar un contenedor)
     */
    public static function getInstance() {
        $db = Database::getInstance();
        $repo = new InventoryRepository($db);
        return new self($repo);
    }

    public function getInventoryBySede($sede_id) {
        return $this->repository->getInventoryBySede($sede_id);
    }

    public static function getStockBadge($item) {
        $current = $item['stock_actual'] ?? 0;
        $min = $item['stock_minimo_referencia'] ?? $item['stock_minimo'] ?? 25;
        $upper_min = $min * 1.20;

        if ($current < $min) {
            return '<span class="px-3 py-1 bg-red-50 text-red-600 text-[9px] font-black uppercase rounded-full border border-red-100 shadow-sm">DÌâFICIT CRÌçTICO</span>';
        } elseif ($current >= $min && $current <= $upper_min) {
            return '<span class="px-3 py-1 bg-amber-50 text-amber-700 text-[9px] font-black uppercase rounded-full border border-amber-100">RIESGO STOCK</span>';
        } else {
            return '<span class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black uppercase rounded-full border border-slate-800">ÌìPTIMO</span>';
        }
    }

    public static function getExpiryBadge($item) {
        $expiry = $item['fecha_vencimiento'] ?? null;
        $today = date('Y-m-d');
        $warning_date = date('Y-m-d', strtotime('+3 months'));

        if (!empty($item['requiere_frio'])) {
            return '<span class="px-3 py-1 bg-blue-600 text-white text-[9px] font-black uppercase rounded-full border border-blue-400">CADENA DE FRÌçO</span>';
        }
        if (!empty($item['es_delicado'])) {
            return '<span class="px-3 py-1 bg-red-800 text-white text-[9px] font-black uppercase rounded-full border border-red-900">ALTA COMPLEJIDAD</span>';
        }
        if ($expiry === null) {
            return '<span class="px-3 py-1 bg-[#d4af37] text-black text-[9px] font-black uppercase rounded-full border border-[#b49020]">SIN VENCIMIENTO</span>';
        } elseif ($expiry < $today) {
            $formatted_date = date('d/m/Y', strtotime($expiry));
            return '<span class="px-3 py-1 bg-red-600 text-white text-[9px] font-black uppercase rounded-full border border-red-400 shadow-lg">' . $formatted_date . '</span>';
        } elseif ($expiry < $warning_date) {
            $formatted_date = date('d/m/Y', strtotime($expiry));
            return '<span class="px-3 py-1 bg-amber-400 text-black text-[9px] font-black uppercase rounded-full border border-amber-500">' . $formatted_date . '</span>';
        } else {
            $formatted_date = date('d/m/Y', strtotime($expiry));
            return '<span class="px-3 py-1 bg-slate-100 text-slate-400 text-[9px] font-black uppercase rounded-full border border-slate-200">' . $formatted_date . '</span>';
        }
    }

    public function getSummaryBySede() {
        $sql = "SELECT s.nombre, s.id,
                       COUNT(i.id) as total_items,
                       SUM(CASE WHEN i.stock_actual < s.stock_minimo_referencia THEN 1 ELSE 0 END) as items_criticos,
                       SUM(i.stock_actual) as stock_total
                FROM sedes s
                LEFT JOIN inventario i ON s.id = i.sede_id
                GROUP BY s.id, s.nombre
                ORDER BY s.nombre ASC";
        return $this->repository->query($sql)->fetchAll();
    }

    public function getUniqueLaboratories() {
        return $this->repository->query("SELECT DISTINCT laboratorio FROM productos WHERE laboratorio IS NOT NULL ORDER BY laboratorio ASC")->fetchAll();
    }

    public function getUniqueProductNames() {
        return $this->repository->query("SELECT DISTINCT nombre_generico FROM productos ORDER BY nombre_generico ASC")->fetchAll();
    }

    public function getFilteredInventory($filters = [], $limit = 10, $offset = 0) {
        return $this->repository->getFilteredStock($filters, $limit, $offset);
    }

    public function getFilteredInventoryCount($filters = []) {
        return $this->repository->getFilteredStockCount($filters);
    }

    public function getExpiredInventory() {
        return $this->repository->getExpired();
    }

    public function canSupplyAllIPS() {
        $faltantes = $this->repository->getFaltantesMunicipales();
        if (empty($faltantes)) return true;

        $db = Database::getInstance();
        $florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
        
        foreach ($faltantes as $f) {
            $stmtCedis = $db->prepare("SELECT stock_actual FROM inventario WHERE sede_id = ? AND producto_id = ?");
            $stmtCedis->execute([$florencia_id, $f['producto_id']]);
            $stockCedis = $stmtCedis->fetchColumn() ?: 0;
            
            if ($stockCedis < $f['total_faltante']) return false;
        }

        return true;
    }

    public function darDeBaja($id) {
        return $this->repository->deleteItem($id);
    }
}

