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
            display:flex;
        }
        /* Sidebar */
        .sidebar { 
            position:fixed; 
            top:0; bottom:0; left:0; 
            width:260px; 
            background:#ff4081; 
            color:#fff; 
            padding-top:30px; 
            display:flex; 
            flex-direction:column;
            overflow-y:auto;
            z-index:100;
        }
        .sidebar .logo { 
            font-size:1.5rem; 
            font-weight:700; 
            text-align:center; 
            margin-bottom:2rem;
            padding:0 10px;
        }
        .sidebar a { 
            display:flex; 
            align-items:center; 
            gap:8px;
            padding:13px 20px; 
            color:#fff; 
            text-decoration:none; 
            font-weight:600;
            font-size:0.9rem;
            white-space:nowrap;
            transition:all .3s ease;
        }
        .sidebar a.active, 
        .sidebar a:hover { 
            background:#e73370; 
        }

        /* Page Content */
        .content { 
            margin-left:260px; 
            padding:30px 40px; 
            width:calc(100% - 260px);
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
            .sidebar { width:200px; }
            .content { margin-left:200px; width:calc(100% - 200px); padding:20px; }
        }
    </style>
</head>
<body>
    <!-- ✅ Sidebar -->
    <div class="sidebar">
        <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira Salon</div>
        <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i>&nbsp; Dashboard</a>
        <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i>&nbsp; Manage Users</a>
        <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i>&nbsp; Manage Bookings</a>
        <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i>&nbsp; Hairstyles</a>
        <a href="admin_messages.php" class="<?= $currentPage === 'admin_messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i>&nbsp; Messages</a>
        <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i>&nbsp; Gallery</a>
        <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock"></i>&nbsp; Business Hours</a>
        <a href="manage_announcements.php" class="<?= $currentPage === 'manage_announcements.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i>&nbsp; Announcements</a>
        <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i>&nbsp; Add Service</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>&nbsp; Logout</a>
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
