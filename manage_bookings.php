<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // composer require phpmailer/phpmailer

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Send Email Function
function sendMail($to, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ityourboiaki@gmail.com';   // Gmail account
        $mail->Password   = 'fojt zvoj imdr xwnw';      // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = "UTF-8";

        // Sender
        $mail->setFrom('ityourboiaki@gmail.com', 'Shakira Salon');

        $mail->addAddress($to, $name);

        $mail->addBCC('nicoleacojedo03@gmail.com', 'Shakira Salon Admin');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = strtolower($_GET['action']);

    if (in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';

        $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();

        $q = $conn->prepare("SELECT customer_name, email, service, schedule FROM appointments WHERE id=?");
        $q->bind_param("i", $id);
        $q->execute();
        $q->bind_result($customer_name, $email, $service, $schedule);
        $q->fetch();
        $q->close();

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($status === 'approved') {
                $subject = "Your Booking is Approved ✅";
                $body = "
                    <h2>Hi $customer_name,</h2>
                    <p>Your booking for <b>$service</b> on <b>$schedule</b> has been 
                    <span style='color:green;'>approved</span>.</p>
                    <p>We look forward to seeing you! 💇‍♀️</p>
                    <br><small>Shakira Salon</small>
                ";
            } else {
                $subject = "Your Booking was Rejected ❌";
                $body = "
                    <h2>Hi $customer_name,</h2>
                    <p>Unfortunately, your booking for <b>$service</b> on <b>$schedule</b> has been 
                    <span style='color:red;'>rejected</span>.</p>
                    <p>Please contact us for rescheduling or more details.</p>
                    <br><small>Shakira Salon</small>
                ";
            }

            sendMail($email, $customer_name, $subject, $body);
        }
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_bookings.php");
    exit;
}

$result = $conn->query("SELECT * FROM appointments ORDER BY created_at DESC");
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings - Shakira Salon</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f2f8; margin: 0; }
    /* ── Sidebar ── */
    .sidebar { width: 260px; background: linear-gradient(180deg,#ff4081,#e73370); color: white; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000; display: flex; flex-direction: column; }
    .sidebar .logo { font-size: 1.4rem; font-weight: bold; text-align: center; padding: 22px 10px 18px; border-bottom: 1px solid rgba(255,255,255,0.2); }
    .sidebar .logo small { display: block; font-size: 0.65rem; font-weight: 400; opacity: .8; margin-top: 3px; letter-spacing: 1px; text-transform: uppercase; }
    .sidebar nav a { display: flex; align-items: center; gap: 10px; color: white; padding: 12px 20px; text-decoration: none; font-size: 0.88rem; font-weight: 600; white-space: nowrap; border-left: 3px solid transparent; transition: all .2s; }
    .sidebar nav a i { width: 18px; text-align: center; flex-shrink: 0; }
    .sidebar nav a:hover:not(.active) { background: rgba(255,255,255,0.1); border-left-color: rgba(255,255,255,0.5); }
    .sidebar nav a.active { background: rgba(255,255,255,0.2); border-left-color: #fff; }
    .sidebar nav .nav-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 6px 15px; }
    /* ── Topbar ── */
    .admin-topbar { position: fixed; top: 0; left: 260px; right: 0; height: 56px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; z-index: 900; }
    .admin-topbar .page-title { font-size: 1.05rem; font-weight: 700; color: #ff4081; }
    .admin-topbar .admin-info { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #555; }
    .admin-topbar .admin-info i { color: #ff4081; }
    /* ── Main Content ── */
    .main-content { margin-left: 260px; padding: 74px 30px 30px; min-height: 100vh; }
    .content-card { background: #fff; padding: 25px; border-radius: 14px; box-shadow: 0 4px 14px rgba(0,0,0,0.08); overflow-x: auto; }
    .content-card h2 { color: #ff4081; margin-top: 0; font-size: 1.3rem; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; min-width: 900px; }
    th, td { padding: 10px 8px; border: 1px solid #e9ecef; text-align: center; font-size: 0.88rem; word-break: break-word; }
    th { background-color: #ff4081; color: white; white-space: nowrap; }
    tr:hover td { background: #fff5f8; }
    .btn { padding: 5px 10px; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 2px; text-decoration: none; display: inline-block; font-size: 0.82rem; }
    .btn-approve { background-color: #28a745; color: white; }
    .btn-reject { background-color: #dc3545; color: white; }
    .btn-delete { background-color: #6c757d; color: white; }
    .status-pending { color: #ffc107; font-weight: bold; }
    .status-approved { color: #28a745; font-weight: bold; }
    .status-rejected { color: #dc3545; font-weight: bold; }
    .proof-img { max-width: 80px; max-height: 80px; border-radius: 6px; }
    @media (max-width: 768px) {
      .sidebar { width: 220px; }
      .admin-topbar { left: 220px; }
      .main-content { margin-left: 220px; padding: 70px 15px 20px; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira <small>Admin Panel</small></div>
    <nav>
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
    </nav>
  </div>

  <div class="admin-topbar">
    <span class="page-title"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</span>
    <div class="admin-info"><i class="fa-solid fa-user-shield"></i> <span>Admin</span></div>
  </div>

  <div class="main-content">
    <div class="content-card">
      <h2><i class="fa-solid fa-calendar-check"></i> Manage Bookings</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Customer</th><th>Phone</th><th>Address</th>
            <th>Service</th><th>Price</th><th>Schedule</th>
            <th>Stylist</th><th>Payment Proof</th><th>Status</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
              $status = strtolower($row['status']); ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['service']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= htmlspecialchars($row['schedule']) ?></td>
                <td><?= htmlspecialchars($row['stylist']) ?></td>
                <td>
                  <?php if (!empty($row['payment_proof'])): ?>
                    <a href="uploads/payment_proofs/<?= htmlspecialchars($row['payment_proof']) ?>" target="_blank">
                      <img src="uploads/payment_proofs/<?= htmlspecialchars($row['payment_proof']) ?>" class="proof-img">
                    </a>
                  <?php else: ?>
                    <span style="color:#555; font-weight: bold;">Cash</span>
                  <?php endif; ?>
                </td>
                <td><span class="status-<?= $status ?>"><?= ucfirst($status) ?></span></td>
                <td>
                  <?php if ($status === 'pending'): ?>
                    <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-approve" onclick="return confirm('Approve this booking?');">Approve</a>
                    <a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-reject" onclick="return confirm('Reject this booking?');">Reject</a>
                  <?php elseif (in_array($status, ['approved','rejected'])): ?>
                    <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this booking?');">Delete</a>
                  <?php else: ?> — <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="11">No bookings found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div><!-- /content-card -->
  </div><!-- /main-content -->
  <?php $conn->close(); ?>
</body>
</html>
