<?php
namespace App\Models;

use App\Core\Database;

class Category {
    private $db;
    private $table = 'categories';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        try {
            error_log("Category->getAll: Attempting to fetch all categories");
            
            $sql = "SELECT id, name, description, created_at, updated_at 
                   FROM {$this->table} 
                   WHERE deleted_at IS NULL 
                   ORDER BY name ASC";
            
            $result = $this->db->fetchAll($sql);
            
            if (empty($result)) {
                error_log("Category->getAll: No categories found");
                return [];
            }
            
            error_log("Category->getAll: Successfully retrieved " . count($result) . " categories");
            return $result;
            
        } catch (\Exception $e) {
            error_log("Category->getAll Exception: " . $e->getMessage());
            error_log("Category->getAll Stack trace: " . $e->getTraceAsString());
            throw new \Exception("Kateqoriyaları əldə edərkən xəta baş verdi");
        }
    }

    public function findByName($name) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE name = :name AND deleted_at IS NULL LIMIT 1";
            $result = $this->db->fetchAll($sql, ['name' => $name]);
            
            return !empty($result) ? $result[0] : null;
        } catch (\Exception $e) {
            error_log("Category->findByName Exception: " . $e->getMessage());
            return null;
        }
    }

    public function getDefaultCategory() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE is_default = 1 AND deleted_at IS NULL LIMIT 1";
            $result = $this->db->fetchAll($sql);
            
            return !empty($result) ? $result[0] : null;
        } catch (\Exception $e) {
            error_log("Category->getDefaultCategory Exception: " . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            error_log("Category->create: Starting with data: " . print_r($data, true));
            
            // Əvvəlcə eyni adlı kateqoriyanın olub-olmadığını yoxlayırıq
            $existingCategory = $this->findByName($data['name']);
            if ($existingCategory) {
                error_log("Category->create: Category with name '{$data['name']}' already exists");
                return false;
            }
            
            // SQL injection-dan qorunmaq üçün prepared statement istifadə edirik
            $sql = "INSERT INTO {$this->table} (name, description, is_default, created_at, updated_at) 
                    VALUES (:name, :description, :is_default, :created_at, :updated_at)";
            
            $params = [
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'is_default' => $data['is_default'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            error_log("Category->create: Executing query with params: " . print_r($params, true));
            
            $result = $this->db->query($sql, $params);
            
            if ($result) {
                $id = $this->db->lastInsertId();
                error_log("Category->create: Success, new ID: " . $id);
                return $id;
            }
            
            error_log("Category->create: Failed to insert");
            return false;
            
        } catch (\Exception $e) {
            error_log("Category->create Exception: " . $e->getMessage());
            error_log("Category->create Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL LIMIT 1";
            $result = $this->db->fetchAll($sql, ['id' => $id]);
            
            return !empty($result) ? $result[0] : null;
        } catch (\Exception $e) {
            error_log("Category->getById Exception: " . $e->getMessage());
            return null;
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET name = :name, 
                        description = :description,
                        updated_at = :updated_at 
                    WHERE id = :id";
            
            $params = [
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'updated_at' => $data['updated_at']
            ];
            
            $result = $this->db->query($sql, $params);
            return $result !== false;
        } catch (\Exception $e) {
            error_log("Category->update Exception: " . $e->getMessage());
            return false;
        }
    }
}