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
  <title>Business Hours</title>
  <style>
    body { 
      margin:0;
      font-family: Arial, sans-serif; 
      background: #f0f2f5; 
    }
    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ff69b4;
      padding: 10px 20px;
      color: white;
      font-weight: bold;
      flex-wrap: wrap;
      gap: 8px;
    }
    .navbar .logo {
      font-size: 17px;
    }
    .navbar ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
    }
    .navbar ul li a {
      color: white;
      text-decoration: none;
      font-size: 13px;
      padding: 5px 10px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .navbar ul li a:hover {
      background: rgba(0,0,0,0.15);
    }

    /* Content Box */
    .container {
      display:flex; 
      justify-content:center; 
      align-items:center; 
      min-height: calc(100vh - 60px);
      padding: 30px 15px;
    }
    .box { 
      background:#fff; 
      padding:35px 40px; 
      border-radius:10px; 
      box-shadow:0 5px 15px rgba(0,0,0,0.2); 
      text-align:center;
      width:100%;
      max-width:420px;
    }
    .open { color:green; font-weight:bold; font-size:20px; margin:10px 0; }
    .closed { color:red; font-weight:bold; font-size:20px; margin:10px 0; }
    @media (max-width:600px) {
      .navbar { flex-direction:column; align-items:flex-start; }
      .box { padding:25px 20px; }
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<div class="navbar">
  <div class="logo">💇 Shakira Salon</div>
  <ul>
    <li><a href="services.php">💆 Services</a></li>
    <li><a href="book_appointment.php">📅 Book</a></li>
    <li><a href="booking_history.php">🕐 Booking History</a></li>
    <li><a href="gallery.php">🖼️ Gallery</a></li>
    <li><a href="contact.php">📧 Contact Us</a></li>
    <li><a href="business_hours_client.php">🕒 Business Hours</a></li>
    <li><a href="logout.php">↩️ Logout</a></li>
  </ul>
</div>

<!-- ✅ Page Content -->
<div class="container">
  <div class="box">
      <h2>Salon Business Hours</h2>

      <?php if ($isOpen): ?>
          <div class="open">✅ We are OPEN!</div>
      <?php elseif ($status == 0): ?>
          <div class="closed">❌ CLOSED (Temporarily Disabled)</div>
      <?php else: ?>
          <div class="closed">❌ CLOSED (Outside Hours)</div>
      <?php endif; ?>

      <p>Operating Hours: <strong><?= $openTimeFormatted ?></strong> - <strong><?= $closeTimeFormatted ?></strong></p>
      <p>Current Time: <?= date("g:i A") ?></p>
  </div>
</div>

</body>
</html>
