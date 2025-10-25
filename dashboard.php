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

  <!-- Bootstrap + Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(-45deg, #fff0f5, #ffe6ef, #ffd6e0, #ffedf1);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      margin: 0;
    }
    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    .navbar {
      background: linear-gradient(to right, #ff4d88, #ff99bb);
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .navbar .title {
      font-size: 1.3rem;
      font-weight: bold;
      text-shadow: 1px 1px 2px #cc3366;
    }
    .navbar nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      padding: 6px 10px;
      border-radius: 6px;
      transition: all 0.3s;
      font-size: 0.9rem;
    }
    .navbar nav a:hover {
      background-color: rgba(255, 255, 255, 0.2);
      transform: scale(1.05);
    }
    .container {
      max-width: 1100px;
      margin: 20px auto;
      background: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .banner {
      background: linear-gradient(to right, #ff99bb, #ff4d88);
      color: white;
      padding: 50px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      font-size: 2rem;
      font-weight: bold;
    }
    h2 {
      color: #d6005c;
      font-weight: bold;
      border-bottom: 2px solid #ffcce0;
      padding-bottom: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 1.4rem;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="title"><i class="fas fa-scissors me-2"></i>Shakira Salon - Dashboard</div>
    <nav>
      <a href="services.php"><i class="fas fa-cog"></i> Services</a>
      <a href="book_appointment.php"><i class="fas fa-calendar-check"></i> Book</a>
      <a href="booking_history.php"><i class="fas fa-history"></i> Booking History</a>
      <a href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
      <a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a>
      <a href="business_hours_client.php">
        <i class="fas fa-clock"></i> Business Hours
      </a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </div>

  <div class="container">
    <!-- Banner -->
    <div class="banner">
      👋 Hello Client!
    </div>

    <h2><i class="fas fa-home me-2"></i>Welcome to Shakira Salon</h2>
    <p>Experience the beauty and relaxation you deserve. Book your appointment today and let us pamper you!</p>
  </div>
</body>
</html>
