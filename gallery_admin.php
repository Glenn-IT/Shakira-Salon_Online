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
    <style>
        body { 
            margin:0; 
            font-family:'Nunito',sans-serif; 
            background:#f0f2f8; 
            color:#333; 
        }
        /* Sidebar */
        .sidebar { 
            position:fixed; 
            top:0; bottom:0; left:0; 
            width:260px; 
            background:linear-gradient(180deg,#ff4081,#e73370); 
            color:#fff; 
            padding-top:0; 
            display:flex; 
            flex-direction:column;
            overflow-y:auto;
            z-index:1000;
        }
        .sidebar .logo { 
            font-size:1.4rem; 
            font-weight:700; 
            text-align:center; 
            padding:22px 10px 16px;
            border-bottom:1px solid rgba(255,255,255,0.2);
        }
        .sidebar .logo small { display:block; font-size:0.65rem; font-weight:400; opacity:.8; margin-top:3px; letter-spacing:1px; text-transform:uppercase; }
        .sidebar a { 
            display:flex; 
            align-items:center; 
            gap:10px;
            padding:12px 20px; 
            color:#fff; 
            text-decoration:none; 
            font-weight:600;
            font-size:0.88rem;
            white-space:nowrap;
            border-left:3px solid transparent;
            transition:all .2s ease;
        }
        .sidebar a i { width:18px; text-align:center; flex-shrink:0; }
        .sidebar a.active, .sidebar a:hover { background:rgba(255,255,255,0.2); }
        .sidebar a.active { border-left-color:#fff; }
        .sidebar a:hover:not(.active) { border-left-color:rgba(255,255,255,0.5); }
        .sidebar .nav-divider { height:1px; background:rgba(255,255,255,0.15); margin:6px 15px; }
        /* Topbar */
        .admin-topbar { position:fixed; top:0; left:260px; right:0; height:56px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:space-between; padding:0 30px; z-index:900; }
        .admin-topbar .page-title { font-size:1.05rem; font-weight:700; color:#ff4081; }
        .admin-topbar .admin-info { display:flex; align-items:center; gap:8px; font-size:0.88rem; color:#555; }
        .admin-topbar .admin-info i { color:#ff4081; }
        /* Page Content */
        .content { 
            margin-left:260px; 
            padding:74px 40px 30px;
        }
        .container {
            max-width:600px; 
            margin:0 auto; 
            background:#fff; 
            padding:30px; 
            border-radius:12px; 
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        h2 { 
            text-align:center; 
            margin-bottom:20px; 
            color:#ff4081; 
        }
        form { 
            display:flex; 
            flex-direction:column; 
            gap:15px; 
        }
        input, button { 
            padding:12px; 
            font-size:16px; 
            border:1px solid #ccc; 
            border-radius:6px; 
            width:100%;
            box-sizing:border-box;
        }
        button { 
            background:#ff4081; 
            color:white; 
            border:none; 
            cursor:pointer; 
            font-weight:600;
            transition:.3s;
        }
        button:hover { 
            background:#e60073; 
        }
        .msg { 
            text-align:center; 
            margin-bottom:15px; 
            font-weight:bold; 
            color:#ff4081; 
        }
        @media (max-width:768px) {
            .sidebar { width:220px; }
            .admin-topbar { left:220px; }
            .content { margin-left:220px; padding:70px 20px 20px; }
        }
    </style>
</head>
<body>
    <!-- ✅ Sidebar -->
    <div class="sidebar">
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
