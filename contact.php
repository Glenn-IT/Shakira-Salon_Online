<?php
// --- Database Connection ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "shakira_salon";

$conn = new mysqli($host, $user, $pass, $db);
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
  <style>
    body { background: #fdfdfd; font-family: Arial, sans-serif; margin-bottom: 0; }
    .navbar { background-color: #ff69b4 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .navbar-brand { font-weight: bold; color: #fff !important; }
    .nav-link { color: #fff !important; font-weight: 500; }
    .nav-link:hover { text-decoration: underline; }
    .contact-header {
      background: linear-gradient(135deg, #ff69b4, #ff1493);
      color: white; padding: 50px 0; text-align: center;
      border-bottom-left-radius: 50px; border-bottom-right-radius: 50px;
      margin-top: 56px;
    }
    .contact-form { background: white; padding: 30px; border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: -40px; }
    .form-control:focus { border-color: #ff69b4; box-shadow: 0 0 5px rgba(255,105,180,0.5); }
    .btn-salon { background-color: #ff1493; color: white; border-radius: 25px;
      padding: 10px 20px; border: none; }
    .btn-salon:hover { background-color: #e01383; }
    footer {
      background: #ff69b4;
      color: white;
      padding: 40px 0;
      text-align: center;
      margin-top: 50px;
    }
    footer a { color: white; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
    footer .social i {
      font-size: 20px;
      margin: 0 10px;
      color: white;
      transition: 0.3s;
    }
    footer .social i:hover {
      color: #ffe4f2;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a></li>
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
          <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="Message subject" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" rows="5" class="form-control" placeholder="Write your message..." required></textarea>
          </div>
          <button type="submit" class="btn btn-salon">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <h5>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot"></i> Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone"></i> +63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope"></i> <a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <div class="social mt-3">
      <a href="#"><i class="fa-brands fa-facebook"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
    </div>
    <hr class="my-3" style="border-color: rgba(255,255,255,0.5);">
    <p>&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
