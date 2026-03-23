<?php
require 'config.php';

// ✅ Fetch Registered Users
$stmtUsers = $pdo->query("SELECT id, full_name, email, contact_number, role FROM users ORDER BY id DESC");
$registeredUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch Hairstylists
$stmtHairstylists = $pdo->query("SELECT id, full_name, phone_number, role FROM hairstylists ORDER BY id DESC");
$hairstylists = $stmtHairstylists->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/shared.css">
    <style>
        * { box-sizing: border-box; }
        body { margin:0;padding:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f8; }
        .content-card { background:#fff;padding:25px;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow-x:auto;margin-bottom:30px; }
        .content-card h2 { color:var(--primary);margin-top:0;font-size:1.3rem; }
        table { width:100%;border-collapse:collapse;margin-bottom:10px;min-width:600px; }
        th, td { padding:10px 8px;border:1px solid #e9ecef;text-align:center;font-size:0.88rem;word-break:break-word; }
        th { background:var(--primary);color:#fff;white-space:nowrap; }
        tr:hover td { background:#fff5f8; }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small></div>
    <nav>
        <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Manage Users</a>
        <a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
        <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
        <a href="admin_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="gallery_admin.php"><i class="fa-solid fa-image"></i> Gallery</a>
        <a href="announcement.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
        <a href="manage_announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
        <div class="nav-divider"></div>
        <a href="insert.php"><i class="fa-solid fa-plus"></i> Add Service</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</div>

<div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-users"></i> Manage Users</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
</div>

<div class="admin-main">
    <div class="content-card">
      <h2><i class="fa-solid fa-users"></i> Registered Users</h2>
      <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th> 
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($registeredUsers): ?>
            <?php foreach ($registeredUsers as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['contact_number']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No users found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ✅ Hairstylists -->
    <div class="content-card">
      <h2><i class="fa-solid fa-scissors"></i> Manage Hairstylist</h2>
      <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($hairstylists): ?>
            <?php foreach ($hairstylists as $stylist): ?>
                <tr>
                    <td><?= htmlspecialchars($stylist['full_name']) ?></td>
                    <td><?= htmlspecialchars($stylist['phone_number']) ?></td>
                    <td><?= htmlspecialchars($stylist['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No hairstylists found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
</div>

</body>
</html>
