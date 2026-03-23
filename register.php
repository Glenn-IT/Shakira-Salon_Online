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
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, cp_number, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
<title>Register - Shakira Salon Appointment System</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #ffdde1, #ee9ca7);
        margin: 0;
        padding: 20px 15px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        box-sizing: border-box;
    }
    main.register-container {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 450px;
        margin: auto 0;
    }
    h1 {
        text-align: center;
        color: #d63384;
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
        color: #333;
    }
    input, select {
        width: 100%;
        padding: 12px;
        border: 2px solid #f8bbd0;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 15px;
        transition: 0.3s;
    }
    input:focus, select:focus {
        border-color: #d63384;
        outline: none;
        box-shadow: 0 0 6px rgba(214, 51, 132, 0.4);
    }
    input[type="submit"] {
        background: #d63384;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: 0.3s;
    }
    input[type="submit"]:hover {
        background: #c2185b;
    }
    .message {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
    .message.error {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    .message.success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .login-link {
        text-align: center;
        margin-top: 15px;
    }
    .login-link a {
        color: #d63384;
        font-weight: bold;
        text-decoration: none;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
    .email-status {
        font-size: 12px;
        margin-top: 5px;
        padding: 5px;
        border-radius: 4px;
    }
    .email-available {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .email-taken {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    .fullname-status {
        font-size: 12px;
        margin-top: 5px;
        padding: 5px;
        border-radius: 4px;
    }
    .fullname-available {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .fullname-taken {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    .cp-status {
        font-size: 12px;
        margin-top: 5px;
        padding: 5px;
        border-radius: 4px;
    }
    .cp-valid {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .cp-invalid {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
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
  <h1 id="registerHeading">Create Your Account</h1>

  <?php if ($error): ?>
    <div class="message error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="message success" role="alert"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <label for="fullname">Full Name</label>
    <input type="text" id="fullname" name="fullname" placeholder="John Doe" required autocomplete="name" onblur="checkFullNameAvailability()" />
    <div id="fullname-status" class="fullname-status"></div>

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email" onblur="checkEmailAvailability()" />
    <div id="email-status" class="email-status"></div>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Choose a strong password" required minlength="8" autocomplete="new-password" />

    <label for="cp">Contact Number</label>
    <input type="text" id="cp" name="cp" placeholder="e.g., 09123456789" pattern="09[0-9]{9}" required maxlength="11" minlength="11" oninput="validatePhilippineNumber(this)" />
    <div id="cp-status" class="cp-status"></div>

    <label for="security_question">Security Question</label>
    <select id="security_question" name="security_question" required>
      <option value="" disabled selected>Select a question</option>
      <option value="What was your childhood nickname?">What was your childhood nickname?</option>
      <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
      <option value="What is the name of your first pet?">What is the name of your first pet?</option>
      <option value="What was the model of your first car?">What was the model of your first car?</option>
      <option value="What is your favorite food?">What is your favorite food?</option>
    </select>

    <label for="security_answer">Security Answer</label>
    <input type="text" id="security_answer" name="security_answer" placeholder="Your answer here" required autocomplete="off" />

    <input type="submit" value="Register" />
  </form>

  <p class="login-link">Already have an account? <a href="login.php">Login here</a>.</p>
</main>
</body>
</html>
