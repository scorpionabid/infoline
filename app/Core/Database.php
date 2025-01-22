<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_DATABASE') ?: 'infoline';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'z@qA00tala62';

        try {
            $this->connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$database",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
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

    public function initializeTables() {
        try {
            // Drop existing tables
            $this->connection->exec("DROP TABLE IF EXISTS data");
            $this->connection->exec("DROP TABLE IF EXISTS users");
            $this->connection->exec("DROP TABLE IF EXISTS schools");
            $this->connection->exec("DROP TABLE IF EXISTS columns");

            // Create schools table
            $this->connection->exec("CREATE TABLE schools (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");

            // Create users table
            $this->connection->exec("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NULL,
                name VARCHAR(255) NOT NULL,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('super_admin', 'school_admin') NOT NULL DEFAULT 'school_admin',
                is_active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL
            ) ENGINE=InnoDB");

            // Create columns table
            $this->connection->exec("CREATE TABLE columns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                type ENUM('text', 'number', 'date', 'select') NOT NULL DEFAULT 'text',
                deadline DATETIME NULL,
                is_active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");

            // Create data table
            $this->connection->exec("CREATE TABLE data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                column_id INT NOT NULL,
                value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                FOREIGN KEY (column_id) REFERENCES columns(id) ON DELETE CASCADE
            ) ENGINE=InnoDB");

            // Insert super admin user
            $this->connection->exec("INSERT INTO users (name, username, password, role, is_active)
                VALUES (
                    'Super Admin',
                    'admin',
                    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                    'super_admin',
                    true
                )");

            return true;
        } catch (PDOException $e) {
            error_log("Database initialization error: " . $e->getMessage());
            return false;
        }
    }

    public function checkTableStructure($table) {
        try {
            $stmt = $this->connection->prepare("DESCRIBE $table");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Table structure check error: " . $e->getMessage());
            return false;
        }
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}