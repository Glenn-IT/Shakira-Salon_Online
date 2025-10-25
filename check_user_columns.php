<?php
require 'config.php';

// Check users table structure
$stmt = $pdo->query("DESCRIBE users");
echo "<h2>Users Table Structure:</h2>";
echo "<pre>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

// Check sample user data
session_start();
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Current Logged In User Data:</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
}
?>
