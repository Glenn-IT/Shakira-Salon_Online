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
    } elseif (!preg_match('/^[0-9]{11}$/', $cp)) {
        $error = "Contact number must be numeric 11 digits.";
    } else {
        try {
            // Check if email already exists in the database
            $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "This email address is already in use. Please use a different email or try logging in if you already have an account.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, contact_number, role, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$fullname, $email, $password, $cp, $role, $question, $answer])) {

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
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    main.register-container {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 450px;
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
</style>
<script>
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
    <input type="text" id="fullname" name="fullname" placeholder="John Doe" required autocomplete="name" />

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email" onblur="checkEmailAvailability()" />
    <div id="email-status" class="email-status"></div>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Choose a strong password" required minlength="8" autocomplete="new-password" />

    <label for="cp">Contact Number</label>
    <input type="text" id="cp" name="cp" placeholder="11-digit contact number" pattern="\d{11}" required maxlength="11" minlength="11" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />

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
