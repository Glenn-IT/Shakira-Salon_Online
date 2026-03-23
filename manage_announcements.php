<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$db = 'shakira_salon';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_announcement'])) {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, type, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['title'],
                $_POST['content'],
                $_POST['type'],
                $_POST['status']
            ]);
            $success = "Announcement added successfully!";
        } elseif (isset($_POST['edit_announcement'])) {
            $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, type=?, status=? WHERE id=?");
            $stmt->execute([
                $_POST['title'],
                $_POST['content'],
                $_POST['type'],
                $_POST['status'],
                $_POST['id']
            ]);
            $success = "Announcement updated successfully!";
        } elseif (isset($_POST['delete_announcement'])) {
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Announcement deleted successfully!";
        } elseif (isset($_POST['add_promo'])) {
            // Handle file upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/promos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $imagePath = $uploadDir . uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
            }

            $stmt = $pdo->prepare("INSERT INTO promos (title, description, discount_percentage, discount_amount, valid_from, valid_until, promo_code, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['title'],
                $_POST['description'],
                $_POST['discount_percentage'] ?: null,
                $_POST['discount_amount'] ?: null,
                $_POST['valid_from'],
                $_POST['valid_until'],
                $_POST['promo_code'],
                $imagePath,
                $_POST['status']
            ]);
            $success = "Promo added successfully!";
        } elseif (isset($_POST['edit_promo'])) {
            $imagePath = $_POST['existing_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/promos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $imagePath = $uploadDir . uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
            }

            $stmt = $pdo->prepare("UPDATE promos SET title=?, description=?, discount_percentage=?, discount_amount=?, valid_from=?, valid_until=?, promo_code=?, image=?, status=? WHERE id=?");
            $stmt->execute([
                $_POST['title'],
                $_POST['description'],
                $_POST['discount_percentage'] ?: null,
                $_POST['discount_amount'] ?: null,
                $_POST['valid_from'],
                $_POST['valid_until'],
                $_POST['promo_code'],
                $imagePath,
                $_POST['status'],
                $_POST['id']
            ]);
            $success = "Promo updated successfully!";
        } elseif (isset($_POST['delete_promo'])) {
            $stmt = $pdo->prepare("DELETE FROM promos WHERE id=?");
            $stmt->execute([$_POST['id']]);
            $success = "Promo deleted successfully!";
        }
    }

    // Fetch all announcements
    $announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all promos
    $promos = $pdo->query("SELECT * FROM promos ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Announcements & Promos - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    * { box-sizing: border-box; }
    body { margin:0;padding:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f8; }
    .content-card { background:#fff;padding:25px;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow-x:auto;margin-bottom:30px; }
    .content-card h2 { color:var(--primary);margin-top:0;font-size:1.3rem;margin-bottom:15px; }
    table { width:100%;border-collapse:collapse;margin-bottom:10px;min-width:700px; }
    th, td { padding:10px 8px;border:1px solid #e9ecef;text-align:center;font-size:0.88rem;word-break:break-word; }
    th { background:var(--primary);color:#fff;white-space:nowrap; }
    tr:hover td { background:#fff5f8; }
    .btn-action { padding:5px 10px;border-radius:var(--radius-sm);cursor:pointer;font-weight:bold;margin:2px;text-decoration:none;display:inline-block;font-size:0.82rem;transition:var(--transition);border:none; }
    .btn-action:hover { opacity:0.85;transform:translateY(-1px); }
    .promo-image { max-width:80px;max-height:80px;object-fit:cover;border-radius:5px; }
  </style>
</head>
<body>

<div class="admin-sidebar">
    <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small></div>
    <nav>
        <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
        <a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
        <a href="hairstyle.php"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
        <a href="admin_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="gallery_admin.php"><i class="fa-solid fa-image"></i> Gallery</a>
        <a href="announcement.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
        <a href="manage_announcements.php" class="active"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
        <div class="nav-divider"></div>
        <a href="insert.php"><i class="fa-solid fa-plus"></i> Add Service</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</div>

<div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-bullhorn"></i> Manage Announcements &amp; Promos</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
</div>

<div class="admin-main">
  <h1 class="mb-4"><i class="fa-solid fa-bullhorn"></i> Manage Announcements & Promos</h1>

  <?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Announcements Section -->
  <div class="content-card">
    <h2><i class="fa-solid fa-megaphone"></i> Announcements</h2>
      <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
        <i class="fa-solid fa-plus"></i> Add Announcement
      </button>

      <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Content</th>
              <th>Type</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($announcements as $announcement): ?>
              <tr>
                <td><strong><?= htmlspecialchars($announcement['title']) ?></strong></td>
                <td style="text-align:left;max-width:250px;"><?= htmlspecialchars(substr($announcement['content'], 0, 100)) ?>...</td>
                <td><span class="badge bg-<?= $announcement['type'] === 'info' ? 'info' : ($announcement['type'] === 'success' ? 'success' : ($announcement['type'] === 'warning' ? 'warning' : 'danger')) ?>"><?= ucfirst($announcement['type']) ?></span></td>
                <td><span class="badge bg-<?= $announcement['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($announcement['status']) ?></span></td>
                <td><?= date('M d, Y', strtotime($announcement['created_at'])) ?></td>
                <td>
                  <button class="btn-action btn btn-sm btn-warning" onclick="editAnnouncement(<?= htmlspecialchars(json_encode($announcement)) ?>)">
                    <i class="fa-solid fa-edit"></i>
                  </button>
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this announcement?')">
                    <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                    <button type="submit" name="delete_announcement" class="btn-action btn btn-sm btn-danger">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
  </div>

  <!-- Promos Section -->
  <div class="content-card">
    <h2><i class="fa-solid fa-tags"></i> Promos</h2>
      <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addPromoModal">
        <i class="fa-solid fa-plus"></i> Add Promo
      </button>

      <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Discount</th>
              <th>Valid Period</th>
              <th>Code</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($promos as $promo): ?>
              <tr>
                <td>
                  <?php if ($promo['image']): ?>
                    <img src="<?= htmlspecialchars($promo['image']) ?>" class="promo-image" alt="Promo">
                  <?php else: ?>
                    <span class="text-muted">No image</span>
                  <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($promo['title']) ?></strong></td>
                <td>
                  <?php if ($promo['discount_percentage']): ?>
                    <span class="badge bg-success"><?= $promo['discount_percentage'] ?>% OFF</span>
                  <?php elseif ($promo['discount_amount']): ?>
                    <span class="badge bg-success">₱<?= number_format($promo['discount_amount'], 2) ?> OFF</span>
                  <?php endif; ?>
                </td>
                <td><?= date('M d, Y', strtotime($promo['valid_from'])) ?> - <?= date('M d, Y', strtotime($promo['valid_until'])) ?></td>
                <td><code><?= htmlspecialchars($promo['promo_code']) ?></code></td>
                <td><span class="badge bg-<?= $promo['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($promo['status']) ?></span></td>
                <td>
                  <button class="btn-action btn btn-sm btn-warning" onclick="editPromo(<?= htmlspecialchars(json_encode($promo)) ?>)">
                    <i class="fa-solid fa-edit"></i>
                  </button>
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this promo?')">
                    <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                    <button type="submit" name="delete_promo" class="btn-action btn btn-sm btn-danger">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
  </div>
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="info">Info</option>
              <option value="success">Success</option>
              <option value="warning">Warning</option>
              <option value="danger">Danger</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_announcement" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="edit_announcement_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="edit_announcement_title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" id="edit_announcement_content" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" id="edit_announcement_type" class="form-select" required>
              <option value="info">Info</option>
              <option value="success">Success</option>
              <option value="warning">Warning</option>
              <option value="danger">Danger</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="edit_announcement_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_announcement" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Promo Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Promo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Promo Code</label>
              <input type="text" name="promo_code" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Discount Percentage (%)</label>
              <input type="number" name="discount_percentage" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">OR Discount Amount (₱)</label>
              <input type="number" step="0.01" name="discount_amount" class="form-control" min="0">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Valid From</label>
              <input type="date" name="valid_from" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Valid Until</label>
              <input type="date" name="valid_until" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Promo Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_promo" class="btn btn-success">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Promo Modal -->
<div class="modal fade" id="editPromoModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Promo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_promo_id">
        <input type="hidden" name="existing_image" id="edit_promo_existing_image">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" id="edit_promo_title" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Promo Code</label>
              <input type="text" name="promo_code" id="edit_promo_code" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="edit_promo_description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Discount Percentage (%)</label>
              <input type="number" name="discount_percentage" id="edit_promo_percentage" class="form-control" min="0" max="100">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">OR Discount Amount (₱)</label>
              <input type="number" step="0.01" name="discount_amount" id="edit_promo_amount" class="form-control" min="0">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Valid From</label>
              <input type="date" name="valid_from" id="edit_promo_from" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Valid Until</label>
              <input type="date" name="valid_until" id="edit_promo_until" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Promo Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">Leave empty to keep current image</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="edit_promo_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="edit_promo" class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editAnnouncement(announcement) {
  document.getElementById('edit_announcement_id').value = announcement.id;
  document.getElementById('edit_announcement_title').value = announcement.title;
  document.getElementById('edit_announcement_content').value = announcement.content;
  document.getElementById('edit_announcement_type').value = announcement.type;
  document.getElementById('edit_announcement_status').value = announcement.status;
  new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
}

function editPromo(promo) {
  document.getElementById('edit_promo_id').value = promo.id;
  document.getElementById('edit_promo_title').value = promo.title;
  document.getElementById('edit_promo_description').value = promo.description;
  document.getElementById('edit_promo_percentage').value = promo.discount_percentage || '';
  document.getElementById('edit_promo_amount').value = promo.discount_amount || '';
  document.getElementById('edit_promo_from').value = promo.valid_from;
  document.getElementById('edit_promo_until').value = promo.valid_until;
  document.getElementById('edit_promo_code').value = promo.promo_code || '';
  document.getElementById('edit_promo_status').value = promo.status;
  document.getElementById('edit_promo_existing_image').value = promo.image || '';
  new bootstrap.Modal(document.getElementById('editPromoModal')).show();
}
</script>

</body>
</html>
