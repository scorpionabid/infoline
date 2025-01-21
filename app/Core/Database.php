<?php
namespace App\Core;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';
        
        if (!$config) {
            throw new \Exception('Database configuration not found');
        }
        
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
            
            $this->connection = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch(\PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \Exception('Database connection failed. Please check your configuration.');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception("FetchOne failed: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("FetchAll failed: " . $e->getMessage());
        }
    }

    public function insert($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $this->connection->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Insert failed: " . $e->getMessage());
        }
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Execute failed: " . $e->getMessage());
        }
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollBack();
    }
}