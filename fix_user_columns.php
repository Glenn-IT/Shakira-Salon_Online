<?php
require 'config.php';

echo "<h2>Database Column Check and Fix</h2>";

try {
    // Check if contact_number column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'contact_number'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "<p>Adding 'contact_number' column...</p>";
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `contact_number` VARCHAR(15) NULL AFTER `cp_number`");
        echo "<p style='color: green;'>✅ Successfully added 'contact_number' column!</p>";
        
        // Copy data from cp_number to contact_number
        $pdo->exec("UPDATE `users` SET `contact_number` = `cp_number` WHERE `cp_number` IS NOT NULL");
        echo "<p style='color: green;'>✅ Copied existing phone numbers to new column!</p>";
    } else {
        echo "<p style='color: blue;'>✅ 'contact_number' column already exists!</p>";
    }
    
    // Display current users table structure
    echo "<h3>Current Users Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><p><a href='book_appointment.php'>Go to Book Appointment</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
