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
    <title>Before and After Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; }
        .navbar { background: linear-gradient(90deg, #ff69b4, #ff99cc); padding:12px 20px; display:flex; justify-content:space-between; align-items:center; }
        .navbar .logo { color:white; font-weight:bold; font-size:20px; display:flex; gap:8px; }
        .navbar a { color:white; text-decoration:none; font-weight:bold; margin-left:20px; }
        .navbar a:hover { color:#333; }
        .gallery { display:flex; flex-wrap:wrap; justify-content:center; gap:30px; padding:20px; }
        .image-pair { background:white; padding:15px; border:1px solid #ccc; border-radius:10px; max-width:320px; text-align:center; box-shadow:2px 2px 10px rgba(0,0,0,0.15); }
        .image-pair img { width:100%; height:250px; object-fit:cover; border-radius:6px; margin-bottom:8px; }
        .service-label { font-weight:bold; color:#007BFF; font-size:18px; margin-bottom:4px; }
        .style-label { font-size:16px; font-style:italic; color:#444; margin-bottom:10px; }
        .label { font-weight:bold; margin:4px 0; }
        .view-btn { background:#ff69b4; border:none; padding:8px 15px; border-radius:6px; color:white; cursor:pointer; font-weight:bold; margin-top:8px; transition:0.3s; }
        .view-btn:hover { background:#ff1493; }

        /* ✅ Modal Styling */
        .modal { display:none; position:fixed; z-index:1000; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; }
        .modal-content { background:white; padding:20px; border-radius:12px; max-width:850px; width:90%; text-align:center; position:relative; box-shadow:0 0 15px rgba(0,0,0,0.3); }
        .modal-content img { width:100%; max-height:420px; object-fit:contain; margin-bottom:10px; border-radius:8px; }
        .close-btn { position:absolute; top:10px; right:15px; font-size:25px; font-weight:bold; color:#333; cursor:pointer; }
        .close-btn:hover { color:red; }
        .modal-service { font-weight:bold; font-size:22px; margin-bottom:5px; color:#ff1493; }
        .modal-style { font-size:18px; font-style:italic; margin-bottom:15px; color:#333; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fa-solid fa-scissors"></i> Shakira Salon</div>
        <div>
            <a href="services.php"><i class="fa-solid fa-gears"></i> Services</a>
            <a href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a>
            <a href="gallery.php"><i class="fa-solid fa-image"></i> Gallery</a>
            <a href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
            <a href="business_hours_client.php"><i class="fa-solid fa-clock"></i> Business Hours</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <h1 style="text-align:center;margin:30px 0;">Before & After Gallery</h1>
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

                    <button class="view-btn" 
                        onclick="openModal('<?php echo htmlspecialchars($row['service_name']); ?>', '<?php echo htmlspecialchars($row['style_name']); ?>', '<?php echo htmlspecialchars($row['before_image']); ?>', '<?php echo htmlspecialchars($row['after_image']); ?>')">
                        View
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;width:100%;">No images uploaded yet.</p>
        <?php endif; ?>
    </div>

    <!-- ✅ Modal -->
    <div id="galleryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div class="modal-service" id="modalService"></div>
            <div class="modal-style" id="modalStyle"></div>
            <img id="modalBefore" src="" alt="Before">
            <div class="label">Before</div>
            <img id="modalAfter" src="" alt="After">
            <div class="label">After</div>
        </div>
    </div>

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
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
