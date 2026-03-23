<?php
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $cp = trim($_POST["cp"]);
    $role = "customer";
    $question = $_POST["security_question"];
    $answer = hash('sha256', strtolower(trim($_POST["security_answer"])));

    // Validation
    if (empty($fullname)) {
        $error = "Full Name is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^09[0-9]{9}$/', $cp)) {
        $error = "Contact number must be a valid Philippine number (starts with 09 followed by 9 digits).";
    } else {
        try {
            // Check if email already exists in the database
            $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "This email address is already in use. Please use a different email or try logging in if you already have an account.";
            } else {
                // Check if full name already exists in the database
                $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(full_name) = LOWER(?)");
                $stmt->execute([$fullname]);
                if ($stmt->fetch()) {
                    $error = "This full name is already registered. Please use a different name or contact support if this is your name.";
                } else {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, contact_number, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $email, $password, $cp, $role, $question, $answer]);
                if ($stmt->rowCount() > 0) {

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'ityourboiaki@gmail.com';
                    $mail->Password   = 'fojt zvoj imdr xwnw';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('ityourboiaki@gmail.com', 'Shakira Salon');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Shakira Salon Appointment System!';
                    $mail->Body    = "
                        <h2>Welcome to Shakira Salon, " . htmlspecialchars($fullname) . "!</h2>
                        <p>Thank you for registering with Shakira Salon Appointment System.</p>
                        <p>You can now <a href='https://yourdomain.com/login.php'>login here</a>.</p>
                        <p>If you did not register, please ignore this email.</p>
                    ";
                    $mail->AltBody = "Welcome to Shakira Salon, $fullname!\n\nYou can now login here: https://yourdomain.com/login.php";

                    $mail->send();

                    $success = "Registration successful! A confirmation email has been sent. You can now <a href='login.php'>login</a>.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register - Shakira Salon</title>
<link rel="stylesheet" href="assets/css/shared.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #fce4ec, #f8bbd0, #f48fb1);
        background-attachment: fixed;
        margin: 0;
        padding: 20px 15px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }
    main.register-container {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 36px 32px;
        border-radius: var(--radius-xl);
        box-shadow: 0 16px 50px rgba(255, 64, 129, 0.2), 0 0 0 1px rgba(255,255,255,0.4);
        width: 100%;
        max-width: 480px;
        margin: auto 0;
        animation: fadeInUp 0.6s ease forwards;
    }
    .register-logo {
        width: 56px;
        height: 56px;
        background: var(--gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        box-shadow: var(--shadow-pink);
    }
    .register-logo i {
        font-size: 1.3rem;
        color: #fff;
    }
    h1 {
        text-align: center;
        color: var(--primary);
        margin: 0 0 4px;
        font-size: 1.5rem;
    }
    .register-subtitle {
        text-align: center;
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 26px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-dark);
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e8e8e8;
        border-radius: var(--radius-sm);
        font-size: 0.95rem;
        font-family: var(--font-family);
        transition: var(--transition);
        background: #fff;
    }
    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(255, 64, 129, 0.12);
    }
    .form-group select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23888' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
        padding-right: 40px;
    }
    .message {
        padding: 14px 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 18px;
        font-weight: 500;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .message.error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }
    .message.error::before {
        content: '\f06a';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        flex-shrink: 0;
    }
    .message.success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
    .message.success::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        flex-shrink: 0;
    }
    .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    .login-link a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }
    .login-link a:hover {
        color: var(--primary-darker);
        text-decoration: underline;
    }
    .email-status, .fullname-status, .cp-status {
        font-size: 0.8rem;
        margin-top: 6px;
        padding: 6px 10px;
        border-radius: 6px;
        font-weight: 500;
    }
    .email-available, .fullname-available, .cp-valid {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
    .email-taken, .fullname-taken, .cp-invalid {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }
    .fullname-taken {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }
    .cp-status {
        font-size: 0.8rem;
        margin-top: 6px;
        padding: 6px 10px;
        border-radius: 6px;
        font-weight: 500;
    }
    .cp-valid {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
    .cp-invalid {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }
</style>
<script>
function validatePhilippineNumber(input) {
    const statusDiv = document.getElementById('cp-status');
    const value = input.value.trim();
    
    // Clear previous status
    statusDiv.innerHTML = '';
    statusDiv.className = 'cp-status';
    
    // Allow only numbers and ensure 09 format
    let cleanValue = value.replace(/[^0-9]/g, '');
    
    // Format the input based on what user types
    if (cleanValue.startsWith('09')) {
        // Keep 09 format
        if (cleanValue.length > 11) {
            cleanValue = cleanValue.substring(0, 11);
        }
    } else if (cleanValue.length > 0 && cleanValue.startsWith('9')) {
        // If starts with 9, add 0
        cleanValue = '0' + cleanValue;
        if (cleanValue.length > 11) {
            cleanValue = cleanValue.substring(0, 11);
        }
    } else if (cleanValue.length > 0 && !cleanValue.startsWith('0')) {
        // If doesn't start with 0, add 09
        cleanValue = '09' + cleanValue;
        if (cleanValue.length > 11) {
            cleanValue = cleanValue.substring(0, 11);
        }
    }
    
    input.value = cleanValue;
    
    // Validate the format
    const philippinePattern = /^09[0-9]{9}$/;
    
    if (cleanValue.length >= 11) {
        if (philippinePattern.test(cleanValue)) {
            statusDiv.innerHTML = '✓ Valid Philippine number';
            statusDiv.className = 'cp-status cp-valid';
        } else {
            statusDiv.innerHTML = '✗ Invalid format. Use 09XXXXXXXXX';
            statusDiv.className = 'cp-status cp-invalid';
        }
    }
}

function checkFullNameAvailability() {
    const fullnameInput = document.getElementById('fullname');
    const statusDiv = document.getElementById('fullname-status');
    const fullname = fullnameInput.value.trim();
    
    // Clear previous status
    statusDiv.innerHTML = '';
    statusDiv.className = 'fullname-status';
    
    // Only check if fullname is not empty and has at least 2 characters
    if (fullname && fullname.length >= 2) {
        fetch('check_fullname.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'fullname=' + encodeURIComponent(fullname)
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                statusDiv.innerHTML = '✗ This name is already registered';
                statusDiv.className = 'fullname-status fullname-taken';
            } else {
                statusDiv.innerHTML = '✓ Name is available';
                statusDiv.className = 'fullname-status fullname-available';
            }
        })
        .catch(error => {
            console.error('Error checking fullname:', error);
        });
    }
}

function checkEmailAvailability() {
    const emailInput = document.getElementById('email');
    const statusDiv = document.getElementById('email-status');
    const email = emailInput.value.trim();
    
    // Clear previous status
    statusDiv.innerHTML = '';
    statusDiv.className = 'email-status';
    
    // Only check if email format is valid
    if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        fetch('check_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                statusDiv.innerHTML = '✗ This email is already in use';
                statusDiv.className = 'email-status email-taken';
            } else {
                statusDiv.innerHTML = '✓ Email is available';
                statusDiv.className = 'email-status email-available';
            }
        })
        .catch(error => {
            console.error('Error checking email:', error);
        });
    }
}
</script>
</head>
<body>
<main class="register-container" role="main" aria-labelledby="registerHeading">
  <div class="register-logo">
    <i class="fa-solid fa-user-plus"></i>
  </div>
  <h1 id="registerHeading">Create Your Account</h1>
  <p class="register-subtitle">Join Shakira Salon and book your appointments</p>

  <?php if ($error): ?>
    <div class="message error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="message success" role="alert"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="form-group">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" placeholder="e.g. Juan Dela Cruz" required autocomplete="name" onblur="checkFullNameAvailability()" />
      <div id="fullname-status" class="fullname-status"></div>
    </div>

    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email" onblur="checkEmailAvailability()" />
      <div id="email-status" class="email-status"></div>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Choose a strong password" required minlength="8" autocomplete="new-password" />
    </div>

    <div class="form-group">
      <label for="cp">Contact Number</label>
      <input type="text" id="cp" name="cp" placeholder="e.g. 09123456789" pattern="09[0-9]{9}" required maxlength="11" minlength="11" oninput="validatePhilippineNumber(this)" />
      <div id="cp-status" class="cp-status"></div>
    </div>

    <div class="form-group">
      <label for="security_question">Security Question</label>
      <select id="security_question" name="security_question" required>
        <option value="" disabled selected>Select a question</option>
        <option value="What was your childhood nickname?">What was your childhood nickname?</option>
        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
        <option value="What is the name of your first pet?">What is the name of your first pet?</option>
        <option value="What was the model of your first car?">What was the model of your first car?</option>
        <option value="What is your favorite food?">What is your favorite food?</option>
      </select>
    </div>

    <div class="form-group">
      <label for="security_answer">Security Answer</label>
      <input type="text" id="security_answer" name="security_answer" placeholder="Your answer here" required autocomplete="off" />
    </div>

    <button type="submit" class="btn-submit">
      <i class="fa-solid fa-user-plus"></i> Create Account
    </button>
  </form>

  <p class="login-link">Already have an account? <a href="login.php">Sign in here</a></p>
</main>
</body>
</html>
