<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first. <a href='login.php'>Login here</a>");
}

echo "<h2>Debug: Current User Data</h2>";
echo "<p>Session User ID: " . $_SESSION['user_id'] . "</p>";

try {
    // Fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h3>All User Data:</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr><th style='background: #ff69b4; color: white;'>Column Name</th><th style='background: #ff69b4; color: white;'>Value</th></tr>";
        
        foreach ($user as $key => $value) {
            $displayValue = empty($value) ? "<em style='color: #999;'>(empty)</em>" : htmlspecialchars($value);
            if ($key === 'password') {
                $displayValue = "***** (hidden)";
            }
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
            echo "<td>" . $displayValue . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show what will be auto-populated
        echo "<br><h3>What Will Be Auto-Populated in Booking Form:</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr><th style='background: #4CAF50; color: white;'>Field</th><th style='background: #4CAF50; color: white;'>Value</th></tr>";
        
        echo "<tr><td><strong>Name</strong></td><td>" . 
             (empty($user['full_name']) ? "<em style='color: red;'>(NOT SET - Please update your profile)</em>" : htmlspecialchars($user['full_name'])) . 
             "</td></tr>";
        
        echo "<tr><td><strong>Email</strong></td><td>" . 
             (empty($user['email']) ? "<em style='color: red;'>(NOT SET)</em>" : htmlspecialchars($user['email'])) . 
             "</td></tr>";
        
        $phone = $user['cp_number'] ?? $user['contact_number'] ?? $user['phone_number'] ?? $user['phone'] ?? '';
        echo "<tr><td><strong>Phone</strong></td><td>" . 
             (empty($phone) ? "<em style='color: red;'>(NOT SET - Please update your profile)</em>" : htmlspecialchars($phone)) . 
             "</td></tr>";
        
        echo "</table>";
        
        echo "<br><p><strong>Note:</strong> If any field shows (NOT SET), you'll need to manually enter it when booking.</p>";
        
    } else {
        echo "<p style='color: red;'>User not found in database!</p>";
    }
    
    echo "<br><br>";
    echo "<a href='book_appointment.php' style='background: #ff69b4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Book Appointment</a>";
    echo " | ";
    echo "<a href='dashboard.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Dashboard</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
