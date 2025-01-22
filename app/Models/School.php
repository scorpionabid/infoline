<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class School {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM schools ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Admin sayını ayrıca hesablayaq
        foreach ($schools as &$school) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'school_admin'");
            $stmt->execute([$school['id']]);
            $school['admin_count'] = $stmt->fetchColumn();
        }

        return $schools;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM schools WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO schools (name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$data['name']]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE schools SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$data['name'], $id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            // Əvvəlcə məktəbə aid adminləri yoxlayaq
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'school_admin'");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'error' => 'Bu məktəbə aid adminlər var. Əvvəlcə onları silin.'];
            }

            $stmt = $this->db->prepare("DELETE FROM schools WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}