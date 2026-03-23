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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    * { box-sizing: border-box; }
    body { margin:0;font-family:'Nunito',sans-serif;background:#f0f2f8; }

    /* Banner */
    .banner {
      position:relative;
      background:linear-gradient(135deg,#ff4081,#ff80ab,#ff4081);
      background-size:400% 400%;
      color:white;padding:25px 30px;border-radius:var(--radius-lg);
      box-shadow:var(--shadow-md);margin-bottom:30px;
      display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;
      animation:gradientBG 15s ease infinite;overflow:hidden;
    }
    @keyframes gradientBG {
      0% { background-position:0% 50%; }
      50% { background-position:100% 50%; }
      100% { background-position:0% 50%; }
    }
    .banner h1 { margin:0;font-size:2rem;display:flex;align-items:center;gap:12px; }
    .banner h2 { font-size:1.2rem;font-weight:400;margin:5px 0 0 0;color:rgba(255,255,255,0.9); }
    .floating-icons { position:absolute;top:-10px;right:-10px;font-size:3rem;opacity:0.15;animation:float 6s ease-in-out infinite; }
    @keyframes float { 0%,100%{transform:translateY(0px)} 50%{transform:translateY(-10px)} }

    .content-card { background:#fff;padding:25px;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow-x:auto; }
    .content-card h2 { color:var(--primary);margin-top:0;font-size:1.3rem; }
    table { width:100%;border-collapse:collapse;margin-bottom:10px;min-width:400px; }
    th, td { padding:10px 8px;border:1px solid #e9ecef;text-align:center;font-size:0.88rem;word-break:break-word; }
    th { background:var(--primary);color:#fff;white-space:nowrap; }
    tr:hover td { background:#fff5f8; }
  </style>
</head>
<body>

<div class="admin-sidebar">
  <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small></div>
  <nav>
    <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="admin_messages.php" class="<?= $currentPage === 'admin_messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>
    <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock"></i> Business Hours</a>
    <a href="manage_announcements.php" class="<?= $currentPage === 'manage_announcements.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <div class="nav-divider"></div>
    <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Service</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </nav>
</div>

<div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-house"></i> Admin Home</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></span></div>
</div>

<div class="admin-main">

  <div class="banner">
    <div>
      <h1><i class="fa-solid fa-circle-user"></i> Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>!</h1>
      <h2>Your daily dashboard at a glance</h2>
    </div>
    <i class="fa-solid fa-star floating-icons"></i>
  </div>

  <div class="content-card">
    <h2><i class="fa-solid fa-concierge-bell"></i> Services Offered</h2>
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
</div>

</body>
</html>
