<?php
include('config.php');
include('email_security.php');
session_start();
$error = '';

// Clean old login attempts (run occasionally)
if (rand(1, 100) == 1) {
    cleanOldAttempts($pdo, 24);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
        // Log the failed attempt
        logLoginAttempt($pdo, $email, $ipAddress, $userAgent, false);
    } else {
        $stmt = $pdo->prepare("SELECT id, email, password, role, full_name FROM users WHERE BINARY email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login - log it
            logLoginAttempt($pdo, $email, $ipAddress, $userAgent, true);
            
            $_SESSION["user_id"] = $user['id'];
            $_SESSION["user_email"] = $user['email'];
            $_SESSION["user_role"] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            // Failed login - log it
            logLoginAttempt($pdo, $email, $ipAddress, $userAgent, false);
            
            // Check if this is an admin account attempt
            if ($user && $user['role'] === 'admin') {
                $failedAttempts = getRecentFailedAttempts($pdo, $email, 30);
                
                // Send security alert after 3 failed attempts
                if ($failedAttempts >= 3) {
                    $adminName = $user['full_name'] ?: 'Admin';
                    $currentTime = date('Y-m-d H:i:s');
                    
                    // Send security notification email
                    $emailSent = sendSecurityAlert($email, $adminName, $ipAddress, $userAgent, $currentTime);
                    
                    if ($emailSent) {
                        $error = "Invalid email or password. Security alert has been sent to the admin email due to multiple failed attempts.";
                    } else {
                        $error = "Invalid email or password. Multiple failed attempts detected.";
                    }
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Shakira Salon Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: url('images/salon-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.88), rgba(255, 240, 245, 0.85));
      backdrop-filter: saturate(180%) blur(10px);
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      box-shadow: 0 12px 40px rgba(255, 64, 129, 0.3);
      text-align: center;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 1.8rem;
      color: #ff4081;
      font-weight: 700;
      margin-bottom: 20px;
    }

    form {
      text-align: left;
      margin-top: 10px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #333;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 14px;
      border: 1.8px solid #f7a0bb;
      border-radius: 10px;
      font-size: 1rem;
      background-color: #fefefe;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    input:focus {
      border-color: #ff4081;
      outline: none;
      box-shadow: 0 0 0 4px rgba(255, 64, 129, 0.2);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 18px;
      font-size: 0.95rem;
      color: #666;
    }

    .checkbox-group input {
      margin-right: 8px;
      cursor: pointer;
    }

    input[type="submit"] {
      width: 100%;
      padding: 14px;
      font-size: 1.1rem;
      background-color: #ff4081;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #e0356e;
    }

    .error {
      background-color: #ffe6e9;
      color: #a94442;
      padding: 12px;
      border-left: 4px solid #f44336;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: left;
    }

    .links {
      margin-top: 20px;
      font-size: 0.95rem;
      text-align: center;
    }

    .links a {
      color: #ff4081;
      text-decoration: none;
      margin: 0 8px;
      font-weight: 600;
    }

    .links a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .login-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h1>Shakira Salon Login</h1>

    <?php if ($error): ?>
      <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required placeholder="Enter your email">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="Enter your password">

      <div class="checkbox-group">
        <input type="checkbox" id="showPassword" onclick="togglePassword()">
        <label for="showPassword">Show Password</label>
      </div>

      <input type="submit" value="Login">

      <div class="links">
        <a href="forgot_password.php">Forgot Password?</a> |
        <a href="register.php">Register</a>
      </div>
    </form>
  </div>

  <script>
    function togglePassword() {
      const pwd = document.getElementById("password");
      pwd.type = pwd.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
