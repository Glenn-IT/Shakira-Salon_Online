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

    if (in_array($action, ['approve', 'reject', 'complete'])) {
        $status = match($action) {
            'approve'  => 'approved',
            'reject'   => 'rejected',
            'complete' => 'completed',
        };

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
            } elseif ($status === 'completed') {
                $subject = "Your Appointment is Completed 🎉";
                $body = "
                    <h2>Hi $customer_name,</h2>
                    <p>Your appointment for <b>$service</b> on <b>$schedule</b> has been marked as 
                    <span style='color:#6f42c1;font-weight:bold;'>completed</span>. 🎉</p>
                    <p>Thank you for visiting <b>Shakira Salon</b>! We hope you loved your experience. 💅</p>
                    <p>We'd love to see you again soon. Feel free to book another appointment anytime!</p>
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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f0f2f8; margin: 0; }
    .content-card { background:#fff;padding:25px;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow-x:auto; }
    .content-card h2 { color:var(--primary);margin-top:0;font-size:1.3rem; }
    table { width:100%;border-collapse:collapse;margin-bottom:10px;min-width:900px; }
    th, td { padding:10px 8px;border:1px solid #e9ecef;text-align:center;font-size:0.88rem;word-break:break-word; }
    th { background:var(--primary);color:#fff;white-space:nowrap; }
    tr:hover td { background:#fff5f8; }
    .btn { padding:5px 10px;border-radius:var(--radius-sm);cursor:pointer;font-weight:bold;margin:2px;text-decoration:none;display:inline-block;font-size:0.82rem;transition:var(--transition); }
    .btn-approve { background:#28a745;color:#fff; }
    .btn-reject { background:#dc3545;color:#fff; }
    .btn-complete { background:#6f42c1;color:#fff; }
    .btn-delete { background:#6c757d;color:#fff; }
    .btn:hover { opacity:0.85;transform:translateY(-1px); }
    .status-pending { color:#ffc107;font-weight:bold; }
    .status-approved { color:#28a745;font-weight:bold; }
    .status-rejected { color:#dc3545;font-weight:bold; }
    .status-completed { color:#6f42c1;font-weight:bold; }
    .proof-img { max-width:80px;max-height:80px;border-radius:6px; }
  </style>
</head>
<body>
  <div class="admin-sidebar">
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

  <div class="admin-main">
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
                  <?php elseif ($status === 'approved'): ?>
                    <a href="?action=complete&id=<?= $row['id'] ?>" class="btn btn-complete" onclick="return confirm('Mark this booking as Completed?');">Complete</a>
                    <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this booking?');">Delete</a>
                  <?php elseif (in_array($status, ['rejected', 'completed'])): ?>
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
