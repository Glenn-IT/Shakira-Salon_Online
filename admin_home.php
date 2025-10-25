<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Home - Shakira Salon</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Nunito', sans-serif;
      background: #f0f2f8;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: 260px;
      background: #ff4081;
      color: #fff;
      padding-top: 30px;
      transition: transform 0.3s ease-in-out;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .logo {
      font-size: 1.8rem;
      text-align: center;
      margin-bottom: 2rem;
      font-weight: bold;
    }

    nav a {
      display: flex;
      align-items: center;
      padding: 15px 30px;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      border-left: 4px solid transparent;
      transition: background 0.3s, border-left-color 0.3s;
    }

    nav a:hover, nav a.active {
      background: #e73370;
      border-left-color: #fff;
    }

    nav a i {
      margin-right: 12px;
    }

    .sidebar-footer {
      position: absolute;
      bottom: 20px;
      width: 100%;
      text-align: center;
      font-size: 0.9rem;
    }

    /* Main content */
    .main-content {
      margin-left: 260px;
      padding: 40px;
      flex: 1;
      transition: margin-left 0.3s ease-in-out;
    }

    .sidebar.hidden + .main-content {
      margin-left: 0;
    }

    /* Banner */
    .banner {
      position: relative;
      background: linear-gradient(135deg, #ff4081, #ff80ab, #ff4081);
      background-size: 400% 400%;
      color: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      animation: gradientBG 15s ease infinite;
      overflow: hidden;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .banner h1 {
      margin: 0;
      font-size: 2rem;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .banner h2 {
      font-size: 1.2rem;
      font-weight: 400;
      margin: 5px 0 0 0;
      color: rgba(255,255,255,0.9);
    }

    .floating-icons {
      position: absolute;
      top: -10px;
      right: -10px;
      font-size: 3rem;
      opacity: 0.15;
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    h2.section-title {
      color: #ff4081;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background: #ff4081;
      color: #fff;
    }

    tr:last-child td {
      border-bottom: none;
    }

    /* Toggle button for mobile */
    .toggle-btn {
      display: none;
      background: #ff4081;
      color: #fff;
      border: none;
      padding: 12px 16px;
      font-size: 1.2rem;
      cursor: pointer;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      .toggle-btn {
        display: inline-block;
      }
    }
  </style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira</div>
  <nav>
    <a href="admin_home.php" class="<?= $currentPage === 'admin_home.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-house"></i> Home
    </a>
    <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>
    <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-users"></i> Manage Users
    </a>
    <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-calendar-check"></i> Manage Bookings
    </a>
    <a href="logout.php">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </nav>
  <div class="sidebar-footer">Admin Panel</div>
</div>

<div class="main-content">
  <button class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i> Menu</button>

  <div class="banner">
    <div>
      <h1><i class="fa-solid fa-circle-user"></i> Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>!</h1>
      <h2>Your daily dashboard at a glance</h2>
    </div>
    <i class="fa-solid fa-star floating-icons"></i>
  </div>

  <h2 class="section-title">Services Offered</h2>
  <table>
    <thead>
      <tr>
        <th>Service</th>
        <th>Price (₱)</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Haircut</td><td>150.00</td></tr>
      <tr><td>Hair Coloring</td><td>200.00</td></tr>
      <tr><td>Rebonding</td><td>1500.00</td></tr>
    </tbody>
  </table>
</div>

<script>
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("show");
}
</script>

</body>
</html>
