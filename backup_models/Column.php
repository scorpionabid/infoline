<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Column {
    private $db;
    private $table = 'columns';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        try {
            error_log("getAll method called");
            
            $sql = "SELECT c.*, cat.name as category_name 
                    FROM {$this->table} c 
                    LEFT JOIN categories cat ON c.category_id = cat.id 
                    WHERE c.deleted_at IS NULL
                    ORDER BY COALESCE(cat.name, 'Digər'), c.name ASC";
            
            error_log("SQL query: " . $sql);
            $result = $this->db->fetchAll($sql);
            error_log("Query result: " . json_encode($result));
            
            return [
                'success' => true,
                'data' => $result ?: []
            ];
        } catch (\PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Sütunlar yüklənmədi: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function getAllByCategoryId($categoryId) {
        try {
            error_log("=================== getAllByCategoryId START ===================");
            error_log("Incoming categoryId: " . $categoryId);
            error_log("Category ID type: " . gettype($categoryId));
            
            // Kateqoriya ID-sini integer-ə çevir
            $categoryId = (int)$categoryId;
            error_log("Converted category ID: " . $categoryId);
            
            // Əvvəlcə kateqoriyanın mövcudluğunu yoxlayaq
            $checkSql = "SELECT COUNT(*) FROM categories WHERE id = :categoryId";
            $params = ['categoryId' => $categoryId];
            
            error_log("Checking category existence...");
            error_log("Check SQL: " . $checkSql);
            error_log("Check params: " . json_encode($params));
            
            $categoryExists = $this->db->fetchColumn($checkSql, $params);
            error_log("Category exists check: " . ($categoryExists ? 'Yes' : 'No'));
            
            if (!$categoryExists) {
                error_log("Category not found!");
                return [
                    'success' => false,
                    'message' => 'Kateqoriya tapılmadı'
                ];
            }
            
            // Kateqoriyaya aid sütunları al
            $sql = "SELECT c.*, cat.name as category_name 
                    FROM {$this->table} c 
                    LEFT JOIN categories cat ON c.category_id = cat.id 
                    WHERE c.category_id = :categoryId 
                    AND c.deleted_at IS NULL 
                    ORDER BY c.name ASC";
            
            error_log("Fetching columns...");
            error_log("SQL query: " . $sql);
            error_log("Parameters: " . json_encode($params));
            
            $result = $this->db->fetchAll($sql, $params);
            error_log("Query result count: " . count($result));
            error_log("Query result: " . json_encode($result));
            
            if (empty($result)) {
                error_log("No columns found for category");
                return [
                    'success' => false,
                    'message' => 'Bu kateqoriya üçün heç bir sütun tapılmadı'
                ];
            }
            
            $response = [
                'success' => true,
                'data' => $result
            ];
            
            error_log("Returning response: " . json_encode($response));
            error_log("=================== getAllByCategoryId END ===================");
            
            return $response;
            
        } catch (\PDOException $e) {
            error_log("Error in getAllByCategoryId: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Sütunlar yüklənmədi: ' . $e->getMessage()
            ];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT c.*, cat.name as category_name 
                    FROM {$this->table} c 
                    LEFT JOIN categories cat ON c.category_id = cat.id 
                    WHERE c.id = :id AND c.deleted_at IS NULL";
            
            $params = ['id' => $id];
            $result = $this->db->fetchOne($sql, $params);
            
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Eyni adlı sütun varmı yoxla
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE name = :name AND deleted_at IS NULL";
            $count = $this->db->fetchColumn($sql, ['name' => $data['name']]);
            
            if ($count > 0) {
                throw new \Exception('Bu adda sütun artıq mövcuddur');
            }

            // Sütunu əlavə et
            $sql = "INSERT INTO {$this->table} (name, type, deadline, is_active, category_id, created_at, updated_at) 
                    VALUES (:name, :type, :deadline, :is_active, :category_id, :created_at, :updated_at)";
            
            $params = [
                'name' => $data['name'],
                'type' => $data['type'],
                'deadline' => $data['deadline'],
                'is_active' => $data['is_active'],
                'category_id' => $data['category_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->execute($sql, $params);
            $columnId = $this->db->lastInsertId();
            
            $this->db->commit();
            return $columnId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in create: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            // Eyni adlı başqa sütun varmı yoxla
            $sql = "SELECT COUNT(*) FROM {$this->table} 
                    WHERE name = :name AND id != :id AND deleted_at IS NULL";
            $count = $this->db->fetchColumn($sql, [
                'name' => $data['name'],
                'id' => $id
            ]);
            
            if ($count > 0) {
                throw new \Exception('Bu adda sütun artıq mövcuddur');
            }

            // Sütunu yenilə
            $sql = "UPDATE {$this->table} 
                    SET name = :name, 
                        type = :type, 
                        deadline = :deadline, 
                        is_active = :is_active, 
                        category_id = :category_id,
                        updated_at = :updated_at 
                    WHERE id = :id";
            
            $params = [
                'id' => $id,
                'name' => $data['name'],
                'type' => $data['type'],
                'deadline' => $data['deadline'],
                'is_active' => $data['is_active'],
                'category_id' => $data['category_id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->execute($sql, $params);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET deleted_at = :deleted_at 
                    WHERE id = :id";
            
            $params = [
                'id' => $id,
                'deleted_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->db->execute($sql, $params);
        } catch (\PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }
}