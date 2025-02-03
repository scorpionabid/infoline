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
        $sql = "INSERT INTO schools (name, code) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            if (empty($data['name'])) {
                return ['success' => false, 'error' => 'Məktəb adı daxil edilməlidir'];
            }

            // Generate a unique code for the school (e.g., SCH001, SCH002, etc.)
            $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)) as max_code FROM schools WHERE code LIKE 'SCH%'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $maxCode = $result['max_code'] ?? 0;
            $newCode = 'SCH' . str_pad($maxCode + 1, 3, '0', STR_PAD_LEFT);

            // Prepare and execute the insert statement
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['name'], $newCode]);

            return [
                'success' => true, 
                'id' => $this->db->lastInsertId(),
                'message' => 'Məktəb uğurla əlavə edildi'
            ];
        } catch (\PDOException $e) {
            error_log("Error in create school: " . $e->getMessage());
            if ($e->getCode() == 23000) { // Duplicate entry
                return ['success' => false, 'error' => 'Bu adda məktəb artıq mövcuddur'];
            }
            return ['success' => false, 'error' => 'Məktəb əlavə edilərkən xəta baş verdi'];
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
            $this->db->beginTransaction();
            
            // FOR UPDATE ilə məktəbi və adminləri yoxlayaq
            $stmt = $this->db->prepare("SELECT * FROM schools WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $school = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$school) {
                throw new \Exception('Məktəb tapılmadı');
            }

            // Aktiv adminləri yoxlayaq
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'school_admin' FOR UPDATE");
            $stmt->execute([$id]);
            $adminCount = $stmt->fetchColumn();

            if ($adminCount > 0) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Bu məktəbə aid adminlər var. Əvvəlcə onları silin.'];
            }

            // Məktəbi sil
            $stmt = $this->db->prepare("DELETE FROM schools WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Məktəb silinmədi');
            }

            $this->db->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}