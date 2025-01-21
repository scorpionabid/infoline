<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    public function create($data) {
        $sql = "INSERT INTO users (username, password, role, is_active) 
                VALUES (:username, :password, :role, :is_active)";
        
        $params = [
            ':username' => $data['username'],
            ':password' => $data['password'],
            ':role' => $data['role'] ?? 'school_admin',
            ':is_active' => $data['is_active'] ?? true
        ];

        return $this->db->insert($sql, $params);
    }

    public function findByUsername($username) {
        $sql = "SELECT u.*, s.id as school_id, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON s.admin_id = u.id 
                WHERE u.username = :username";
        return $this->db->fetchOne($sql, [':username' => $username]);
    }

    public function findById($id) {
        $sql = "SELECT u.*, s.id as school_id, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON s.admin_id = u.id 
                WHERE u.id = :id";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }

    public function findAllSchoolAdmins() {
        $sql = "SELECT u.*, s.id as school_id, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON s.admin_id = u.id 
                WHERE u.role = 'school_admin' 
                ORDER BY s.name";
        return $this->db->fetchAll($sql);
    }

    public function update($id, $data) {
        $updates = [];
        $params = [':id' => $id];

        // Yalnız verilmiş sahələri yenilə
        if (isset($data['username'])) {
            $updates[] = "username = :username";
            $params[':username'] = $data['username'];
        }
        if (isset($data['password'])) {
            $updates[] = "password = :password";
            $params[':password'] = $data['password'];
        }
        if (isset($data['role'])) {
            $updates[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        if (isset($data['is_active'])) {
            $updates[] = "is_active = :is_active";
            $params[':is_active'] = $data['is_active'];
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = :id";
        return $this->db->execute($sql, $params);
    }

    public function delete($id) {
        // Əvvəlcə school cədvəlindən admin_id referansını təmizlə
        $sql = "UPDATE schools SET admin_id = NULL WHERE admin_id = :id";
        $this->db->execute($sql, [':id' => $id]);

        // Sonra istifadəçini sil
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }

    public function isUsernameUnique($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = [':username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] == 0;
    }
}