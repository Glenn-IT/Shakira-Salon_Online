<?php include('config.php'); ?>
<?php
$message = '';
$show_form = false;
$security_question = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["check_user"])) {
        $email = $_POST["email"];

        // Fetch security question
        $stmt = $pdo->prepare("SELECT security_question FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            $security_question = $row['security_question'];
            $show_form = true;
        } else {
            $message = "Email not found.";
        }

    } elseif (isset($_POST["reset_password"])) {
        $email = $_POST["email"];
        $user_answer = strtolower(trim($_POST["security_answer"]));
        $answer_hash = hash("sha256", $user_answer);
        $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);

        // Verify security answer
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND security_answer = ?");
        $stmt->execute([$email, $answer_hash]);
        $user = $stmt->fetch();

        if ($user) {
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->execute([$new_password, $email]);
            $message = "✅ Password changed successfully. <a href='login.php'>Login here</a>";
            $show_form = false;
        } else {
            $message = "❌ Incorrect security answer.";
            $show_form = true;
            $security_question = $_POST['security_question'] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Shakira Salon</title>
    <link rel="stylesheet" href="assets/css/shared.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .forgot-box {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px 32px;
            border-radius: var(--radius-xl);
            box-shadow: 0 16px 50px rgba(255, 64, 129, 0.2), 0 0 0 1px rgba(255,255,255,0.4);
            width: 100%;
            max-width: 440px;
            animation: fadeInUp 0.6s ease forwards;
        }
        .forgot-logo {
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
        .forgot-logo i {
            font-size: 1.3rem;
            color: #fff;
        }
        .forgot-box h2 {
            margin: 0 0 6px;
            text-align: center;
            color: var(--primary);
            font-size: 1.5rem;
        }
        .forgot-subtitle {
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
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 6px;
            color: var(--text-dark);
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e8e8e8;
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            font-family: var(--font-family);
            transition: var(--transition);
            background: #fff;
        }
        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 64, 129, 0.12);
        }
        .form-group input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
            color: var(--text-muted);
        }
        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-row .btn-submit {
            flex: 1;
        }
        .btn-row .btn-cancel {
            flex: 1;
            padding: 14px;
            background: #f0f0f0;
            color: var(--text-dark);
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            box-shadow: none;
        }
        .btn-row .btn-cancel:hover {
            background: #e0e0e0;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0 0;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .checkbox-group label {
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }
        .message {
            text-align: center;
            margin-bottom: 18px;
            padding: 14px 16px;
            background: #ffebee;
            color: #c62828;
            border-radius: var(--radius-sm);
            border: 1px solid #ef9a9a;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .message a {
            color: #2e7d32;
            text-decoration: underline;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="forgot-box">
    <div class="forgot-logo">
        <i class="fa-solid fa-key"></i>
    </div>
    <h2>Reset Password</h2>
    <p class="forgot-subtitle">Enter your email to recover your account</p>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!$show_form): ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            <div class="btn-row">
                <button type="submit" name="check_user" class="btn-submit">
                    <i class="fa-solid fa-arrow-right"></i> Continue
                </button>
                <a href="login.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="security_question" value="<?= htmlspecialchars($security_question) ?>">

            <div class="form-group">
                <label>Security Question</label>
                <input type="text" value="<?= htmlspecialchars($security_question) ?>" readonly>
            </div>

            <div class="form-group">
                <label for="security_answer">Security Answer</label>
                <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                <div class="checkbox-group">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">
                    <label for="showPassword">Show Password</label>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" name="reset_password" class="btn-submit">
                    <i class="fa-solid fa-check"></i> Reset Password
                </button>
                <a href="login.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>
<script>
  function togglePassword() {
    const pwd = document.getElementById("new_password");
    if (pwd) pwd.type = pwd.type === "password" ? "text" : "password";
  }
</script>
</body>
</html>
