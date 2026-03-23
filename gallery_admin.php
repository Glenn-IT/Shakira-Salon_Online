<?php
session_start();

// ✅ Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database error: " . $conn->connect_error);
}

// ✅ Detect current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];

    $beforeDir = "images/before/";
    $afterDir  = "images/after/";

    // ✅ Upload Before & After Images
    $beforeFile = $beforeDir . basename($_FILES["before_image"]["name"]);
    $afterFile  = $afterDir . basename($_FILES["after_image"]["name"]);

    if (move_uploaded_file($_FILES["before_image"]["tmp_name"], $beforeFile) &&
        move_uploaded_file($_FILES["after_image"]["tmp_name"], $afterFile)) {

        $stmt = $conn->prepare("INSERT INTO gallery (service_name, before_image, after_image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $service_name, $beforeFile, $afterFile);
        if ($stmt->execute()) {
            $message = "✅ Images uploaded successfully!";
        } else {
            $message = "❌ Database error.";
        }
    } else {
        $message = "❌ Failed to upload images.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Upload Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/shared.css">
    <style>
        body { margin:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f8;color:#333; }
        .content { margin-left:260px;padding:74px 40px 30px; }
        .container {
            max-width:600px;margin:0 auto;background:#fff;padding:30px;
            border-radius:var(--radius-lg);box-shadow:var(--shadow-md);
        }
        h2 { text-align:center;margin-bottom:20px;color:var(--primary); }
        form { display:flex;flex-direction:column;gap:15px; }
        input, button { padding:12px;font-size:16px;border:2px solid #e8e8e8;border-radius:var(--radius-sm);width:100%;box-sizing:border-box;font-family:var(--font-family);transition:var(--transition); }
        input:focus { border-color:var(--primary);outline:none;box-shadow:0 0 0 4px rgba(255,64,129,0.12); }
        button { background:var(--primary);color:white;border:none;cursor:pointer;font-weight:600;border-radius:var(--radius-full); }
        button:hover { background:var(--primary-dark); }
        .msg { text-align:center;margin-bottom:15px;font-weight:bold;color:var(--primary); }
        @media (max-width:768px) { .content { margin-left:220px;padding:70px 20px 20px; } }
        @media (max-width:576px) { .content { margin-left:0;padding:115px 15px 20px; } }
    </style>
</head>
<body>
    <!-- ✅ Sidebar -->
    <div class="admin-sidebar">
        <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small></div>
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
    </div>

    <div class="admin-topbar">
        <span class="page-title"><i class="fa-solid fa-image"></i> Gallery Management</span>
        <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
    </div>

    <!-- ✅ Page Content -->
    <div class="content">
        <div class="container">
            <h2>Upload Before & After Images</h2>
            <?php if ($message) echo "<div class='msg'>$message</div>"; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="service_name" placeholder="Service Name" required>
                <label>Before Image:</label>
                <input type="file" name="before_image" accept="image/*" required>
                <label>After Image:</label>
                <input type="file" name="after_image" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>
