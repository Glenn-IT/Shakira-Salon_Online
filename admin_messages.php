<?php
session_start();

// ✅ Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- Database Connection ---
$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Mark as read
if (isset($_GET['read_id'])) {
    $read_id = intval($_GET['read_id']);
    $conn->query("UPDATE contact_messages SET status='read' WHERE id=$read_id");
}

// ✅ Fetch messages
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Contact Messages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; background: #f0f2f8; }
    .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(180deg,#ff4081,#e73370); position: fixed; top: 0; left: 0; color: white; overflow-y: auto; z-index: 1000; display: flex; flex-direction: column; }
    .sidebar h2 { text-align: center; font-weight: bold; font-size: 1.3rem; padding: 22px 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.2); margin: 0; }
    .sidebar h2 small { display: block; font-size: 0.65rem; font-weight: 400; opacity: .8; margin-top: 3px; letter-spacing: 1px; text-transform: uppercase; }
    .sidebar h2 i { margin-right: 6px; }
    .sidebar a { display: flex; align-items: center; gap: 10px; color: white; padding: 12px 20px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: all .2s; white-space: nowrap; border-left: 3px solid transparent; }
    .sidebar a i { width: 18px; text-align: center; flex-shrink: 0; }
    .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
    .sidebar a.active { border-left-color: #fff; }
    .sidebar a:hover:not(.active) { border-left-color: rgba(255,255,255,0.5); }
    .sidebar .nav-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 6px 15px; }
    .admin-topbar { position: fixed; top: 0; left: 260px; right: 0; height: 56px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; z-index: 900; }
    .admin-topbar .page-title { font-size: 1.05rem; font-weight: 700; color: #ff4081; }
    .admin-topbar .admin-info { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #555; }
    .admin-topbar .admin-info i { color: #ff4081; }
    .content { margin-left: 260px; padding: 74px 30px 30px; overflow-x: auto; }
    @media (max-width: 768px) {
      .sidebar { width: 220px; }
      .admin-topbar { left: 220px; }
      .content { margin-left: 220px; padding: 70px 15px 20px; }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-cut"></i> Shakira <small>Admin Panel</small></h2>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php"><i class="fas fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="admin_messages.php" class="active"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="gallery_admin.php"><i class="fa-solid fa-image"></i> Gallery</a>
    <a href="announcement.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
    <a href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
    <div class="nav-divider"></div>
    <a href="insert.php"><i class="fas fa-plus"></i> Add Service</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-envelope"></i> Contact Messages</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="container-fluid mt-4">
      <h2>📩 Contact Messages</h2>
      <table class="table table-bordered table-hover mt-3">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>From</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['subject']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
              <td>
                <?php if ($row['status'] == 'unread'): ?>
                  <span class="badge bg-danger">Unread</span>
                <?php else: ?>
                  <span class="badge bg-success">Read</span>
                <?php endif; ?>
              </td>
              <td><?= $row['created_at'] ?></td>
              <td>
                <a href="?read_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Mark as Read</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
