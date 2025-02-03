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
        error_log("User::getById called with ID: " . $id);
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            error_log("SQL Query prepared: SELECT * FROM users WHERE id = " . $id);
            
            $stmt->execute([$id]);
            error_log("SQL Query executed");
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Query result: " . ($result ? json_encode($result) : "No user found"));
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Error in User::getById: " . $e->getMessage());
            throw $e;
        }
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

    public function getSchoolAdminCount($schoolId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'school_admin'");
        $stmt->execute([$schoolId]);
        return $stmt->fetchColumn();
    }

    public function createSchoolAdmin($data) {
        try {
            // Əvvəlcə istifadəçi adının mövcudluğunu yoxlayaq
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $checkStmt->execute([$data['username']]);
            if ($checkStmt->fetchColumn() > 0) {
                return ['success' => false, 'error' => 'Bu istifadəçi adı artıq mövcuddur'];
            }

            $stmt = $this->db->prepare("INSERT INTO users (name, username, password, role, school_id, is_active) 
                                       VALUES (?, ?, ?, 'school_admin', ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['username'],
                $data['password'],
                $data['school_id'],
                $data['is_active'] ?? true
            ]);
            
            $id = $this->db->lastInsertId();
            return ['success' => true, 'id' => $id];
        } catch (\PDOException $e) {
            error_log("Error creating school admin: " . $e->getMessage());
            if ($e->getCode() == 23000) {
                return ['success' => false, 'error' => 'Bu istifadəçi adı artıq mövcuddur'];
            }
            return ['success' => false, 'error' => 'Server xətası baş verdi'];
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
            $this->db->beginTransaction();
            error_log("Starting delete transaction for user ID: " . $id);

            // Əvvəlcə istifadəçinin məlumatlarını yoxlayaq
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("User data found: " . print_r($user, true));

            if (!$user) {
                throw new \Exception('İstifadəçi tapılmadı');
            }

            if ($user['role'] === 'super_admin') {
                throw new \Exception('Super admin silinə bilməz');
            }

            // İstifadəçi ilə əlaqəli məlumatları təmizlə
            // Burada əlavə cədvəllərdə olan əlaqəli məlumatları da silə bilərsiniz

            // İstifadəçini sil
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND role = 'school_admin'");
            $result = $stmt->execute([$id]);
            error_log("Delete query executed. Result: " . ($result ? 'true' : 'false'));
            error_log("Rows affected: " . $stmt->rowCount());

            if ($stmt->rowCount() === 0) {
                throw new \Exception('İstifadəçi silinmədi. Yalnız məktəb adminləri silinə bilər.');
            }

            $this->db->commit();
            error_log("Transaction committed successfully");
            return ['success' => true];
        } catch (\Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function findBySchool($schoolId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'school_admin'");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}