<?php
require 'config.php';

echo "<h2>Database Column Check and Fix</h2>";

try {
    // Ensure contact_number column exists (canonical phone column)
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'contact_number'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "<p>Adding 'contact_number' column...</p>";
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `contact_number` VARCHAR(15) NULL");
        echo "<p style='color: green;'>✅ Successfully added 'contact_number' column!</p>";
    } else {
        echo "<p style='color: blue;'>✅ 'contact_number' column already exists!</p>";
    }

    // Migrate any leftover data from legacy cp_number column
    $stmtCp = $pdo->query("SHOW COLUMNS FROM users LIKE 'cp_number'");
    if ($stmtCp->fetch()) {
        $pdo->exec("UPDATE `users` SET `contact_number` = `cp_number` WHERE `cp_number` IS NOT NULL AND (`contact_number` IS NULL OR `contact_number` = '')");
        echo "<p style='color: green;'>✅ Migrated any remaining data from legacy 'cp_number' to 'contact_number'.</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Legacy 'cp_number' column not found — nothing to migrate.</p>";
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
