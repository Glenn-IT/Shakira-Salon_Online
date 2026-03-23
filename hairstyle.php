<?php
session_start();
require 'config.php';

// ✅ Insert new hairstylist (staff)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_hairstylist'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone_number'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $error    = '';

    if ($fullName && $phone && $role) {
        // Validate Philippine phone number (must start with 09 and be 11 digits)
        if (!preg_match('/^09\d{9}$/', $phone)) {
            $error = 'invalid_phone';
        } else {
            // Check for duplicate full name
            $checkName = $pdo->prepare("SELECT id FROM hairstylists WHERE full_name = :full_name");
            $checkName->execute([':full_name' => $fullName]);
            
            if ($checkName->fetch()) {
                $error = 'duplicate_name';
            } else {
                // Insert new hairstylist
                $stmt = $pdo->prepare("INSERT INTO hairstylists (full_name, phone_number, role, created_at) 
                                       VALUES (:full_name, :phone_number, :role, NOW())");
                $stmt->execute([
                    ':full_name'   => $fullName,
                    ':phone_number'=> $phone,
                    ':role'        => $role
                ]);
                header("Location: hairstyle.php?added=1");
                exit;
            }
        }
    }
    
    if ($error) {
        header("Location: hairstyle.php?error=$error");
        exit;
    }
}

// ✅ Update hairstylist
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_hairstylist'])) {
    $id       = $_POST['id'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone_number'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $error    = '';

    if ($id && $fullName && $phone && $role) {
        // Validate Philippine phone number (must start with 09 and be 11 digits)
        if (!preg_match('/^09\d{9}$/', $phone)) {
            $error = 'invalid_phone';
        } else {
            // Check for duplicate full name (excluding current record)
            $checkName = $pdo->prepare("SELECT id FROM hairstylists WHERE full_name = :full_name AND id != :id");
            $checkName->execute([':full_name' => $fullName, ':id' => $id]);
            
            if ($checkName->fetch()) {
                $error = 'duplicate_name';
            } else {
                // Update hairstylist
                $update = $pdo->prepare("UPDATE hairstylists SET full_name = :full_name, phone_number = :phone_number, role = :role WHERE id = :id");
                $update->execute([
                    ':full_name'    => $fullName,
                    ':phone_number' => $phone,
                    ':role'         => $role,
                    ':id'           => $id
                ]);
                header("Location: hairstyle.php?updated=1");
                exit;
            }
        }
    }
    
    if ($error) {
        header("Location: hairstyle.php?error=$error");
        exit;
    }
}

// ✅ Delete hairstylist
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $delete = $pdo->prepare("DELETE FROM hairstylists WHERE id = :id");
    $delete->execute([':id' => $deleteId]);
    header("Location: hairstyle.php?deleted=1");
    exit;
}

// ✅ Fetch all hairstylists (staff)
$stmt = $pdo->query("SELECT id, full_name, phone_number, role FROM hairstylists ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current page for sidebar active state
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Hairstylists - Shakira Salon</title>
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
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 0;
            color: #fff;
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
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            padding: 11px 18px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
            white-space: nowrap;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.2);
        }
        .sidebar a i {
            margin-right: 0;
            width: 16px;
            flex-shrink: 0;
        }
        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 25px 30px;
            width: calc(100% - 250px);
        }
        @media (max-width: 768px) {
            .sidebar { width: 200px; }
            .main-content { margin-left: 200px; width: calc(100% - 200px); padding: 15px; }
        }
        .table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-action {
            padding: 5px 12px;
            margin: 2px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit {
            background: #ffc107;
            color: #000;
        }
        .btn-edit:hover {
            background: #e0a800;
            color: #000;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c82333;
            color: #fff;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(0,0,0,0.4);
            padding: 20px;
            box-sizing: border-box;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 40px auto;
            padding: 25px;
            border: 1px solid #888;
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #000;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2><i class="fa-solid fa-scissors"></i> Shakira</h2>
    <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
    <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
    <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Services</a>
    <a href="admin_messages.php" class="<?= $currentPage === 'admin_messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <h2 class="mb-4 text-center">Manage Hairstylists</h2>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show">✅ Hairstylist updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show">✅ New hairstylist added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show">✅ Hairstylist deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'invalid_phone'): ?>
            <div class="alert alert-danger alert-dismissible fade show">❌ Invalid phone number! Please enter a valid Philippine number (e.g., 09123456789).
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] === 'duplicate_name'): ?>
            <div class="alert alert-danger alert-dismissible fade show">❌ A hairstylist with this name already exists!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Add Hairstylist Form -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fa-solid fa-user-plus"></i> Add New Hairstylist
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="full_name" id="add_full_name" class="form-control" placeholder="Full Name" required>
                        <small class="text-muted">Full name must be unique</small>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="phone_number" id="add_phone_number" class="form-control" placeholder="09XXXXXXXXX" pattern="09\d{9}" maxlength="11" required>
                        <small class="text-muted">Format: 09XXXXXXXXX</small>
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
                    <div class="col-md-2">
                        <button type="submit" name="add_hairstylist" class="btn btn-success w-100">
                            <i class="fa-solid fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hairstylists DataGrid Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa-solid fa-table"></i> Hairstylists List
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="30%">Full Name</th>
                        <th width="20%">Phone Number</th>
                        <th width="20%">Role</th>
                        <th width="25%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users && count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td class="text-center">
                                <button onclick="openEditModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['full_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['phone_number'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>')" 
                                        class="btn-action btn-edit">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </button>
                                <a href="?delete_id=<?= $user['id'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this hairstylist?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="fa-solid fa-info-circle"></i> No hairstylists found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3><i class="fa-solid fa-user-edit"></i> Edit Hairstylist</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                <small class="text-muted">Full name must be unique</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="edit_phone_number" class="form-control" placeholder="09XXXXXXXXX" pattern="09\d{9}" maxlength="11" required>
                <small class="text-muted">Format: 09XXXXXXXXX (Philippine number only)</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="edit_role" class="form-select" required>
                    <option value="">-- Select Role --</option>
                    <option value="Junior Stylist">Junior Stylist</option>
                    <option value="Senior Stylist">Senior Stylist</option>
                    <option value="Manager">Manager</option>
                    <option value="Administrator">Administrator</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="update_hairstylist" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Update
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fa-solid fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Phone number validation for Philippine numbers
    function validatePhoneNumber(input) {
        const phonePattern = /^09\d{9}$/;
        if (!phonePattern.test(input.value)) {
            input.setCustomValidity('Please enter a valid Philippine phone number starting with 09 (11 digits total)');
        } else {
            input.setCustomValidity('');
        }
    }

    // Add phone validation listeners
    document.getElementById('add_phone_number').addEventListener('input', function() {
        validatePhoneNumber(this);
    });

    document.getElementById('edit_phone_number').addEventListener('input', function() {
        validatePhoneNumber(this);
    });

    // Allow only numbers in phone fields
    document.getElementById('add_phone_number').addEventListener('keypress', function(e) {
        if (e.key < '0' || e.key > '9') {
            e.preventDefault();
        }
    });

    document.getElementById('edit_phone_number').addEventListener('keypress', function(e) {
        if (e.key < '0' || e.key > '9') {
            e.preventDefault();
        }
    });

    function openEditModal(id, fullName, phone, role) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_phone_number').value = phone;
        document.getElementById('edit_role').value = role;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

</body>
</html>
