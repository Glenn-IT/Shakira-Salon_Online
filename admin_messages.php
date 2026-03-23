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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    * { box-sizing: border-box; }
    body { margin:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f8; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="admin-sidebar">
    <div class="logo"><i class="fas fa-cut"></i> Shakira <small>Admin Panel</small></div>
    <nav>
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
    </nav>
  </div>

  <div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-envelope"></i> Contact Messages</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
  </div>

  <!-- Main Content -->
  <div class="admin-main">
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
