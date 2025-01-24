<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\Core\Database;

class MigrationRunner {
    private $db;
    private $migrationsPath;

    public function __construct() {
        // Enable buffered queries
        $options = [
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ];
        $this->db = Database::getInstance($options)->getConnection();
        $this->migrationsPath = __DIR__ . '/../database/migrations';
    }

    public function run() {
        // Get the last batch number
        $lastBatch = $this->getLastBatchNumber();
        $newBatch = $lastBatch + 1;

        // Get all migration files
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files); // Sort by filename

        // Get executed migrations
        $executed = $this->getExecutedMigrations();

        foreach ($files as $file) {
            $filename = basename($file);
            
            // Skip if already executed
            if (in_array($filename, $executed)) {
                echo "Skipping {$filename} - already executed\n";
                continue;
            }

            try {
                // Read and execute the SQL file
                $sql = file_get_contents($file);
                
                // Split SQL into separate statements
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $this->db->exec($statement);
                    }
                }

                // Record the migration
                $this->recordMigration($filename, $newBatch);
                
                echo "Executed {$filename} successfully\n";
            } catch (\PDOException $e) {
                echo "Error executing {$filename}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        echo "\nMigration completed successfully!\n";
    }

    private function getLastBatchNumber() {
        try {
            $stmt = $this->db->query("SELECT MAX(batch) FROM migrations");
            return (int) $stmt->fetchColumn() ?: 0;
        } catch (\PDOException $e) {
            // migrations table might not exist yet
            return 0;
        }
    }

    private function getExecutedMigrations() {
        try {
            $stmt = $this->db->query("SELECT migration FROM migrations");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            // migrations table might not exist yet
            return [];
        }
    }

    private function recordMigration($filename, $batch) {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$filename, $batch]);
    }
}

// Run migrations
echo "Starting migration...\n";
$runner = new MigrationRunner();
$runner->run();