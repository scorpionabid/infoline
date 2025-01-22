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
        $sql = "INSERT INTO columns (name, type, deadline, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['deadline'] ?: null,
                $data['is_active'] ?? true
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE columns SET name = ?, type = ?, deadline = ?, is_active = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                $data['name'],
                $data['type'],
                $data['deadline'] ?: null,
                $data['is_active'] ?? true,
                $id
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM columns WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}