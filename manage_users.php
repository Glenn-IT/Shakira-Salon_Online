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
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f0f2f8; }
        .sidebar { width: 260px; background: linear-gradient(180deg,#ff4081,#e73370); min-height: 100vh; position: fixed; left: 0; top: 0; color: #fff; overflow-y: auto; z-index: 1000; display: flex; flex-direction: column; }
        .sidebar .logo { font-size: 1.4rem; font-weight: bold; text-align: center; padding: 22px 10px 18px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar .logo small { display: block; font-size: 0.65rem; font-weight: 400; opacity: .8; margin-top: 3px; letter-spacing: 1px; text-transform: uppercase; }
        .sidebar nav { flex: 1; }
        .sidebar a { display: flex; align-items: center; gap: 10px; color: #fff; padding: 12px 20px; text-decoration: none; font-size: 0.88rem; font-weight: 600; white-space: nowrap; border-left: 3px solid transparent; transition: all .2s; }
        .sidebar a i { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
        .sidebar a.active { border-left-color: #fff; }
        .sidebar a:hover:not(.active) { border-left-color: rgba(255,255,255,0.5); }
        .sidebar .nav-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 6px 15px; }
        .admin-topbar { position: fixed; top: 0; left: 260px; right: 0; height: 56px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; z-index: 900; }
        .admin-topbar .page-title { font-size: 1.05rem; font-weight: 700; color: #ff4081; }
        .admin-topbar .admin-info { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #555; }
        .admin-topbar .admin-info i { color: #ff4081; }
        .main-content { margin-left: 260px; padding: 74px 30px 30px; }
        @media (max-width: 768px) {
            .sidebar { width: 220px; }
            .admin-topbar { left: 220px; }
            .main-content { margin-left: 220px; padding: 70px 15px 20px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
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

<div class="main-content">
    <h2 class="mb-4 text-center">Registered Users</h2>
    <table class="table table-bordered table-striped mb-5">
        <thead class="table-dark">
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

    <!-- ✅ Hairstylists -->
    <h2 class="mb-4 text-center">Manage Hairstylist</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Full Name</th>
                <th>Contact Number</th> <!-- changed -->
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

</body>
</html>
