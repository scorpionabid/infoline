<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Data {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllSchoolData() {
        $sql = "SELECT d.*, s.name as school_name, c.name as column_name 
                FROM data d
                JOIN schools s ON d.school_id = s.id
                JOIN columns c ON d.column_id = c.id
                ORDER BY s.name, c.name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchoolData($schoolId) {
        $sql = "SELECT d.*, c.name as column_name 
                FROM data d
                JOIN columns c ON d.column_id = c.id
                WHERE d.school_id = ?
                ORDER BY c.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO data (school_id, column_id, value) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                $data['school_id'],
                $data['column_id'],
                $data['value']
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update($schoolId, $columnId, $value) {
        try {
            // First check if data exists
            $stmt = $this->db->prepare("SELECT id FROM data WHERE school_id = ? AND column_id = ?");
            $stmt->execute([$schoolId, $columnId]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Update existing data
                $sql = "UPDATE data SET value = ? WHERE school_id = ? AND column_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$value, $schoolId, $columnId]);
            } else {
                // Insert new data
                $sql = "INSERT INTO data (school_id, column_id, value) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$schoolId, $columnId, $value]);
            }

            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($schoolId, $columnId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM data WHERE school_id = ? AND column_id = ?");
            $stmt->execute([$schoolId, $columnId]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}