<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'customer') {
    header("Location: login.php");
    exit;
}

// --- Database Connection ---
$mysqli = new mysqli("localhost", "root", "", "shakira_salon");
if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shakira Salon | Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    body { background: #fdfdfd; font-family: Arial, sans-serif; padding-top: 0; }
    .navbar { background-color: #ff69b4 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .navbar-brand { font-weight: bold; color: #fff !important; }
    .nav-link { color: #fff !important; font-weight: 500; }
    .nav-link:hover, .nav-link.active { text-decoration: underline; }
    .navbar-toggler { border-color: rgba(255,255,255,0.5); }
    .navbar-toggler-icon { filter: invert(1); }
    .page-header {
      background: linear-gradient(135deg, #ff69b4, #ff1493);
      color: white; padding: 50px 0; text-align: center;
      border-bottom-left-radius: 50px; border-bottom-right-radius: 50px;
      margin-top: 56px;
    }
    .page-header h1 { margin: 0; font-size: 2rem; }
    .page-header p { margin: 8px 0 0; opacity: 0.9; }
    .content-card {
      background: white; padding: 30px; border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-top: -40px;
    }
    .quick-links { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-top: 20px; }
    .quick-links a {
      background: linear-gradient(135deg, #ff69b4, #ff1493);
      color: white; padding: 14px 22px; border-radius: 12px;
      text-decoration: none; font-weight: 600; font-size: 0.95rem;
      transition: transform 0.2s, box-shadow 0.2s;
      display: flex; align-items: center; gap: 8px;
    }
    .quick-links a:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(255,20,147,0.3); color: white; }
    footer { background: #ff69b4; color: white; padding: 40px 0; text-align: center; margin-top: 50px; }
    footer a { color: white; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
    footer .social i { font-size: 20px; margin: 0 10px; color: white; transition: 0.3s; }
    footer .social i:hover { color: #ffe4f2; }
    @media (max-width: 576px) {
      .page-header { border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; padding: 35px 15px; }
      .content-card { padding: 20px; }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
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

<div class="page-header">
  <h1><i class="fa-solid fa-scissors me-2"></i>Welcome to Shakira Salon</h1>
  <p>Hello, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Client') ?>! What would you like to do today?</p>
</div>

<div class="container">
  <div class="content-card">
    <h4 class="text-center mb-3" style="color:#ff1493;">Quick Access</h4>
    <div class="quick-links">
      <a href="services.php"><i class="fa-solid fa-gears"></i> Services</a>
      <a href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book Appointment</a>
      <a href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a>
      <a href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a>
      <a href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a>
      <a href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
    </div>
  </div>
</div>

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
