<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Column {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM columns ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM columns WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Eyni adlı sütun varmı yoxla
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM columns WHERE name = ?");
            $stmt->execute([$data['name']]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception('Bu adda sütun artıq mövcuddur');
            }

            // Sütunu əlavə et
            $sql = "INSERT INTO columns (name, type, deadline, is_active) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['type'],
                empty($data['deadline']) ? null : $data['deadline'],
                $data['is_active'] ?? true
            ]);

            if (!$result) {
                throw new \Exception('Sütun əlavə edilmədi');
            }

            $id = $this->db->lastInsertId();
            $this->db->commit();
            
            return [
                'success' => true,
                'id' => $id,
                'message' => 'Sütun uğurla əlavə edildi'
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in Column::create: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Sütunu kilidlə və yoxla
            $stmt = $this->db->prepare("SELECT * FROM columns WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $column = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$column) {
                throw new \Exception('Sütun tapılmadı');
            }

            // Eyni adlı başqa sütun varmı yoxla
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM columns WHERE name = ? AND id != ?");
            $stmt->execute([$data['name'], $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception('Bu adda sütun artıq mövcuddur');
            }

            // Sütunu yenilə
            $sql = "UPDATE columns SET name = ?, type = ?, deadline = ?, is_active = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['type'],
                empty($data['deadline']) ? null : $data['deadline'],
                $data['is_active'] ?? true,
                $id
            ]);

            if (!$result) {
                throw new \Exception('Sütun yenilənmədi');
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Sütun uğurla yeniləndi'
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in Column::update: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // Sütunu kilidlə və yoxla
            $stmt = $this->db->prepare("SELECT * FROM columns WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $column = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$column) {
                throw new \Exception('Sütun tapılmadı');
            }

            // Sütunda data olub-olmadığını yoxla
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM data WHERE column_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception('Bu sütunda məlumatlar var. Əvvəlcə məlumatları silin.');
            }

            // Sütunu sil
            $stmt = $this->db->prepare("DELETE FROM columns WHERE id = ?");
            $result = $stmt->execute([$id]);

            if (!$result) {
                throw new \Exception('Sütun silinmədi');
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Sütun uğurla silindi'
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in Column::delete: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}