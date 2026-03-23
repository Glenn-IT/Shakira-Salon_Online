<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch booked services (from booking table)
$bookedServices = [];
$bookingResult = $conn->query("SELECT service FROM bookings");
if ($bookingResult) {
    while ($b = $bookingResult->fetch_assoc()) {
        $bookedServices[] = $b['service'];
    }
}

// Fetch all services
$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Our Services - Shakira Salon</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/shared.css">
<style>
    body { padding-top: 0; }
    .content-section { margin-top: -30px; padding: 0 15px 40px; position: relative; z-index: 2; }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 22px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 0;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border: none;
        overflow: hidden;
    }
    .card:hover { transform: translateY(-6px); box-shadow: var(--shadow-pink); }
    .card .card-img-wrap {
        overflow: hidden;
        height: 200px;
    }
    .card img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.4s ease; }
    .card:hover img { transform: scale(1.08); }
    .card .card-content { padding: 18px 16px 20px; }
    .card h3 { margin: 0 0 8px; color: var(--primary); font-size: 1.1rem; font-weight: 700; }
    .card p { font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; min-height: 48px; margin: 0 0 12px; }
    .card .price { font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
    .status { font-weight: 600; padding: 5px 16px; border-radius: var(--radius-pill); display: inline-block; font-size: 0.8rem; }
    .booked { background: #ffebee; color: #c62828; }
    .available { background: #e8f5e9; color: #2e7d32; }
    @media (max-width: 576px) {
      .card img { height: 160px; }
      .card .card-img-wrap { height: 160px; }
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top salon-navbar">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1><i class="fa-solid fa-gears me-2"></i>Our Beauty Services</h1>
  <p>Explore what Shakira Salon has to offer</p>
</div>

<div class="content-section">
    <div class="grid">
        <?php while ($row = $services->fetch_assoc()): ?>
            <div class="card">
                <div class="card-img-wrap">
                  <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                </div>
                <div class="card-content">
                  <h3><?= htmlspecialchars($row['name']) ?></h3>
                  <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                  <div class="price">₱<?= number_format($row['price'], 2) ?></div>
                  <?php if (in_array($row['name'], $bookedServices)): ?>
                      <div class="status booked"><i class="fa-solid fa-circle-check me-1"></i>Booked</div>
                  <?php else: ?>
                      <div class="status available"><i class="fa-solid fa-circle-check me-1"></i>Available</div>
                  <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="salon-footer">
  <div class="container">
    <h5><i class="fa-solid fa-scissors me-2"></i>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot me-2"></i>Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone me-2"></i>+63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope me-2"></i><a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <div class="social">
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
    </div>
    <hr>
    <p class="copyright">&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>