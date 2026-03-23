<?php
session_start();

// ✅ Clients & Guests can view
date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$db = 'shakira_salon';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM business_hours LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $openHour = $row ? (int)$row['open_hour'] : 9;
    $closeHour = $row ? (int)$row['close_hour'] : 18;
    $status = $row ? (int)$row['status'] : 1;

    $currentHour = (int)date('G');
    $withinHours = ($currentHour >= $openHour && $currentHour < $closeHour);
    $isOpen = ($status == 1 && $withinHours);

    $openTimeFormatted = date("g A", strtotime("$openHour:00"));
    $closeTimeFormatted = date("g A", strtotime("$closeHour:00"));
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Business Hours - Shakira Salon</title>
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
    .content-wrap {
      display: flex; justify-content: center; padding: 30px 15px 60px;
      margin-top: -30px;
    }
    .box {
      background: #fff; padding: 35px 40px; border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.12); text-align: center;
      width: 100%; max-width: 440px;
    }
    .status-open { color: #198754; font-weight: bold; font-size: 1.4rem; margin: 12px 0; }
    .status-closed { color: #dc3545; font-weight: bold; font-size: 1.4rem; margin: 12px 0; }
    footer { background: #ff69b4; color: white; padding: 40px 0; text-align: center; margin-top: 0; }
    footer a { color: white; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
    footer .social i { font-size: 20px; margin: 0 10px; color: white; transition: 0.3s; }
    footer .social i:hover { color: #ffe4f2; }
    @media (max-width: 576px) {
      .page-header { border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; padding: 35px 15px; }
      .box { padding: 25px 20px; }
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a></li>
        <li class="nav-item"><a class="nav-link active" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1><i class="fa-solid fa-clock me-2"></i>Business Hours</h1>
  <p>Check if we're currently open</p>
</div>

<div class="content-wrap">
  <div class="box">
    <h4 class="mb-3" style="color:#ff1493;"><i class="fa-solid fa-store me-2"></i>Salon Status</h4>
    <?php if ($isOpen): ?>
        <div class="status-open"><i class="fa-solid fa-circle-check me-1"></i> We are OPEN!</div>
    <?php elseif ($status == 0): ?>
        <div class="status-closed"><i class="fa-solid fa-circle-xmark me-1"></i> CLOSED (Temporarily Disabled)</div>
    <?php else: ?>
        <div class="status-closed"><i class="fa-solid fa-circle-xmark me-1"></i> CLOSED (Outside Hours)</div>
    <?php endif; ?>
    <hr>
    <p class="mb-1"><strong>Operating Hours:</strong></p>
    <p class="fs-5"><?= $openTimeFormatted ?> – <?= $closeTimeFormatted ?></p>
    <p class="text-muted">Current Time: <?= date("g:i A") ?></p>
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
