<?php
$host = 'localhost';
$db   = 'shakira_salon';
$user = 'root';
$pass = ''; // change this if you set a password in XAMPP/WAMP
$charset = 'utf8mb4';

// DSN (Data Source Name) for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Show detailed DB errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Auto-fix: ensure contact_number column exists in users table
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `contact_number` varchar(15) DEFAULT NULL");

    // Migrate any data from legacy cp_number column into contact_number
    try {
        $pdo->exec("UPDATE `users` SET `contact_number` = `cp_number` WHERE `cp_number` IS NOT NULL AND (`contact_number` IS NULL OR `contact_number` = '')");
    } catch (\PDOException $ignore) {
        // cp_number column may not exist — that's fine
    }

    // Auto-fix: ensure status column exists in users table (active/deactivated)
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `status` ENUM('active','deactivated') NOT NULL DEFAULT 'active'");

} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
