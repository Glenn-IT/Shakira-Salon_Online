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
    <link rel="stylesheet" href="assets/css/shared.css">
    <style>
        body { padding-top: 0; }
        .gallery { display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;padding:30px 20px;max-width:1200px;margin:auto; }
        .image-pair {
          background:var(--bg-white);border-radius:var(--radius-lg);overflow:hidden;
          box-shadow:var(--shadow-sm);transition:var(--transition);
        }
        .image-pair:hover { transform:translateY(-6px);box-shadow:var(--shadow-lg); }
        .image-pair .pair-header { padding:16px 18px 8px;text-align:center; }
        .service-label { font-weight:700;color:var(--primary);font-size:1.05rem;margin-bottom:2px; }
        .style-label { font-size:0.9rem;font-style:italic;color:#666;margin-bottom:8px; }
        .pair-images { display:grid;grid-template-columns:1fr 1fr;gap:0; }
        .img-col { position:relative;overflow:hidden; }
        .img-col img { width:100%;height:200px;object-fit:cover;transition:transform 0.4s; }
        .img-col:hover img { transform:scale(1.08); }
        .img-label { position:absolute;bottom:0;left:0;right:0;padding:4px 0;
          background:linear-gradient(transparent,rgba(0,0,0,0.6));color:#fff;font-size:0.78rem;
          font-weight:600;text-align:center;text-transform:uppercase;letter-spacing:1px; }
        .pair-footer { padding:12px 18px;text-align:center; }
        .view-btn {
          background:var(--gradient);color:#fff;border:none;padding:8px 24px;
          border-radius:var(--radius-full);font-weight:600;font-size:0.88rem;
          cursor:pointer;transition:var(--transition);
        }
        .view-btn:hover { transform:translateY(-2px);box-shadow:0 6px 16px rgba(255,64,129,0.35); }
        .empty-gallery { text-align:center;padding:60px 20px;color:#999; }
        .empty-gallery i { font-size:3rem;color:#ddd;display:block;margin-bottom:14px; }

        /* Modal */
        .modal-overlay { display:none;position:fixed;z-index:2000;top:0;left:0;width:100%;height:100%;
          background:rgba(0,0,0,0.85);justify-content:center;align-items:center;
          overflow-y:auto;padding:20px;box-sizing:border-box;backdrop-filter:blur(4px); }
        .modal-box {
          background:var(--bg-white);padding:24px;border-radius:var(--radius-lg);
          max-width:820px;width:100%;text-align:center;position:relative;
          box-shadow:0 20px 60px rgba(0,0,0,0.4);animation:fadeInUp 0.3s ease;
        }
        .modal-box img { width:100%;max-height:380px;object-fit:contain;margin-bottom:8px;border-radius:var(--radius-md); }
        .close-btn { position:absolute;top:12px;right:16px;font-size:28px;font-weight:bold;color:#888;cursor:pointer;line-height:1;transition:var(--transition);width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%; }
        .close-btn:hover { color:var(--primary);background:#fce4ec; }
        .modal-service { font-weight:700;font-size:1.2rem;color:var(--primary);margin-bottom:4px; }
        .modal-style { font-size:0.95rem;font-style:italic;color:#666;margin-bottom:18px; }
        .modal-label { font-weight:600;color:#555;font-size:0.85rem;text-transform:uppercase;letter-spacing:1px;margin:6px 0 4px; }
        @media (max-width:576px) {
          .gallery { grid-template-columns:1fr;padding:20px 12px; }
          .img-col img { height:150px; }
        }
    </style>
</head>
<body>

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
        <li class="nav-item"><a class="nav-link" href="booking_history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li class="nav-item"><a class="nav-link active" href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Hours</a></li>
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
                <div class="pair-header">
                  <div class="service-label"><?php echo htmlspecialchars($row['service_name']); ?></div>
                  <div class="style-label"><?php echo htmlspecialchars($row['style_name']); ?></div>
                </div>
                <div class="pair-images">
                  <div class="img-col">
                    <img src="<?php echo htmlspecialchars($row['before_image']); ?>" alt="Before">
                    <div class="img-label">Before</div>
                  </div>
                  <div class="img-col">
                    <img src="<?php echo htmlspecialchars($row['after_image']); ?>" alt="After">
                    <div class="img-label">After</div>
                  </div>
                </div>
                <div class="pair-footer">
                  <button class="view-btn" onclick="openModal('<?php echo htmlspecialchars($row['service_name']); ?>','<?php echo htmlspecialchars($row['style_name']); ?>','<?php echo htmlspecialchars($row['before_image']); ?>','<?php echo htmlspecialchars($row['after_image']); ?>')">
                    <i class="fa-solid fa-expand me-1"></i>View Full
                  </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-gallery" style="grid-column:1/-1;">
          <i class="fa-solid fa-camera-retro"></i>
          <p>No images uploaded yet. Check back soon!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="galleryModal" class="modal-overlay">
    <div class="modal-box">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-service" id="modalService"></div>
        <div class="modal-style" id="modalStyle"></div>
        <div class="modal-label">Before</div>
        <img id="modalBefore" src="" alt="Before">
        <div class="modal-label">After</div>
        <img id="modalAfter" src="" alt="After">
    </div>
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
