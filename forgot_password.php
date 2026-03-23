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
<html>
<head>
    <title>Forgot Password - Shakira Salon</title>
    <style>
    body {
            font-family: Arial;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .forgot-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #aaa;
            width: 100%;
            max-width: 420px;
        }
        .forgot-box h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #ff69b4;
        }
        .forgot-box label {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
            font-weight: bold;
            color: #333;
        }
        .forgot-box input[type="text"],
        .forgot-box input[type="email"],
        .forgot-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 4px 0 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        .forgot-box input[type="submit"] {
            width: 48%;
            padding: 10px;
            background: #ff69b4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 12px;
            transition: background 0.3s;
        }
        .forgot-box input[type="submit"]:hover {
            background: #e0358c;
        }
        .forgot-box a.button {
            width: 48%;
            padding: 10px;
            background: #aaa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            margin-top: 12px;
            font-size: 1rem;
            font-weight: bold;
            box-sizing: border-box;
        }
        .forgot-box a.button:hover {
            background: #888;
        }
        .btn-row {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            font-size: 0.95rem;
            color: #555;
        }
        .checkbox-group input { width: auto; margin: 0; }
        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #d9534f;
            padding: 10px;
            background: #fdf3f3;
            border-radius: 6px;
        }
        .message a {
            color: green;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="forgot-box">
    <h2>FORGOT PASSWORD</h2>
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!$show_form): ?>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <div class="btn-row">
              <input type="submit" name="check_user" value="Next">
              <a href="login.php" class="button">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="security_question" value="<?= htmlspecialchars($security_question) ?>">

            <label>Security Question:</label>
            <input type="text" value="<?= htmlspecialchars($security_question) ?>" readonly>

            <label>Security Answer:</label>
            <input type="text" name="security_answer" required>

            <label>New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <div class="checkbox-group">
              <input type="checkbox" id="showPassword" onclick="togglePassword()">
              <label for="showPassword">Show Password</label>
            </div>

            <div class="btn-row">
              <input type="submit" name="reset_password" value="Change">
              <a href="login.php" class="button">Cancel</a>
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
