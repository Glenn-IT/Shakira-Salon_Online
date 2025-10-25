<?php
require 'config.php';

// ✅ Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// ✅ Auto-seed hairstylists table if empty
try {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM hairstylists");
    $count = $countStmt->fetchColumn();

    if ($count == 0) {
        $seedData = [
            ['Anna Lopez', '09171234567', 'anna@example.com', 'Senior Stylist'],
            ['Mark Reyes', '09181234567', 'mark@example.com', 'Junior Stylist'],
            ['Jane Cruz', '09191234567', 'jane@example.com', 'Manager'],
            ['Carlos Tan', '09201234567', 'carlos@example.com', 'Administrator']
        ];
        $insertStmt = $pdo->prepare("INSERT INTO hairstylists (full_name, phone_number, email, role) VALUES (?, ?, ?, ?)");
        foreach ($seedData as $stylist) {
            $insertStmt->execute($stylist);
        }
    }
} catch (Exception $e) {}

// ✅ Fetch services dynamically
$services = [];
try {
    $stmt = $pdo->query("SELECT id, name, price FROM services ORDER BY name ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $services[$row['name']] = $row['price'];
    }
} catch (Exception $e) {
    $services = [];
}

$schedules = ["08:00 AM", "09:00 AM", "10:00 AM", "11:00 AM", "01:00 PM", "02:00 PM", "03:00 PM", "04:00 PM",  "05:00 PM",  "06:00 PM",];

$stylists = [];
try {
    $stmt = $pdo->query("SELECT full_name FROM hairstylists ORDER BY full_name");
    $stylists = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $stylists = ["No stylists available"];
}

$success = false;
$error = '';
$gcashNumber = "09623224038";  
$gcashName   = "Mary Grace Acojedo";
$gcashQRPath = "gcash.jpg"; // ✅ Use your gcash.jpg file in root folder

// ✅ Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $service  = trim($_POST['service'] ?? '');
    $price    = isset($_POST['price']) ? (float) $_POST['price'] : 0;
    $schedule = trim($_POST['schedule'] ?? '');
    $stylist  = trim($_POST['stylist'] ?? '');
    $paymentProof = $_FILES['payment_proof'] ?? null;

    // ✅ Validate fields
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email.";
    } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
        $error = "⚠️ Phone number must be exactly 11 digits.";
    } elseif (!$paymentProof || $paymentProof['error'] !== UPLOAD_ERR_OK) {
        $error = "⚠️ Please upload your payment proof.";
    } elseif ($name && $phone && $address && $service && $schedule && $stylist) {
        try {
            // ✅ Check duplicates (name OR email OR phone)
            $checkStmt = $pdo->prepare("SELECT * FROM appointments WHERE customer_name = :name OR email = :email OR phone = :phone LIMIT 1");
            $checkStmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);

            if ($checkStmt->fetch()) {
                $error = "⚠️ Duplicate booking found! Name, Email, or Phone number already used.";
            } else {
                // ✅ Upload payment proof
                $uploadDir = __DIR__ . "/uploads/payment_proofs/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($paymentProof['name'], PATHINFO_EXTENSION);
                $filename = uniqid("proof_") . "." . strtolower($ext);
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($paymentProof['tmp_name'], $targetPath)) {
                    // ✅ Save appointment with proof
                    $stmt = $pdo->prepare("INSERT INTO appointments 
                        (customer_name, email, phone, address, service, price, schedule, stylist, payment_status, payment_proof) 
                        VALUES (:name, :email, :phone, :address, :service, :price, :schedule, :stylist, 'Pending', :proof)");
                    $stmt->execute([
                        ':name'     => $name,
                        ':email'    => $email,
                        ':phone'    => $phone,
                        ':address'  => $address,
                        ':service'  => $service,
                        ':price'    => $price,
                        ':schedule' => $schedule,
                        ':stylist'  => $stylist,
                        ':proof'    => $filename
                    ]);

                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'ityourboiaki@gmail.com';       
                        $mail->Password   = 'fojt zvoj imdr xwnw';     
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;

                        $mail->setFrom('nicoleacojedo03@gmail.com', 'Shakira Salon');
                        $mail->addAddress($email, $name);

                        $mail->isHTML(true);
                        $mail->Subject = "Your Appointment is Pending - Shakira Salon";
                        $mail->Body    = "
                            <h2>Hi $name,</h2>
                            <p>Thank you for booking an appointment at <b>Shakira Salon</b>.</p>
                            <p><b>Service:</b> $service<br>
                               <b>Price:</b> ₱$price<br>
                               <b>Schedule:</b> $schedule<br>
                               <b>Stylist:</b> $stylist</p>
                            <p>Your payment proof has been uploaded. Please wait for admin confirmation.</p>
                            <br><p>✨ Shakira Salon ✨</p>
                        ";

                        $mail->send();
                    } catch (Exception $e) {
                        $error = "📧 Email not sent: {$mail->ErrorInfo}";
                    }

                    $success = true;
                } else {
                    $error = "❌ Failed to upload payment proof.";
                }
            }
        } catch (PDOException $e) {
            $error = "❌ Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Book Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    body {font-family: Arial, sans-serif; background:#fdf3f7;}
    .container-box {max-width:650px;margin:80px auto;padding:25px;background:#fff;border-radius:12px;}
    label{font-weight:bold;display:block;margin-top:12px;}
    input,select{width:100%;padding:10px;margin-top:6px;border-radius:8px;border:1px solid #ccc;}
    button{margin-top:20px;padding:12px;width:100%;background:#ff4da6;color:white;border:none;border-radius:8px;cursor:pointer;}
    .success,.error{padding:10px;margin-bottom:15px;border-radius:8px;text-align:center;}
    .success{background:#d4edda;color:#155724;}
    .error{background:#f8d7da;color:#721c24;}
    .alert-info {background:#e7f3fe;color:#0c5460;border:1px solid #bee5eb;}
    .navbar {background-color: #ff69b4 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
    .navbar-brand {color:#fff !important;font-weight:bold;}
    .nav-link {color:#fff !important;font-weight:500;}
    .nav-link:hover {text-decoration:underline;}
    .qr-box {text-align:center;margin:15px 0;}
    .qr-box img {max-width:220px;border:8px solid #fff;box-shadow:0 4px 8px rgba(0,0,0,0.2);border-radius:12px;}
  </style>
  <script>
    function updatePrice(){
      const prices={<?php foreach ($services as $s=>$p): ?>"<?= $s ?>":<?= (float)$p ?>,<?php endforeach; ?>};
      const s=document.getElementById("service").value;
      document.getElementById("price").value=prices[s]||0;
    }
  </script>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="book_appointment.php">Book</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="business_hours_client.php">Business Hours</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ Booking Form -->
<div class="container-box">
  <h2>💇 Book Appointment</h2>

  <div class="alert alert-info text-center">
    <strong>💰 GCash Payment Instructions</strong><br>
    Send your payment to:<br>
    <b>GCash Number:</b> <?= $gcashNumber ?><br>
    <b>Account Name:</b> <?= $gcashName ?><br>
    <div class="qr-box">
      <p>📷 Scan this official QR Code to pay:</p>
      <img src="<?= $gcashQRPath ?>" alt="GCash QR Code">
    </div>
    <small>⚠️ After paying, upload your screenshot/receipt below to confirm your booking. Admin will verify your payment.</small>
  </div>

  <?php if ($success): ?>
    <div class="success">✅ Appointment saved! Your payment proof has been uploaded. Please wait for admin confirmation. A confirmation email has been sent to you.</div>
  <?php elseif ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Name</label>
    <input type="text" name="name" required>
    
    <label>Email</label>
    <input type="email" name="email" required>
    
    <label>Phone</label>
    <input type="text" name="phone" maxlength="11" required>
    
    <label>Address</label>
    <input type="text" name="address" required>

    <label>Service</label>
    <select name="service" id="service" onchange="updatePrice()" required>
      <option value="">--Select--</option>
      <?php foreach($services as $s=>$p): ?>
        <option value="<?= $s ?>"><?= $s ?></option>
      <?php endforeach; ?>
    </select>

    <label>Price (₱)</label>
    <input type="number" id="price" name="price" readonly required>

    <label>Schedule</label>
    <select name="schedule" required>
      <option value="">--Select--</option>
      <?php foreach($schedules as $t): ?>
        <option value="<?= $t ?>"><?= $t ?></option>
      <?php endforeach; ?>
    </select>

    <label>Stylist</label>
    <select name="stylist" required>
      <option value="">--Select--</option>
      <?php foreach($stylists as $st): ?>
        <option value="<?= $st ?>"><?= $st ?></option>
      <?php endforeach; ?>
    </select>

    <label>Upload Payment Proof (Screenshot)</label>
    <input type="file" name="payment_proof" accept="image/*" required>

    <button type="submit">Confirm Booking</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
