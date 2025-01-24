<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct($options = []) {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_DATABASE') ?: 'infoline';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'z@qA00tala62';

        $default_options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ];

        $final_options = array_merge($default_options, $options);

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            error_log("Attempting database connection to: $host:$port/$database");
            
            $this->connection = new PDO($dsn, $username, $password, $final_options);
            error_log("Database connection successful");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new \Exception("Database connection failed. Please check your configuration.");
        }
    }

    public static function getInstance($options = []) {
        if (self::$instance === null) {
            self::$instance = new self($options);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function initializeTables() {
        try {
            // Set default character set and collation
            $this->connection->exec("SET NAMES utf8mb4");
            $this->connection->exec("SET CHARACTER SET utf8mb4");
            $this->connection->exec("SET character_set_connection=utf8mb4");
            
            // Start transaction
            $this->connection->beginTransaction();

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
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

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
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Create columns table
                $this->connection->exec("CREATE TABLE columns (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    type ENUM('text', 'number', 'date', 'select') NOT NULL DEFAULT 'text',
                    deadline DATETIME NULL,
                    is_active BOOLEAN NOT NULL DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

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
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Insert super admin user
                $this->connection->exec("INSERT INTO users (name, username, password, role, is_active)
                    VALUES (
                        'Super Admin',
                        'admin',
                        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                        'super_admin',
                        true
                    )");

                // Commit transaction
                $this->connection->commit();
                return true;
            } catch (PDOException $e) {
                // Rollback on error
                $this->connection->rollBack();
                error_log("Database initialization error: " . $e->getMessage());
                throw $e;
            }
        } catch (PDOException $e) {
            error_log("Fatal database error: " . $e->getMessage());
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