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
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      background: #f8f8f8;
    }
    .sidebar {
      width: 250px;
      min-height: 100vh;
      background: #ff4081;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px 0;
      color: white;
      overflow-y: auto;
      z-index: 100;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: bold;
      font-size: 1.3rem;
      padding: 0 10px;
    }
    .sidebar h2 i {
      margin-right: 8px;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      gap: 8px;
      color: white;
      padding: 11px 18px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
      white-space: nowrap;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(0, 0, 0, 0.2);
    }
    .content {
      margin-left: 250px;
      padding: 25px;
      width: calc(100% - 250px);
      overflow-x: auto;
    }
    @media (max-width: 768px) {
      .sidebar { width: 200px; }
      .content { margin-left: 200px; width: calc(100% - 200px); padding: 15px; }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-cut"></i> Shakira</h2>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php"><i class="fas fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="gallery_admin.php"><i class="fa-solid fa-image"></i> Gallery</a>
    <a href="announcement.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
    <a href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
    <a href="admin_messages.php" class="active"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="insert.php"><i class="fas fa-plus"></i> Add Service</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
