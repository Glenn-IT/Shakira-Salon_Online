<?php
session_start();

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database error: " . $conn->connect_error);
}

$sql = "SELECT id, service_name, style_name, before_image, after_image, created_at 
        FROM gallery ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery - Shakira Salon</title>
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
        .gallery { display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; padding: 30px 20px; }
        .image-pair { background: white; padding: 15px; border: 1px solid #ccc; border-radius: 10px; width: 300px; text-align: center; box-shadow: 2px 2px 10px rgba(0,0,0,0.15); }
        .image-pair img { width: 100%; height: 220px; object-fit: cover; border-radius: 6px; margin-bottom: 8px; }
        .service-label { font-weight: bold; color: #ff1493; font-size: 17px; margin-bottom: 4px; }
        .style-label { font-size: 15px; font-style: italic; color: #444; margin-bottom: 10px; }
        .label { font-weight: bold; margin: 4px 0; color: #555; }
        .view-btn { background: #ff69b4; border: none; padding: 8px 20px; border-radius: 6px; color: white; cursor: pointer; font-weight: bold; margin-top: 8px; transition: 0.3s; }
        .view-btn:hover { background: #ff1493; }
        .modal-overlay { display: none; position: fixed; z-index: 2000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; overflow-y: auto; padding: 20px; box-sizing: border-box; }
        .modal-box { background: white; padding: 20px; border-radius: 12px; max-width: 820px; width: 100%; text-align: center; position: relative; box-shadow: 0 0 15px rgba(0,0,0,0.3); }
        .modal-box img { width: 100%; max-height: 380px; object-fit: contain; margin-bottom: 10px; border-radius: 8px; }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 25px; font-weight: bold; color: #333; cursor: pointer; line-height: 1; }
        .close-btn:hover { color: red; }
        .modal-service { font-weight: bold; font-size: 20px; margin-bottom: 5px; color: #ff1493; }
        .modal-style { font-size: 16px; font-style: italic; margin-bottom: 15px; color: #333; }
        footer { background: #ff69b4; color: white; padding: 40px 0; text-align: center; margin-top: 20px; }
        footer a { color: white; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
        footer .social i { font-size: 20px; margin: 0 10px; color: white; transition: 0.3s; }
        footer .social i:hover { color: #ffe4f2; }
        @media (max-width: 576px) {
          .page-header { border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; padding: 35px 15px; }
          .image-pair { width: 100%; max-width: 340px; }
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
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Booking History</a></li>
        <li class="nav-item"><a class="nav-link active" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h1><i class="fa-solid fa-image me-2"></i>Before &amp; After Gallery</h1>
  <p>See the amazing transformations at Shakira Salon</p>
</div>

<div class="gallery">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="image-pair">
                <div class="service-label"><?php echo htmlspecialchars($row['service_name']); ?></div>
                <div class="style-label"><?php echo htmlspecialchars($row['style_name']); ?></div>
                <img src="<?php echo htmlspecialchars($row['before_image']); ?>" alt="Before">
                <div class="label">Before</div>
                <img src="<?php echo htmlspecialchars($row['after_image']); ?>" alt="After">
                <div class="label">After</div>
                <button class="view-btn" onclick="openModal('<?php echo htmlspecialchars($row['service_name']); ?>','<?php echo htmlspecialchars($row['style_name']); ?>','<?php echo htmlspecialchars($row['before_image']); ?>','<?php echo htmlspecialchars($row['after_image']); ?>')">View</button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;width:100%;padding:40px;color:#999;">No images uploaded yet.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="galleryModal" class="modal-overlay">
    <div class="modal-box">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-service" id="modalService"></div>
        <div class="modal-style" id="modalStyle"></div>
        <img id="modalBefore" src="" alt="Before">
        <div class="label">Before</div>
        <img id="modalAfter" src="" alt="After">
        <div class="label">After</div>
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
<script>
    function openModal(service, style, before, after) {
        document.getElementById("modalService").innerText = service;
        document.getElementById("modalStyle").innerText = style;
        document.getElementById("modalBefore").src = before;
        document.getElementById("modalAfter").src = after;
        document.getElementById("galleryModal").style.display = "flex";
    }
    function closeModal() {
        document.getElementById("galleryModal").style.display = "none";
    }
    window.onclick = function(event) {
        let modal = document.getElementById("galleryModal");
        if (event.target === modal) closeModal();
    }
</script>
</body>
</html>
