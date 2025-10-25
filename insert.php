<?php
session_start();

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

// Handle service upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    $imagePath = "";
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
        $targetDir = "uploads/services/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["service_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO services (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $service_name, $description, $price, $imagePath);

    if ($stmt->execute()) {
        $message = "<div class='alert success'>Service added successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}

$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
$currentPage = basename($_SERVER['PHP_SELF']); // to highlight active menu
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Insert Service - Shakira Salon</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        font-family: 'Segoe UI', sans-serif;
        background: #fceef3;
    }
    /* Sidebar */
    .sidebar {
        width: 250px;
        background: #ff4f81;
        height: 100vh;
        padding-top: 20px;
        position: fixed;
        left: 0;
        top: 0;
        color: #fff;
    }
    .logo {
        text-align: center;
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 30px;
        padding: 10px;
        border-bottom: 2px solid rgba(255,255,255,0.3);
    }
    .logo i { margin-right: 8px; }
    .sidebar a {
        display: block;
        color: #fff;
        padding: 12px 20px;
        text-decoration: none;
        font-size: 16px;
        transition: 0.3s;
    }
    .sidebar a:hover,
    .sidebar a.active {
        background: rgba(255,255,255,0.2);
    }
    .sidebar a i { margin-right: 10px; }

    /* Main content */
    .main-content {
        margin-left: 250px;
        padding: 40px;
        width: calc(100% - 250px);
        display: flex;
        flex-direction: column;
        align-items: center; /* Center align */
    }

    .form-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0px 6px 15px rgba(0,0,0,0.05);
        margin-bottom: 40px;
        width: 100%;
        max-width: 900px; /* Center form size */
    }
    h2 {
        color: #ff4f81;
        margin-bottom: 20px;
        text-align: center;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    label {
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 5px;
        color: #333;
        display: block;
    }
    input, textarea, button {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 1rem;
        width: 100%;
    }
    textarea { resize: none; }
    button {
        background: #ff4f81;
        color: white;
        border: none;
        cursor: pointer;
        margin-top: 15px;
        padding: 12px;
        font-size: 1.1rem;
        transition: background 0.3s;
    }
    button:hover { background: #e04371; }
    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
    .alert.success { background: #d4edda; color: #155724; }
    .alert.error { background: #f8d7da; color: #721c24; }

    /* Services grid */
    .services-section {
        width: 100%;
        max-width: 1200px;
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }
    .service-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 20px rgba(0,0,0,0.12);
    }
    .service-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    .service-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .service-card h3 {
        margin: 0 0 10px 0;
        color: #ff4f81;
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
    }
    .service-card p {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: auto;
        text-align: justify;
    }
    .service-footer {
        margin-top: 15px;
        display: flex;
        justify-content: center;
    }
    .service-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        background: #fceef3;
        padding: 8px 12px;
        border-radius: 8px;
    }

    @media(max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr; /* stack form in mobile */
        }
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <i class="fa-solid fa-scissors"></i> Shakira
    </div>
    <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>
    <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="admin_messages.php" class="<?= $currentPage === 'admin_messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Services</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main content -->
<div class="main-content">

    <div class="form-section">
        <h2>Insert New Service</h2>
        <?= $message ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div>
                    <label>Service Name</label>
                    <input type="text" name="service_name" placeholder="Enter service name" required>

                    <label>Description</label>
                    <textarea name="description" placeholder="Enter service description" rows="6" required></textarea>

                    <label>Price (₱)</label>
                    <input type="number" step="0.01" name="price" placeholder="Enter price" required>
                </div>
                <div>
                    <label>Service Image</label>
                    <input type="file" name="service_image" accept="image/*" required>
                    <button type="submit">Insert Service</button>
                </div>
            </div>
        </form>
    </div>

    <div class="services-section">
        <h2>All Services</h2>
        <div class="services-grid">
            <?php while ($row = $services->fetch_assoc()): ?>
                <div class="service-card">
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Service Image">
                    <div class="service-content">
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <div class="service-footer">
                            <div class="service-price">₱<?= number_format($row['price'], 2) ?></div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

</body>
</html>
<?php $conn->close(); ?>
