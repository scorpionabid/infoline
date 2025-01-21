<?php
namespace App\Models;

use App\Core\Model;

class Column extends Model {
    protected $table = 'columns';

    public function findActive() {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name"
        );
    }

    public function findByDeadline($date) {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
            WHERE deadline <= :date AND is_active = 1
            ORDER BY deadline",
            [':date' => $date]
        );
    }

    public function findWithStats() {
        return $this->db->fetchAll(
            "SELECT c.*, 
                    COUNT(DISTINCT dv.school_id) as filled_schools,
                    (SELECT COUNT(*) FROM schools) as total_schools
            FROM {$this->table} c
            LEFT JOIN data_values dv ON c.id = dv.column_id
            GROUP BY c.id
            ORDER BY c.name"
        );
    }

    public function isNameUnique($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
        $params = [':name' => $name];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] == 0;
    }

    public function updateDeadline($id, $deadline) {
        return $this->db->execute(
            "UPDATE {$this->table} SET deadline = :deadline WHERE id = :id",
            [
                ':deadline' => $deadline,
                ':id' => $id
            ]
        );
    }

    public function toggleActive($id, $active) {
        return $this->db->execute(
            "UPDATE {$this->table} SET is_active = :active WHERE id = :id",
            [
                ':active' => $active ? 1 : 0,
                ':id' => $id
            ]
        );
    }
}