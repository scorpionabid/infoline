<?php
namespace App\Models;

use App\Core\Model;

class School extends Model {
    protected $table = 'schools';

    public function findByAdminId($adminId) {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE admin_id = :admin_id",
            [':admin_id' => $adminId]
        );
    }

    public function findAllWithAdmins() {
        return $this->db->fetchAll(
            "SELECT s.*, u.username as admin_username, u.is_active as admin_is_active
            FROM {$this->table} s
            LEFT JOIN users u ON s.admin_id = u.id
            ORDER BY s.name"
        );
    }

    public function findActiveSchools() {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name"
        );
    }

    public function updateAdminId($schoolId, $adminId) {
        return $this->db->execute(
            "UPDATE {$this->table} SET admin_id = :admin_id WHERE id = :id",
            [
                ':admin_id' => $adminId,
                ':id' => $schoolId
            ]
        );
    }

    public function removeAdmin($schoolId) {
        return $this->db->execute(
            "UPDATE {$this->table} SET admin_id = NULL WHERE id = :id",
            [':id' => $schoolId]
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
}