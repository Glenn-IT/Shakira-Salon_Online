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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    body { padding-top: 0; }
    .welcome-text { color: var(--text-muted); font-size: 0.95rem; margin-top: 8px; }
    .welcome-text strong { color: #fff; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top salon-navbar">
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
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1><i class="fa-solid fa-scissors me-2"></i>Welcome to Shakira Salon</h1>
  <p>Hello, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Client') ?></strong>! What would you like to do today?</p>
</div>

<div class="container" style="max-width: 900px;">
  <div class="content-card animate-in">
    <h4 class="section-title"><i class="fa-solid fa-bolt me-2"></i>Quick Access</h4>
    <div class="quick-links">
      <a href="services.php"><i class="fa-solid fa-gears"></i> Our Services</a>
      <a href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book Appointment</a>
      <a href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a>
      <a href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a>
      <a href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a>
      <a href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
    </div>
  </div>
</div>

<footer class="salon-footer">
  <div class="container">
    <h5><i class="fa-solid fa-scissors me-2"></i>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot me-2"></i>Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone me-2"></i>+63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope me-2"></i><a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <div class="social">
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
    </div>
    <hr>
    <p class="copyright">&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
