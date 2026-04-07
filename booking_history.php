<?php
session_start();
require 'config.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ✅ Fetch user's email to match with appointments
$userEmail = '';
try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userEmail = $user['email'] ?? '';
} catch (Exception $e) {
    $userEmail = '';
}

// ✅ Fetch user's booking history
$bookings = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE email = ? ORDER BY created_at DESC");
    $stmt->execute([$userEmail]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $bookings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Booking History - Shakira Salon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    body { padding-top: 0; }
    .container-main {
      max-width:1200px;margin:-30px auto 30px;padding:30px;
      background:var(--bg-white);border-radius:var(--radius-lg);
      box-shadow:var(--shadow-md);position:relative;z-index:2;
    }
    .container-main h2 {
      color:var(--primary);font-weight:700;margin-bottom:25px;text-align:center;font-size:1.3rem;
    }
    .no-bookings { text-align:center;padding:40px;color:#999;font-size:1.1rem; }
    .no-bookings i { font-size:3rem;margin-bottom:15px;color:#ddd;display:block; }
    .btn-rebook { background:var(--primary);color:#fff;border:none;padding:6px 14px;border-radius:var(--radius-sm);font-size:0.85rem;font-weight:600;transition:var(--transition);text-decoration:none;display:inline-block; }
    .btn-rebook:hover { background:var(--primary-dark);color:#fff;transform:translateY(-2px);box-shadow:0 4px 10px rgba(255,64,129,0.3); }
    .badge-completed { background:#ede7f6;color:#6f42c1;border:1px solid #b39ddb;border-radius:20px;padding:3px 10px;font-size:0.78rem;font-weight:600; }
    @media (max-width:768px) {
      .container-main { padding:20px;margin:-20px 10px 20px; }
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="salon-navbar navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link active" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1><i class="fa-solid fa-clock-rotate-left me-2"></i>My Booking History</h1>
  <p>View and track all your salon appointments</p>
</div>

<!-- ✅ Main Content -->
<div class="container-main">
  <h2><i class="fa-solid fa-clock-rotate-left me-2"></i>My Booking History</h2>

  <?php if (empty($bookings)): ?>
    <div class="no-bookings">
      <i class="fa-solid fa-calendar-xmark"></i>
      <p>You haven't made any bookings yet.</p>
      <a href="book_appointment.php" class="btn-submit" style="display:inline-block;padding:10px 24px;">
        <i class="fa-solid fa-calendar-plus me-1"></i> Book Your First Appointment
      </a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="salon-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Date Booked</th>
            <th>Service</th>
            <th>Schedule</th>
            <th>Stylist</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $index => $booking): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= date('M d, Y', strtotime($booking['created_at'])) ?></td>
              <td><strong><?= htmlspecialchars($booking['service']) ?></strong></td>
              <td><?= htmlspecialchars($booking['schedule']) ?></td>
              <td><?= htmlspecialchars($booking['stylist']) ?></td>
              <td>₱<?= number_format($booking['price'], 2) ?></td>
              <td>
                <?php 
                  $status = strtolower($booking['status']);
                  $badgeClass = 'badge-pending';
                  $icon = 'fa-clock';
                  if ($status === 'approved') { $badgeClass = 'badge-approved'; $icon = 'fa-circle-check'; }
                  elseif ($status === 'cancelled' || $status === 'rejected') { $badgeClass = 'badge-cancelled'; $icon = 'fa-circle-xmark'; }
                  elseif ($status === 'completed') { $badgeClass = 'badge-completed'; $icon = 'fa-star'; }
                ?>
                <span class="badge-status <?= $badgeClass ?>"><i class="fa-solid <?= $icon ?> me-1"></i><?= ucfirst($booking['status']) ?></span>
              </td>
              <td>
                <?php if (in_array(strtolower($booking['status']), ['approved', 'completed'])): ?>
                  <?php
                    $rebookUrl = 'book_appointment.php?' . http_build_query([
                      'rebook'  => 1,
                      'service' => $booking['service'],
                      'price'   => $booking['price'],
                      'phone'   => $booking['phone'],
                      'address' => $booking['address'],
                    ]);
                  ?>
                  <a href="<?= htmlspecialchars($rebookUrl) ?>" class="btn-rebook" title="Rebook this appointment">
                    <i class="fa-solid fa-rotate-right me-1"></i>Rebook
                  </a>
                <?php else: ?>
                  <span class="text-muted" style="font-size:0.82rem;">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <p class="text-muted">
        <i class="fa-solid fa-info-circle me-1"></i> 
        Total Bookings: <strong><?= count($bookings) ?></strong>
      </p>
    </div>
  <?php endif; ?>
</div>

<footer class="salon-footer">
  <div class="container">
    <h5><i class="fa-solid fa-scissors me-2"></i>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot me-1"></i> Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone me-1"></i> +63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope me-1"></i> <a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <hr>
    <p>&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
