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
<style>
    body { background: #fdfdfd; font-family: Arial, sans-serif; padding-top: 0; }
    .navbar { background-color: #ff69b4 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .navbar-brand { font-weight: bold; color: #fff !important; }
    .nav-link { color: #fff !important; font-weight: 500; }
    .nav-link:hover, .nav-link.active { text-decoration: underline; }
    .navbar-toggler { border-color: rgba(255,255,255,0.5); }
    .navbar-toggler-icon { filter: invert(1); }
    .page-header {
      background: linear-gradient(135deg, #ff69b4, #ff1493);
      color: white; padding: 50px 0; text-align: center;
      border-bottom-left-radius: 50px; border-bottom-right-radius: 50px;
      margin-top: 56px;
    }
    .page-header h1 { margin: 0; font-size: 2rem; }
    .page-header p { margin: 8px 0 0; opacity: 0.9; }
    .content-section { margin-top: -30px; padding: 0 15px 40px; }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .card {
        background: #fff; border-radius: 12px; padding: 15px;
        text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s; border: none;
    }
    .card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
    .card img { max-height: 180px; max-width: 100%; width: auto; height: auto; margin: 0 auto; display: block; border-radius: 8px; }
    .card h3 { margin: 12px 0 8px; color: #ff4081; font-size: 1.1rem; }
    .card p { font-size: 14px; color: #555; line-height: 1.4; min-height: 50px; }
    .card strong { display: block; margin-top: 10px; font-size: 16px; color: #333; }
    .status { margin-top: 8px; font-weight: bold; padding: 6px 12px; border-radius: 8px; display: inline-block; }
    .booked { background: #ffe0e6; color: #d6005c; }
    .available { background: #e0ffe6; color: #007a3d; }
    footer { background: #ff69b4; color: white; padding: 40px 0; text-align: center; margin-top: 50px; }
    footer a { color: white; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
    footer .social i { font-size: 20px; margin: 0 10px; color: white; transition: 0.3s; }
    footer .social i:hover { color: #ffe4f2; }
    @media (max-width: 576px) {
      .page-header { border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; padding: 35px 15px; }
      .card img { max-height: 140px; }
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
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
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a></li>
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
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <strong>₱<?= number_format($row['price'], 2) ?></strong>
                <?php if (in_array($row['name'], $bookedServices)): ?>
                    <div class="status booked">Booked</div>
                <?php else: ?>
                    <div class="status available">Available</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
  <div class="container">
    <h5>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot"></i> Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone"></i> +63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope"></i> <a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a></p>
    <div class="social mt-3">
      <a href="#"><i class="fa-brands fa-facebook"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
    </div>
    <hr class="my-3" style="border-color: rgba(255,255,255,0.5);">
    <p>&copy; <?= date('Y'); ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>