<?php
session_start();
require 'config.php';

// ✅ Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = '';
$errorMsg = '';

// ─── Handle POST actions ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Edit User
    if (isset($_POST['edit_user'])) {
        $id = (int)$_POST['id'];
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $contact_number = trim($_POST['contact_number']);
        $role = trim($_POST['role']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = "Invalid email address.";
        } else {
            // Check duplicate email (exclude current user)
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->execute([$email, $id]);
            if ($check->fetch()) {
                $errorMsg = "Another user with this email already exists.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ?, role = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $contact_number, $role, $id]);
                $success = "User updated successfully!";
            }
        }
    }

    // Delete User
    if (isset($_POST['delete_user'])) {
        $id = (int)$_POST['id'];
        // Prevent deleting self (admin)
        if ($id === (int)$_SESSION['user_id']) {
            $errorMsg = "You cannot delete your own account.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $success = "User deleted successfully!";
        }
    }

    // Toggle Status (Activate / Deactivate)
    if (isset($_POST['toggle_status'])) {
        $id = (int)$_POST['id'];
        $newStatus = $_POST['new_status'];
        // Prevent deactivating self
        if ($id === (int)$_SESSION['user_id'] && $newStatus === 'deactivated') {
            $errorMsg = "You cannot deactivate your own account.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);
            $success = "User " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully!";
        }
    }
}

// ✅ Fetch Registered Users (re-fetch after any changes)
$stmtUsers = $pdo->query("SELECT id, full_name, email, contact_number, role, status FROM users ORDER BY id DESC");
$registeredUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch Hairstylists
$stmtHairstylists = $pdo->query("SELECT id, full_name, phone_number, role FROM hairstylists ORDER BY id DESC");
$hairstylists = $stmtHairstylists->fetchAll(PDO::FETCH_ASSOC);

$currentPage = basename($_SERVER['PHP_SELF']);
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
        .content-card h2 { color:var(--primary);margin-top:0;font-size:1.3rem;margin-bottom:15px; }
        table { width:100%;border-collapse:collapse;margin-bottom:10px;min-width:800px; }
        th, td { padding:10px 8px;border:1px solid #e9ecef;text-align:center;font-size:0.88rem;word-break:break-word; }
        th { background:var(--primary);color:#fff;white-space:nowrap; }
        tr:hover td { background:#fff5f8; }
        .btn-action { padding:5px 10px;border-radius:var(--radius-sm);cursor:pointer;font-weight:bold;margin:2px;text-decoration:none;display:inline-block;font-size:0.82rem;transition:var(--transition);border:none;color:#fff; }
        .btn-action:hover { opacity:0.85;transform:translateY(-1px); }
        .btn-edit { background:#ffc107;color:#000; }
        .btn-edit:hover { background:#e0a800;color:#000; }
        .btn-delete { background:#dc3545;color:#fff; }
        .btn-delete:hover { background:#c82333; }
        .btn-activate { background:#28a745;color:#fff; }
        .btn-activate:hover { background:#218838; }
        .btn-deactivate { background:#6c757d;color:#fff; }
        .btn-deactivate:hover { background:#5a6268; }
        .status-active { color:#28a745;font-weight:bold; }
        .status-deactivated { color:#dc3545;font-weight:bold; }
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

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($errorMsg): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($errorMsg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

    <div class="content-card">
      <h2><i class="fa-solid fa-users"></i> Registered Users</h2>
      <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th> 
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($registeredUsers): ?>
            <?php foreach ($registeredUsers as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['contact_number'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                    <td>
                      <?php $status = $user['status'] ?? 'active'; ?>
                      <span class="status-<?= $status ?>"><?= ucfirst($status) ?></span>
                    </td>
                    <td>
                      <button class="btn-action btn-edit" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" title="Edit">
                        <i class="fa-solid fa-edit"></i>
                      </button>
                      <?php if ($status === 'active'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Deactivate this user?')">
                          <input type="hidden" name="id" value="<?= $user['id'] ?>">
                          <input type="hidden" name="new_status" value="deactivated">
                          <button type="submit" name="toggle_status" class="btn-action btn-deactivate" title="Deactivate">
                            <i class="fa-solid fa-ban"></i>
                          </button>
                        </form>
                      <?php else: ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Activate this user?')">
                          <input type="hidden" name="id" value="<?= $user['id'] ?>">
                          <input type="hidden" name="new_status" value="active">
                          <button type="submit" name="toggle_status" class="btn-action btn-activate" title="Activate">
                            <i class="fa-solid fa-check-circle"></i>
                          </button>
                        </form>
                      <?php endif; ?>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this user? This cannot be undone.')">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button type="submit" name="delete_user" class="btn-action btn-delete" title="Delete">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No users found.</td>
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--primary);color:#fff;">
        <h5 class="modal-title" id="editUserModalLabel"><i class="fa-solid fa-user-edit"></i> Edit User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="edit_user_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Full Name</label>
            <input type="text" name="full_name" id="edit_user_fullname" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="email" id="edit_user_email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Contact Number</label>
            <input type="text" name="contact_number" id="edit_user_contact" class="form-control" placeholder="09XXXXXXXXX" maxlength="11">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Role</label>
            <select name="role" id="edit_user_role" class="form-select" required>
              <option value="customer">Customer</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_user" class="btn" style="background:var(--primary);color:#fff;"><i class="fa-solid fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editUser(user) {
  document.getElementById('edit_user_id').value = user.id;
  document.getElementById('edit_user_fullname').value = user.full_name || '';
  document.getElementById('edit_user_email').value = user.email || '';
  document.getElementById('edit_user_contact').value = user.contact_number || '';
  document.getElementById('edit_user_role').value = user.role || 'customer';
  new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>

</body>
</html>
