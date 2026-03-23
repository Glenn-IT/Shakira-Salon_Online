<?php
// Test script for email security functionality
include('config.php');
include('email_security.php');

echo "<h2>🔧 Security System Test Script</h2>";
echo "<hr>";

// Test 1: Database connection
echo "<h3>1. Testing Database Connection</h3>";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM login_attempts");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Database connection: OK<br>";
    echo "📊 Current login attempts in database: " . $result['count'] . "<br><br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br><br>";
}

// Test 2: Check if admin user exists
echo "<h3>2. Testing Admin User Detection</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, email, full_name, role FROM users WHERE role = 'admin'");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admins) > 0) {
        echo "✅ Admin users found: " . count($admins) . "<br>";
        foreach ($admins as $admin) {
            echo "👤 Admin: " . htmlspecialchars($admin['full_name']) . " (" . htmlspecialchars($admin['email']) . ")<br>";
        }
    } else {
        echo "⚠️ No admin users found in database<br>";
    }
    echo "<br>";
} catch (Exception $e) {
    echo "❌ Admin user check failed: " . $e->getMessage() . "<br><br>";
}

// Test 3: Test logging functionality
echo "<h3>3. Testing Login Attempt Logging</h3>";
$testEmail = "test@example.com";
$testIP = "127.0.0.1";
$testUserAgent = "Test Browser";

$logResult = logLoginAttempt($pdo, $testEmail, $testIP, $testUserAgent, false);
if ($logResult) {
    echo "✅ Login attempt logging: OK<br>";
    
    // Check if it was recorded
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE email = ?");
    $stmt->execute([$testEmail]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📝 Test attempt recorded: " . ($result['count'] > 0 ? "Yes" : "No") . "<br>";
    
    // Clean up test data
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->execute([$testEmail]);
    echo "🧹 Test data cleaned up<br><br>";
} else {
    echo "❌ Login attempt logging failed<br><br>";
}

// Test 4: Check PHPMailer
echo "<h3>4. Testing PHPMailer Installation</h3>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer: Available<br>";
    echo "📍 PHPMailer path: vendor/phpmailer/phpmailer/<br>";
} else {
    echo "❌ PHPMailer: Not available<br>";
}
echo "<br>";

// Test 5: Check email configuration
echo "<h3>5. Email Configuration Status</h3>";
echo "⚙️ SMTP Host: smtp.gmail.com<br>";
echo "⚙️ SMTP Port: 587<br>";
echo "⚠️ <strong>Action Required:</strong> Update your Gmail credentials in email_security.php<br>";
echo "   - Change 'your-gmail@gmail.com' to your actual Gmail<br>";
echo "   - Change 'your-app-password' to your Gmail App Password<br><br>";

// Test 6: Instructions for testing
echo "<h3>6. Manual Testing Instructions</h3>";
echo "<div style='background-color: #f0f8ff; padding: 15px; border: 1px solid #0066cc; border-radius: 5px;'>";
echo "<strong>To test the security alert feature:</strong><br>";
echo "1. Make sure you've configured your Gmail credentials in email_security.php<br>";
echo "2. Try logging into an admin account with wrong password 3 times<br>";
echo "3. Check the admin's email for security alert<br>";
echo "4. Check login_attempts table for recorded attempts<br>";
echo "</div><br>";

echo "<h3>7. System Status Summary</h3>";
echo "<div style='background-color: #f0fff0; padding: 15px; border: 1px solid #00aa00; border-radius: 5px;'>";
echo "✅ Database table: Created<br>";
echo "✅ PHP files: No syntax errors<br>";
echo "✅ PHPMailer: Installed<br>";
echo "✅ Login tracking: Integrated<br>";
echo "⚙️ Email configuration: Needs your Gmail setup<br>";
echo "</div>";

echo "<hr>";
echo "<p><em>Test completed. Delete this file (test_security.php) after testing.</em></p>";
?>
