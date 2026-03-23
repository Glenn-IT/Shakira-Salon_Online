<?php
// --- Database Connection ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "shakira_salon";
<!-- Header -->
<div class="page-header">
  <h1><i class="fa-solid fa-envelope me-2"></i>Contact Shakira Salon</h1>
  <p>We'd love to hear from you! Fill out the form below.</p>
</div>n = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $conn->real_escape_string($_POST['name']);
    $email   = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at) 
                VALUES ('$name', '$email', '$subject', '$message', NOW())";

        if ($conn->query($sql) === TRUE) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ityourboiaki@gmail.com'; 
                $mail->Password = 'fojt zvoj imdr xwnw';    
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('nicoleacojedo03@gmail.com', 'Shakira Salon Website');
                $mail->addAddress('nicoleacojedo03@gmail.com', 'Shakira Salon Admin');

                $mail->isHTML(true);
                $mail->Subject = "📩 New Contact Form Message: $subject";
                $mail->Body    = "
                    <h2>New Message from Contact Form</h2>
                    <p><b>Name:</b> $name</p>
                    <p><b>Email:</b> $email</p>
                    <p><b>Subject:</b> $subject</p>
                    <p><b>Message:</b><br>$message</p>
                    <hr>
                    <small>This message was sent from the Shakira Salon website.</small>
                ";

                $mail->send();
                $success = "✅ Your message has been sent successfully!";
            } catch (Exception $e) {
                $error = "❌ Message saved, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "❌ Something went wrong. Please try again.";
        }
    } else {
        $error = "⚠️ All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Us - Shakira Salon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    body { padding-top: 0; }
    .contact-form {
      background:var(--bg-white);padding:32px;border-radius:var(--radius-lg);
      box-shadow:var(--shadow-md);margin-top:-40px;position:relative;z-index:2;
    }
    .contact-form .form-label { font-weight:600;font-size:0.9rem;color:var(--text-dark); }
    .contact-form .form-control {
      padding:12px 16px;border-radius:var(--radius-sm);border:2px solid #e8e8e8;
      font-size:0.95rem;font-family:var(--font-family);transition:var(--transition);
    }
    .contact-form .form-control:focus {
      border-color:var(--primary);box-shadow:0 0 0 4px rgba(255,64,129,0.12);
    }
    .contact-form textarea { resize:vertical;min-height:120px; }
    @media (max-width:576px) { .contact-form { padding:22px 18px; } }
  </style>
</head>
<body>

<nav class="salon-navbar navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Header -->
<div class="contact-header">
  <h1>Contact Shakira Salon</h1>
  <p>We’d love to hear from you! Fill out the form below.</p>
</div>

<!-- Contact Form -->
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="contact-form">
        <?php if ($success): ?>
          <div class="salon-alert-success"><i class="fa-solid fa-circle-check me-1"></i> <?= $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="salon-alert-error"><i class="fa-solid fa-circle-exclamation me-1"></i> <?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-user me-1"></i> Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-envelope me-1"></i> Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-tag me-1"></i> Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Message subject" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-message me-1"></i> Message</label>
            <textarea name="message" rows="5" class="form-control" placeholder="Write your message..." required></textarea>
          </div>
          <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane me-1"></i> Send Message</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="salon-footer">
  <div class="container">
    <h5><i class="fa-solid fa-scissors me-2"></i>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot me-1"></i> Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone me-1"></i> +63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope me-1"></i> <a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <hr>
    <p>&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
