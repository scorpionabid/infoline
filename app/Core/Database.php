<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connections = [];
    private $config;
    private $activeConnections = 0;

    private function __construct() {
        $this->loadConfig();
    }

    private function loadConfig() {
        $configPath = __DIR__ . '/../../config/database.php';
        if (!file_exists($configPath)) {
            throw new \Exception('Database konfiqurasiya faylı tapılmadı');
        }
        $this->config = require $configPath;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        try {
            // Əgər heç bir connection yoxdursa, yeni bir connection yarat
            if (empty($this->connections)) {
                return $this->createNewConnection();
            }

            // Mövcud connectionlardan birini istifadə et
            return $this->connections[0];
        } catch (\Exception $e) {
            throw new \Exception("Database bağlantısı yaratmaq mümkün olmadı: " . $e->getMessage());
        }
    }

    private function createNewConnection() {
        try {
            $config = $this->config['default'];
            
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $options
            );

            $this->connections[] = $connection;
            return $connection;
        } catch (\PDOException $e) {
            throw new \Exception(
                'Database serverə qoşulmaq mümkün olmadı: ' . $e->getMessage()
            );
        }
    }

    public function prepare($sql) {
        try {
            $connection = $this->getConnection();
            return $connection->prepare($sql);
        } catch (\Exception $e) {
            error_log("Database->prepare Exception: " . $e->getMessage());
            throw new \Exception("SQL hazırlanarkən xəta baş verdi: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $connection = $this->getConnection();
            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\Exception $e) {
            error_log("Database->query Exception: " . $e->getMessage());
            throw new \Exception("Sorğu icra edilərkən xəta baş verdi: " . $e->getMessage());
        }
    }

    public function execute($sql, $params = []) {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            error_log("Database execute error: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchAll($sql, $params = []) {
        try {
            $statement = $this->query($sql, $params);
            if ($statement instanceof \PDOStatement) {
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (\PDOException $e) {
            error_log("Database fetchAll error: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchOne($sql, $params = []) {
        try {
            $statement = $this->query($sql, $params);
            if ($statement instanceof \PDOStatement) {
                return $statement->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("Database fetchOne error: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchColumn($sql, $params = []) {
        try {
            $statement = $this->query($sql, $params);
            if ($statement instanceof \PDOStatement) {
                return $statement->fetchColumn();
            }
            return null;
        } catch (\PDOException $e) {
            error_log("Database fetchColumn error: " . $e->getMessage());
            throw $e;
        }
    }

    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }

    public function beginTransaction() {
        $connection = $this->getConnection();
        if (!$connection->inTransaction()) {
            return $connection->beginTransaction();
        }
        return true;
    }

    public function commit() {
        $connection = $this->getConnection();
        if ($connection->inTransaction()) {
            return $connection->commit();
        }
        return true;
    }

    public function rollBack() {
        $connection = $this->getConnection();
        if ($connection->inTransaction()) {
            return $connection->rollBack();
        }
        return true;
    }

    public function __destruct() {
        // Bütün connection-ları bağla
        foreach ($this->connections as $connection) {
            $connection = null;
        }
        $this->connections = [];
        $this->activeConnections = 0;
    }

    // Clone-lamanı qadağan et
    private function __clone() {}
    private function __wakeup() {}
}