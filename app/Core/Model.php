<?php
namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll() {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function findById($id) {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        );
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $params = array_combine($placeholders, array_values($data));
        return $this->db->insert($sql, $params);
    }

    public function update($id, $data) {
        $fields = array_keys($data);
        $set = array_map(function($field) {
            return $field . ' = :' . $field;
        }, $fields);

        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = :id",
            $this->table,
            implode(', ', $set)
        );

        $params = array_combine(
            array_map(function($field) { return ':' . $field; }, $fields),
            array_values($data)
        );
        $params[':id'] = $id;

        return $this->db->execute($sql, $params);
    }

    public function delete($id) {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE id = :id",
            [':id' => $id]
        );
    }

    public function count($conditions = '') {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE " . $conditions;
        }
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function exists($conditions) {
        return $this->count($conditions) > 0;
    }

    protected function buildWhereClause($conditions) {
        if (empty($conditions)) {
            return ['', []];
        }

        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $placeholder = ':' . $field;
            $where[] = $field . ' = ' . $placeholder;
            $params[$placeholder] = $value;
        }

        return [
            'WHERE ' . implode(' AND ', $where),
            $params
        ];
    }
}