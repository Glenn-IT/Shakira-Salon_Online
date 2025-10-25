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
        body {
            margin: 0;
            padding: 0;
            display: flex;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }
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
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-scissors"></i> Shakira</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="announcement.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <a href="admin_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="insert.php"><i class="fa-solid fa-plus"></i> Add Services</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
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
