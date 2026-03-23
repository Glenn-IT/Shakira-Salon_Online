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

// Handle service deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    
    // Get image path before deletion
    $imageStmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
    $imageStmt->bind_param("i", $deleteId);
    $imageStmt->execute();
    $imageResult = $imageStmt->get_result();
    
    if ($imageRow = $imageResult->fetch_assoc()) {
        $imagePath = $imageRow['image'];
        
        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $deleteStmt->bind_param("i", $deleteId);
        
        if ($deleteStmt->execute()) {
            // Delete image file if exists
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $message = "<div class='alert success'>Service deleted successfully!</div>";
        } else {
            $message = "<div class='alert error'>Error deleting service.</div>";
        }
        $deleteStmt->close();
    }
    $imageStmt->close();
}

// Handle service update
if (isset($_POST['update_service'])) {
    $serviceId = intval($_POST['service_id']);
    $service_name = trim($_POST['edit_service_name']);
    $description = trim($_POST['edit_description']);
    $price = floatval($_POST['edit_price']);
    
    // Check if new name conflicts with other services
    $checkStmt = $conn->prepare("SELECT id FROM services WHERE name = ? AND id != ?");
    $checkStmt->bind_param("si", $service_name, $serviceId);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $message = "<div class='alert error'>A service with this name already exists!</div>";
        $checkStmt->close();
    } else {
        $checkStmt->close();
        
        // Get current image
        $currentImageStmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
        $currentImageStmt->bind_param("i", $serviceId);
        $currentImageStmt->execute();
        $currentImageResult = $currentImageStmt->get_result();
        $currentImage = $currentImageResult->fetch_assoc()['image'];
        $currentImageStmt->close();
        
        $imagePath = $currentImage;
        
        // Handle new image upload
        if (isset($_FILES['edit_service_image']) && $_FILES['edit_service_image']['error'] == 0) {
            $targetDir = "uploads/services/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . "_" . basename($_FILES["edit_service_image"]["name"]);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES["edit_service_image"]["tmp_name"], $targetFile)) {
                // Delete old image
                if (file_exists($currentImage)) {
                    unlink($currentImage);
                }
                $imagePath = $targetFile;
            }
        }
        
        $updateStmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $updateStmt->bind_param("ssdsi", $service_name, $description, $price, $imagePath, $serviceId);
        
        if ($updateStmt->execute()) {
            $message = "<div class='alert success'>Service updated successfully!</div>";
        } else {
            $message = "<div class='alert error'>" . $conn->error . "</div>";
        }
        $updateStmt->close();
    }
}

// Handle service upload
if (isset($_POST['add_service'])) {
    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // Check if service name already exists
    $checkStmt = $conn->prepare("SELECT id FROM services WHERE name = ?");
    $checkStmt->bind_param("s", $service_name);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $message = "<div class='alert error'> A service with this name already exists!</div>";
        $checkStmt->close();
    } else {
        $checkStmt->close();

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
            $message = "<div class='alert error'> " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
$currentPage = basename($_SERVER['PHP_SELF']); // to highlight active menu
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Service - Shakira Salon</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/shared.css">
<style>
    body { margin:0;padding:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f8; }

    .form-section {
        background:white;padding:25px;border-radius:var(--radius-lg);
        box-shadow:var(--shadow-sm);margin-bottom:40px;width:100%;max-width:900px;
    }
    h2 { color:var(--primary);margin-bottom:20px;text-align:center; }
    .form-grid { display:grid;grid-template-columns:2fr 1fr;gap:20px; }
    label { font-weight:bold;margin-top:10px;margin-bottom:5px;color:#333;display:block; }
    input, textarea, button { padding:10px;border-radius:var(--radius-sm);border:2px solid #e8e8e8;font-size:1rem;width:100%;font-family:var(--font-family);transition:var(--transition); }
    input:focus, textarea:focus { border-color:var(--primary);outline:none;box-shadow:0 0 0 4px rgba(255,64,129,0.12); }
    textarea { resize:none; }
    button { background:var(--primary);color:white;border:none;cursor:pointer;margin-top:15px;padding:12px;font-size:1.1rem;font-weight:600;border-radius:var(--radius-full); }
    button:hover { background:var(--primary-dark); }
    .alert { padding:12px;border-radius:var(--radius-sm);margin-bottom:15px;text-align:center;font-weight:bold; }
    .alert.success { background:#d4edda;color:#155724; }
    .alert.error { background:#f8d7da;color:#721c24; }

    .services-section { width:100%;max-width:1200px; }
    .services-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:25px; }
    .service-card { background:white;border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);transition:var(--transition);display:flex;flex-direction:column;height:100%; }
    .service-card:hover { transform:translateY(-5px);box-shadow:var(--shadow-md); }
    .service-card img { width:100%;height:220px;object-fit:cover; }
    .service-content { padding:15px;display:flex;flex-direction:column;flex-grow:1; }
    .service-card h3 { margin:0 0 10px;color:var(--primary);font-size:1.2rem;font-weight:bold;text-align:center; }
    .service-card p { font-size:0.95rem;color:#555;margin-bottom:auto;text-align:justify; }
    .service-footer { margin-top:15px;display:flex;justify-content:center; }
    .service-price { font-size:1.2rem;font-weight:bold;color:#333;background:#fceef3;padding:8px 12px;border-radius:var(--radius-sm); }
    .service-actions { display:flex;gap:10px;justify-content:center;margin-top:10px; }
    .btn-edit, .btn-delete { padding:8px 16px;border-radius:var(--radius-sm);border:none;cursor:pointer;font-size:0.9rem;transition:var(--transition);display:inline-flex;align-items:center;gap:5px; }
    .btn-edit { background:#4CAF50;color:white; }
    .btn-edit:hover { background:#45a049; }
    .btn-delete { background:#f44336;color:white; }
    .btn-delete:hover { background:#da190b; }

    /* Modal */
    .modal { display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.5);animation:fadeIn 0.3s; }
    @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    .modal-content { background-color:white;margin:5% auto;padding:0;border-radius:var(--radius-lg);width:90%;max-width:600px;box-shadow:var(--shadow-lg);animation:slideDown 0.3s; }
    @keyframes slideDown { from{transform:translateY(-50px);opacity:0} to{transform:translateY(0);opacity:1} }
    .modal-header { background:var(--primary);color:white;padding:20px;border-radius:var(--radius-lg) var(--radius-lg) 0 0;display:flex;justify-content:space-between;align-items:center; }
    .modal-header h2 { margin:0;color:white; }
    .close { color:white;font-size:28px;font-weight:bold;cursor:pointer;transition:0.3s; }
    .close:hover { color:#fceef3; }
    .modal-body { padding:25px; }
    .modal-body label { display:block;margin-top:15px;margin-bottom:5px;font-weight:bold;color:#333; }
    .modal-body label:first-of-type { margin-top:0; }
    .modal-body input, .modal-body textarea { width:100%;padding:10px;border:2px solid #e8e8e8;border-radius:var(--radius-sm);font-size:1rem;box-sizing:border-box; }
    .modal-body textarea { resize:vertical;min-height:100px; }
    .modal-footer { padding:15px 25px 25px;display:flex;gap:10px;justify-content:flex-end; }
    .btn-cancel, .btn-save { padding:10px 20px;border-radius:var(--radius-sm);border:none;cursor:pointer;font-size:1rem;transition:var(--transition); }
    .btn-cancel { background:#ccc;color:#333; }
    .btn-cancel:hover { background:#bbb; }
    .btn-save { background:var(--primary);color:white; }
    .btn-save:hover { background:var(--primary-dark); }
    .current-image { max-width:100%;height:auto;border-radius:var(--radius-sm);margin-top:10px;max-height:200px;object-fit:cover; }

    @media(max-width:768px) {
        .form-grid { grid-template-columns:1fr; }
        .modal-content { width:95%;margin:5% auto; }
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="logo">
        <i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small>
    </div>
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
    <span class="page-title"><i class="fa-solid fa-plus"></i> Add / Manage Services</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
</div>

<!-- Main content -->
<div class="admin-main" style="display:flex;flex-direction:column;align-items:center;">

    <div class="form-section">
        <h2>Add New Service</h2>
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
                    <button type="submit" name="add_service">Add Service</button>
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
                        <div class="service-actions">
                            <button class="btn-edit" onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['name'])) ?>', '<?= htmlspecialchars(addslashes($row['description'])) ?>', <?= $row['price'] ?>, '<?= htmlspecialchars($row['image']) ?>')">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <button class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['name'])) ?>')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fa-solid fa-edit"></i> Edit Service</h2>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" id="edit_service_id" name="service_id">
                
                <label>Service Name</label>
                <input type="text" id="edit_service_name" name="edit_service_name" placeholder="Enter service name" required>
                
                <label>Description</label>
                <textarea id="edit_description" name="edit_description" placeholder="Enter service description" required></textarea>
                
                <label>Price (₱)</label>
                <input type="number" step="0.01" id="edit_price" name="edit_price" placeholder="Enter price" required>
                
                <label>Current Image</label>
                <img id="current_image" class="current-image" src="" alt="Current Service Image">
                
                <label>New Image (Optional - leave empty to keep current)</label>
                <input type="file" name="edit_service_image" accept="image/*">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" name="update_service" class="btn-save">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open edit modal
    function openEditModal(id, name, description, price, image) {
        document.getElementById('edit_service_id').value = id;
        document.getElementById('edit_service_name').value = name;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_price').value = price;
        document.getElementById('current_image').src = image;
        document.getElementById('editModal').style.display = 'block';
    }

    // Close edit modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }

    // Confirm delete
    function confirmDelete(id, name) {
        if (confirm('Are you sure you want to delete "' + name + '"?\nThis action cannot be undone.')) {
            window.location.href = 'insert.php?delete=' + id;
        }
    }
</script>

</body>
</html>
<?php $conn->close(); ?>
