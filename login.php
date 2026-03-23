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
  <title>Login - Shakira Salon</title>
  <link rel="stylesheet" href="assets/css/shared.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background: url('images/salon-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: saturate(180%) blur(16px);
      -webkit-backdrop-filter: saturate(180%) blur(16px);
      padding: 44px 36px;
      width: 100%;
      max-width: 440px;
      border-radius: var(--radius-xl);
      box-shadow: 0 16px 50px rgba(255, 64, 129, 0.25), 0 0 0 1px rgba(255,255,255,0.3);
      text-align: center;
      animation: fadeInUp 0.6s ease forwards;
    }

    .login-logo {
      width: 60px;
      height: 60px;
      background: var(--gradient);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      box-shadow: var(--shadow-pink);
    }

    .login-logo i {
      font-size: 1.5rem;
      color: #fff;
    }

    h1 {
      font-size: 1.6rem;
      color: var(--primary);
      font-weight: 700;
      margin: 0 0 6px;
    }

    .login-subtitle {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin-bottom: 28px;
    }

    form {
      text-align: left;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text-dark);
    }

    .input-icon-wrap {
      position: relative;
    }

    .input-icon-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #bbb;
      font-size: 0.9rem;
      transition: var(--transition);
    }

    .input-icon-wrap input {
      width: 100%;
      padding: 13px 14px 13px 42px;
      border: 2px solid #e8e8e8;
      border-radius: var(--radius-sm);
      font-size: 0.95rem;
      font-family: var(--font-family);
      background: #fff;
      transition: var(--transition);
    }

    .input-icon-wrap input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 4px rgba(255, 64, 129, 0.12);
    }

    .input-icon-wrap input:focus + i,
    .input-icon-wrap:focus-within i {
      color: var(--primary);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 24px;
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-right: 8px;
      cursor: pointer;
      accent-color: var(--primary);
    }

    .checkbox-group label {
      cursor: pointer;
      font-weight: 500;
      margin: 0;
    }

    .error {
      background: #ffebee;
      color: #c62828;
      padding: 14px 16px;
      border-left: 4px solid #ef5350;
      border-radius: var(--radius-sm);
      margin-bottom: 22px;
      font-weight: 500;
      text-align: left;
      font-size: 0.9rem;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .error::before {
      content: '\f06a';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .links {
      margin-top: 24px;
      font-size: 0.9rem;
      text-align: center;
      color: var(--text-muted);
    }

    .links a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      padding: 4px 2px;
    }

    .links a:hover {
      color: var(--primary-darker);
      text-decoration: underline;
    }

    .links .divider {
      margin: 0 10px;
      color: #ddd;
    }

    @media (max-width: 500px) {
      .login-container {
        padding: 30px 22px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-logo">
      <i class="fa-solid fa-scissors"></i>
    </div>
    <h1>Welcome Back</h1>
    <p class="login-subtitle">Sign in to your Shakira Salon account</p>

    <?php if ($error): ?>
      <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-icon-wrap">
          <input type="email" id="email" name="email" required placeholder="you@example.com">
          <i class="fa-solid fa-envelope"></i>
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-icon-wrap">
          <input type="password" id="password" name="password" required placeholder="Enter your password">
          <i class="fa-solid fa-lock"></i>
        </div>
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="showPassword" onclick="togglePassword()">
        <label for="showPassword">Show Password</label>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-right-to-bracket"></i> Sign In
      </button>

      <div class="links">
        <a href="forgot_password.php">Forgot Password?</a>
        <span class="divider">|</span>
        <a href="register.php">Create Account</a>
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
