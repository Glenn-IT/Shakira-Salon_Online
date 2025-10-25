<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendSecurityAlert($adminEmail, $adminName, $ipAddress, $userAgent, $attemptTime) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shakirasalon0@gmail.com'; // SMTP username (change this to your Gmail)
        $mail->Password   = 'dyrlmtfjwkasemkm';    // SMTP password (use App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('shakirasalon0@gmail.com', 'Shakira Salon Security');
        $mail->addAddress($adminEmail, $adminName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Security Alert: Multiple Failed Login Attempts';
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #ff4081; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
                .alert { background-color: #ffe6e9; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }
                .details { background-color: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🚨 Security Alert - Shakira Salon</h2>
                </div>
                <div class='content'>
                    <div class='alert'>
                        <strong>SECURITY NOTICE:</strong> Multiple failed login attempts detected on your admin account.
                    </div>
                    
                    <p>Dear " . htmlspecialchars($adminName) . ",</p>
                    
                    <p>We detected <strong>3 consecutive failed login attempts</strong> on your admin account. Here are the details:</p>
                    
                    <div class='details'>
                        <strong>Attempt Details:</strong><br>
                        <strong>Time:</strong> " . date('F j, Y \a\t g:i A', strtotime($attemptTime)) . "<br>
                        <strong>IP Address:</strong> " . htmlspecialchars($ipAddress) . "<br>
                        <strong>Browser/Device:</strong> " . htmlspecialchars($userAgent) . "<br>
                        <strong>Email Attempted:</strong> " . htmlspecialchars($adminEmail) . "
                    </div>
                    
                    <div class='alert'>
                        <strong>What should you do?</strong><br>
                        • If this was not you, consider changing your password immediately<br>
                        • Review your account security settings<br>
                        • Monitor for any suspicious activity<br>
                        • Consider enabling additional security measures
                    </div>
                    
                    <p>If you recognize this activity, you can safely ignore this email. However, if you did not attempt to log in, please secure your account immediately.</p>
                    
                    <p>Best regards,<br>
                    Shakira Salon Security Team</p>
                </div>
                
                <div class='footer'>
                    This is an automated security notification from Shakira Salon Management System.<br>
                    Please do not reply to this email.
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $body;
        
        // Alternative plain text version
        $mail->AltBody = "Security Alert - Shakira Salon\n\n" .
                        "Multiple failed login attempts detected on admin account: $adminEmail\n" .
                        "Time: " . date('F j, Y \a\t g:i A', strtotime($attemptTime)) . "\n" .
                        "IP Address: $ipAddress\n" .
                        "Browser/Device: $userAgent\n\n" .
                        "If this was not you, please secure your account immediately.\n\n" .
                        "Shakira Salon Security Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function logLoginAttempt($pdo, $email, $ipAddress, $userAgent, $success = false) {
    try {
        $stmt = $pdo->prepare("INSERT INTO login_attempts (email, ip_address, user_agent, success) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $ipAddress, $userAgent, $success ? 1 : 0]);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to log login attempt: " . $e->getMessage());
        return false;
    }
}

function getRecentFailedAttempts($pdo, $email, $minutes = 30) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempt_count 
            FROM login_attempts 
            WHERE email = ? 
            AND success = 0 
            AND attempt_time >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$email, $minutes]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['attempt_count'];
    } catch (PDOException $e) {
        error_log("Failed to get recent failed attempts: " . $e->getMessage());
        return 0;
    }
}

function cleanOldAttempts($pdo, $hours = 24) {
    try {
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL ? HOUR)");
        $stmt->execute([$hours]);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to clean old attempts: " . $e->getMessage());
        return false;
    }
}
?>
