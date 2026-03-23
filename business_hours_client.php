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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    body { padding-top: 0; }
    .content-wrap { display:flex;justify-content:center;padding:30px 15px 60px;margin-top:-30px; }
    .box {
      background:var(--bg-white);padding:35px 40px;border-radius:var(--radius-lg);
      box-shadow:var(--shadow-md);text-align:center;width:100%;max-width:440px;
      position:relative;z-index:2;
    }
    .box h4 { color:var(--primary);font-weight:700; }
    .status-open { color:#198754;font-weight:700;font-size:1.4rem;margin:12px 0; }
    .status-closed { color:#dc3545;font-weight:700;font-size:1.4rem;margin:12px 0; }
    @media (max-width:576px) { .box { padding:25px 20px; } }
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
