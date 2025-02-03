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

    public function getByColumnIds($columnIds) {
        try {
            if (empty($columnIds)) {
                error_log("getByColumnIds called with empty columnIds");
                return [];
            }

            error_log("Getting data for column IDs: " . json_encode($columnIds));

            // Sütun ID-lərini string-ə çevir
            $placeholders = str_repeat('?,', count($columnIds) - 1) . '?';
            
            $sql = "SELECT d.*, s.name as school_name, c.name as column_name 
                    FROM data d
                    JOIN schools s ON d.school_id = s.id
                    JOIN columns c ON d.column_id = c.id
                    WHERE d.column_id IN ($placeholders)
                    ORDER BY s.name, c.name";
            
            error_log("SQL Query: " . $sql);
            error_log("Parameters: " . json_encode($columnIds));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($columnIds);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($result) . " data records");
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Error in getByColumnIds: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            return [];
        }
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

    public function getDataBySchoolsAndColumns($schoolIds, $columnIds) {
        try {
            error_log("=================== getDataBySchoolsAndColumns START ===================");
            error_log("School IDs: " . json_encode($schoolIds));
            error_log("Column IDs: " . json_encode($columnIds));
            
            if (empty($schoolIds) || empty($columnIds)) {
                error_log("Empty school IDs or column IDs");
                return [
                    'success' => true,
                    'data' => []
                ];
            }

            // Create placeholders for the IN clauses
            $schoolPlaceholders = str_repeat('?,', count($schoolIds) - 1) . '?';
            $columnPlaceholders = str_repeat('?,', count($columnIds) - 1) . '?';
            
            $sql = "SELECT d.*, s.name as school_name, c.name as column_name 
                    FROM data d
                    JOIN schools s ON d.school_id = s.id
                    JOIN columns c ON d.column_id = c.id
                    WHERE d.school_id IN ($schoolPlaceholders)
                    AND d.column_id IN ($columnPlaceholders)
                    ORDER BY s.name, c.name";
            
            error_log("SQL Query: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            
            // Combine parameters for execution
            $params = array_merge($schoolIds, $columnIds);
            error_log("Query parameters: " . json_encode($params));
            
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Query result count: " . count($result));
            error_log("Query result: " . json_encode($result));
            error_log("=================== getDataBySchoolsAndColumns END ===================");
            
            return [
                'success' => true,
                'data' => $result
            ];
            
        } catch (\PDOException $e) {
            error_log("Error in getDataBySchoolsAndColumns: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Məlumatları yükləmək mümkün olmadı: ' . $e->getMessage()
            ];
        }
    }
}