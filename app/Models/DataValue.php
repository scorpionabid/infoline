<?php
namespace App\Models;

use App\Core\Model;

class DataValue extends Model {
    protected $table = 'data_values';

    public function getAllSchoolData() {
        return $this->db->fetchAll(
            "SELECT dv.*, s.name as school_name, c.name as column_name 
            FROM {$this->table} dv
            JOIN schools s ON dv.school_id = s.id
            JOIN columns c ON dv.column_id = c.id"
        );
    }

    public function getSchoolData($schoolId) {
        return $this->db->fetchAll(
            "SELECT dv.*, c.name as column_name 
            FROM {$this->table} dv
            JOIN columns c ON dv.column_id = c.id
            WHERE dv.school_id = :school_id",
            [':school_id' => $schoolId]
        );
    }

    public function findBySchoolAndColumn($schoolId, $columnId) {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} 
            WHERE school_id = :school_id AND column_id = :column_id",
            [
                ':school_id' => $schoolId,
                ':column_id' => $columnId
            ]
        );
    }

    public function getDataByDeadline($deadline) {
        return $this->db->fetchAll(
            "SELECT dv.*, s.name as school_name, c.name as column_name 
            FROM {$this->table} dv
            JOIN schools s ON dv.school_id = s.id
            JOIN columns c ON dv.column_id = c.id
            WHERE c.deadline <= :deadline",
            [':deadline' => $deadline]
        );
    }

    public function getMissingData() {
        return $this->db->fetchAll(
            "SELECT s.id as school_id, s.name as school_name, 
                    c.id as column_id, c.name as column_name
            FROM schools s
            CROSS JOIN columns c
            LEFT JOIN {$this->table} dv 
                ON dv.school_id = s.id 
                AND dv.column_id = c.id
            WHERE dv.id IS NULL"
        );
    }

    public function getDataSummary() {
        return $this->db->fetchAll(
            "SELECT c.name as column_name, 
                    COUNT(dv.id) as filled_count,
                    (SELECT COUNT(*) FROM schools) as total_schools
            FROM columns c
            LEFT JOIN {$this->table} dv ON dv.column_id = c.id
            GROUP BY c.id, c.name"
        );
    }
}