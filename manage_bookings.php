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
    body { font-family: Arial, sans-serif; background: #f8f8f8; margin: 0; display: flex; }
    .sidebar { width: 250px; background: #ff4f81; color: white; height: 100vh; position: fixed; top: 0; left: 0; padding-top: 20px; }
    .logo { text-align: center; font-size: 1.8rem; font-weight: bold; margin-bottom: 30px; padding: 10px; border-bottom: 2px solid rgba(255,255,255,0.3); }
    .sidebar a { display: block; color: white; padding: 12px 20px; text-decoration: none; }
    .sidebar a:hover { background: #e04371; }
    .main-content { margin-left: 250px; padding: 20px; width: 100%; }
    .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background-color: #ff4081; color: white; }
    .btn { padding: 6px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 2px; text-decoration: none; display: inline-block; }
    .btn-approve { background-color: #28a745; color: white; }
    .btn-reject { background-color: #dc3545; color: white; }
    .btn-delete { background-color: #6c757d; color: white; }
    .status-pending { color: #ffc107; font-weight: bold; }
    .status-approved { color: #28a745; font-weight: bold; }
    .status-rejected { color: #dc3545; font-weight: bold; }
    .proof-img { max-width: 100px; max-height: 100px; border-radius: 8px; }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira </div>
    <nav>
      <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
      <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
      <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
      <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
      <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
      <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Insert</a>
      <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </div>

  <div class="main-content">
    <div class="container">
      <h2>📅 Manage Bookings</h2>
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
                    <span style="color:#aaa;">No Proof</span>
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
    </div>
  </div>
  <?php $conn->close(); ?>
</body>
</html>
