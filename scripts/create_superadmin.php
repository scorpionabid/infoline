<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$db = App\Core\Database::getInstance()->getConnection();

$username = 'superadmin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'admin@infoline.edu.az';

$stmt = $db->prepare("INSERT INTO users (username, password, email, role, is_active) VALUES (?, ?, ?, 'superadmin', 1)");
$stmt->execute([$username, $password, $email]);

echo "Superadmin created successfully!\n";
echo "Username: superadmin\n";
echo "Password: admin123\n";