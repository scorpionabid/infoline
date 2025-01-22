<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSchoolByAdminId($adminId) {
        $stmt = $this->db->prepare("SELECT s.* FROM schools s 
                                   JOIN users u ON s.id = u.school_id 
                                   WHERE u.id = ? AND u.role = 'school_admin'");
        $stmt->execute([$adminId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllSchoolAdmins() {
        $stmt = $this->db->prepare("SELECT users.*, schools.name as school_name 
                                   FROM users 
                                   LEFT JOIN schools ON users.school_id = schools.id 
                                   WHERE users.role = 'school_admin'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSchoolAdmin($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, username, password, role, school_id) 
                                       VALUES (?, ?, ?, 'school_admin', ?)");
            $stmt->execute([
                $data['name'],
                $data['username'],
                $data['password'],
                $data['school_id']
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET name = ?, username = ?";
        $params = [$data['name'], $data['username']];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = $data['password'];
        }

        if (isset($data['school_id'])) {
            $sql .= ", school_id = ?";
            $params[] = $data['school_id'];
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND role != 'super_admin'");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function findBySchool($schoolId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'school_admin'");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}