<?php
require 'config.php';

// ✅ Insert new hairstylist (staff)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_hairstylist'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone_number'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if ($fullName && $phone && $role) {
        $stmt = $pdo->prepare("INSERT INTO hairstylists (full_name, phone_number, role, created_at) 
                               VALUES (:full_name, :phone_number, :role, NOW())");
        $stmt->execute([
            ':full_name'   => $fullName,
            ':phone_number'=> $phone,
            ':role'        => $role
        ]);
        header("Location: manage_users.php?added=1");
        exit;
    }
}

// ✅ Update hairstylist role
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'] ?? '';
    $role   = $_POST['role'] ?? '';

    if ($userId && $role) {
        $update = $pdo->prepare("UPDATE hairstylists SET role = :role WHERE id = :id");
        $update->execute([':role' => $role, ':id' => $userId]);
        header("Location: manage_users.php?success=1");
        exit;
    }
}

// ✅ Fetch all hairstylists (staff)
$stmt = $pdo->query("SELECT id, full_name, phone_number, role FROM hairstylists ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        }
        .table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fa-solid fa-scissors"></i> Shakira</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="announcement.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Services</a>
    <a href="admin_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <h2 class="mb-4 text-center">Manage Hairstylists</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ User role updated successfully!</div>
    <?php elseif (isset($_GET['added'])): ?>
        <div class="alert alert-success">✅ New hairstylist added successfully!</div>
    <?php endif; ?>

    <!-- Add Hairstylist Form -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">➕ Add New Hairstylist</div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select" required>
                            <option value="">-- Select Role --</option>
                            <option value="Junior Stylist">Junior Stylist</option>
                            <option value="Senior Stylist">Senior Stylist</option>
                            <option value="Manager">Manager</option>
                            <option value="Administrator">Administrator</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="add_hairstylist" class="btn btn-success w-100">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</body>
</html>
