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
            height: 100vh;
        }
        .forgot-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #aaa;
            width: 400px;
        }
        .forgot-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .forgot-box input[type="text"],
        .forgot-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .forgot-box input[type="submit"],
        .forgot-box a.button {
            width: 48%;
            padding: 10px;
            background: #ff69b4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
        }
        .forgot-box .cancel {
            background: #aaa;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            color: #d9534f;
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
            <input type="text" name="email" placeholder="Enter your email" required>
                        <div style="text-align: center;">
            <input type="submit" name="check_user" value="Next">
            <div style="text-align: center;">
            <a href="login.php" class="button cancel">Cancel</a>
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
            <input type="password" name="new_password" required>

              <div class="checkbox-group">
            <input type="checkbox" id="showPassword" onclick="togglePassword()">
            <label for="showPassword">Show Password</label>
        </div>

            <input type="submit" name="reset_password" value="Change">
            <a href="login.php" class="button cancel">Cancel</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
