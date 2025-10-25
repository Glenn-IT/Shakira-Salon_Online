<?php
session_start();

// ✅ Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$db = 'shakira_salon';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS business_hours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        open_hour INT NOT NULL,
        close_hour INT NOT NULL,
        status TINYINT(1) NOT NULL DEFAULT 1
    )");

    // Fetch row
    $stmt = $pdo->query("SELECT * FROM business_hours LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $openHour = $row ? (int)$row['open_hour'] : 9;
    $closeHour = $row ? (int)$row['close_hour'] : 18;
    $status = $row ? (int)$row['status'] : 1;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['toggle'])) {
            $newStatus = $status ? 0 : 1;
            $pdo->prepare("UPDATE business_hours SET status = ? WHERE id = ?")
                ->execute([$newStatus, $row['id']]);
            $status = $newStatus;
        } else {
            $newOpen = (int)$_POST['open_hour'];
            $newClose = (int)$_POST['close_hour'];

            if ($row) {
                $pdo->prepare("UPDATE business_hours SET open_hour=?, close_hour=? WHERE id=?")
                    ->execute([$newOpen, $newClose, $row['id']]);
            } else {
                $pdo->prepare("INSERT INTO business_hours (open_hour, close_hour, status) VALUES (?, ?, 1)")
                    ->execute([$newOpen, $newClose]);
            }

            $openHour = $newOpen;
            $closeHour = $newClose;
        }
    }

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
  <title>Admin - Business Hours</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      font-family: Arial, sans-serif;
    }
    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #ff4081;
      height: 100vh;
      padding: 20px 0;
      position: fixed;
      left: 0;
      top: 0;
      color: #fff;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: bold;
    }
    .sidebar a {
      display: block;
      color: #fff;
      padding: 12px 20px;
      text-decoration: none;
      font-size: 16px;
      transition: 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255,255,255,0.2);
    }
    .sidebar a i {
      margin-right: 10px;
    }
    /* Main content */
    .main-content {
      margin-left: 250px;
      padding: 30px;
      width: 100%;
      background: #f3f4f6;
      min-height: 100vh;
    }
    .box {
      background:#fff;
      padding:30px;
      border-radius:10px;
      box-shadow:0 5px 15px rgba(0,0,0,0.2);
      text-align:center;
    }
    .open { color:green; font-weight:bold; font-size:20px; }
    .closed { color:red; font-weight:bold; font-size:20px; }
    button, select {
      padding:8px 15px;
      margin:5px;
      border-radius:6px;
    }
    button {
      background:#007bff;
      color:white;
      border:none;
      cursor:pointer;
    }
    button:hover {
      background:#0056b3;
    }
  </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-scissors"></i> Shakira</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="announcement.php" class="active"><i class="fa-solid fa-clock"></i> Business Hours</a>
    <a href="manage_announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements & Promos</a>
    <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="insert.php"><i class="fa-solid fa-plus"></i> Add Services</a>
    <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>

    <li class="nav-item">
  <a class="nav-link" href="admin_messages.php">
    <i class="fa-solid fa-envelope"></i> Messages
  </a>
</li>

    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
  <div class="box">
    <h2>Business Hours (Admin)</h2>

    <?php if ($isOpen): ?>
        <div class="open">✅ We are OPEN!</div>
    <?php elseif ($status == 0): ?>
        <div class="closed">❌ CLOSED (Disabled)</div>
    <?php else: ?>
        <div class="closed">❌ CLOSED (Outside Hours)</div>
    <?php endif; ?>

    <p>Operating Hours: <strong><?= $openTimeFormatted ?></strong> - <strong><?= $closeTimeFormatted ?></strong></p>
    <p>Current Time: <?= date("g:i A") ?></p>

    <!-- Toggle -->
    <form method="POST">
        <button type="submit" name="toggle"><?= $status ? "🔴 Disable Business" : "🟢 Enable Business" ?></button>
    </form>

    <!-- Update Hours -->
    <form method="POST">
        <label>Open:</label>
        <select name="open_hour">
            <?php for ($i=0; $i<24; $i++): ?>
                <option value="<?= $i ?>" <?= $i==$openHour ? "selected":"" ?>><?= date("g A", strtotime("$i:00")) ?></option>
            <?php endfor; ?>
        </select>

        <label>Close:</label>
        <select name="close_hour">
            <?php for ($i=0; $i<24; $i++): ?>
                <option value="<?= $i ?>" <?= $i==$closeHour ? "selected":"" ?>><?= date("g A", strtotime("$i:00")) ?></option>
            <?php endfor; ?>
        </select>

        <br><br>
        <button type="submit">💾 Save</button>
    </form>
  </div>
</div>

</body>
</html>
