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
  <style>
    body {
      font-family: Arial, sans-serif; 
      background: #fdf3f7;
      padding-bottom: 50px;
    }
    .navbar {
      background-color: #ff69b4 !important; 
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      color: #fff !important;
      font-weight: bold;
    }
    .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .nav-link:hover {
      text-decoration: underline;
    }
    .navbar-toggler {
      border-color: rgba(255,255,255,0.5);
    }
    .navbar-toggler-icon {
      filter: invert(1);
    }
    .container-main {
      max-width: 1200px;
      margin: 100px auto 30px;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    h2 {
      color: #ff4081;
      font-weight: 700;
      margin-bottom: 25px;
      text-align: center;
    }
    .table-responsive {
      margin-top: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    thead {
      background: linear-gradient(90deg, #ff69b4, #ff99cc);
      color: white;
    }
    thead th {
      padding: 15px;
      text-align: left;
      font-weight: 600;
    }
    tbody tr {
      border-bottom: 1px solid #f0f0f0;
      transition: background 0.2s;
    }
    tbody tr:hover {
      background: #fff5f8;
    }
    tbody td {
      padding: 12px 15px;
      vertical-align: middle;
    }
    .badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    .badge-pending {
      background: #fff3cd;
      color: #856404;
    }
    .badge-approved {
      background: #d4edda;
      color: #155724;
    }
    .badge-cancelled {
      background: #f8d7da;
      color: #721c24;
    }
    .badge-paid {
      background: #d1ecf1;
      color: #0c5460;
    }
    .no-bookings {
      text-align: center;
      padding: 40px;
      color: #999;
      font-size: 1.1rem;
    }
    .no-bookings i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #ddd;
    }
    .btn-sm {
      padding: 6px 12px;
      font-size: 0.875rem;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }
    .btn-primary {
      background: #007bff;
      color: white;
      border: none;
    }
    .btn-primary:hover {
      background: #0056b3;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,123,255,0.3);
    }
    @media (max-width: 768px) {
      .container-main {
        padding: 20px;
        margin: 80px 10px 20px;
      }
      table {
        font-size: 0.9rem;
      }
      thead th, tbody td {
        padding: 10px 8px;
      }
    }
  </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php">Book</a></li>
        <li class="nav-item"><a class="nav-link active" href="booking_history.php">Booking History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php">Business Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Main Content -->
<div class="container-main">
  <h2><i class="fa-solid fa-clock-rotate-left"></i> My Booking History</h2>

  <?php if (empty($bookings)): ?>
    <div class="no-bookings">
      <i class="fa-solid fa-calendar-xmark"></i>
      <p>You haven't made any bookings yet.</p>
      <a href="book_appointment.php" class="btn btn-primary mt-3">
        <i class="fa-solid fa-calendar-plus"></i> Book Your First Appointment
      </a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
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
                  if ($status === 'approved') $badgeClass = 'badge-approved';
                  elseif ($status === 'cancelled') $badgeClass = 'badge-cancelled';
                ?>
                <span class="badge <?= $badgeClass ?>"><?= ucfirst($booking['status']) ?></span>
              </td>
              <td>
                <a href="book_appointment.php" class="btn btn-sm btn-primary" title="Book another appointment">
                  <i class="fa-solid fa-calendar-plus"></i> Rebook
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <p class="text-muted">
        <i class="fa-solid fa-info-circle"></i> 
        Total Bookings: <strong><?= count($bookings) ?></strong>
      </p>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
