<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch booked services (from booking table)
$bookedServices = [];
$bookingResult = $conn->query("SELECT service_id FROM bookings");
if ($bookingResult) {
    while ($b = $bookingResult->fetch_assoc()) {
        $bookedServices[] = $b['service_id'];
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

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #fdf3f7;
        margin: 0;
    }

    /* --- Navbar --- */
    .navbar {
        background: linear-gradient(90deg, #ff69b4, #ff99cc);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .navbar .logo {
        color: white;
        font-size: 20px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .navbar .menu {
        display: flex;
        gap: 25px;
    }
    .navbar a {
        color: white;
        text-decoration: none;
        font-size: 15px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: 0.3s;
    }
    .navbar a:hover {
        color: #333;
    }

    .container {
        max-width: 1000px;
        margin: auto;
        padding: 30px 15px;
    }
    h1 {
        text-align: center;
        color: #d6005c;
        font-weight: 700;
        margin-bottom: 30px;
        font-size: 2rem;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .card img {
        max-height: 180px;
        max-width: 100%;
        width: auto;
        height: auto;
        margin: 0 auto;
        display: block;
        border-radius: 8px;
    }
    .card h3 {
        margin: 12px 0 8px;
        color: #ff4081;
        font-size: 1.1rem;
    }
    .card p {
        font-size: 14px;
        color: #555;
        line-height: 1.4;
        min-height: 50px;
    }
    .card strong {
        display: block;
        margin-top: 10px;
        font-size: 16px;
        color: #333;
    }
    .status {
        margin-top: 8px;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }
    .booked {
        background: #ffe0e6;
        color: #d6005c;
    }
    .available {
        background: #e0ffe6;
        color: #007a3d;
    }
    @media (max-width: 768px) {
        .container {
            max-width: 100%;
            padding: 20px 10px;
        }
        .card img {
            max-height: 140px;
        }
    }
</style>
</head>
<body>

<!-- ✅ Navbar -->
<div class="navbar">
    <div class="logo">
        <i class="fa-solid fa-scissors"></i> Shakira Salon
    </div>
    <div class="menu">
        <a href="services.php"><i class="fa-solid fa-gears"></i> Services</a>
        <a href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a>
        <a href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a>
        <a href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a>
        <a href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h1>Explore Our Beauty Services</h1>
    <div class="grid">
        <?php while ($row = $services->fetch_assoc()): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <strong>₱<?= number_format($row['price'], 2) ?></strong>

                <?php if (in_array($row['id'], $bookedServices)): ?>
                    <div class="status booked">Booked</div>
                <?php else: ?>
                    <div class="status available">Available</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>