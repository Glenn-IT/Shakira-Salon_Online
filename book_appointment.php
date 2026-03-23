<?php
session_start();
require 'config.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Fetch logged-in user's data
$loggedInUser = [];
try {
    $stmt = $pdo->prepare("SELECT full_name, email, phone_number, cp_number, phone, contact_number FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $loggedInUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get the first non-empty phone number from available columns
    $loggedInUser['user_phone'] = $loggedInUser['cp_number'] ?? 
                                   $loggedInUser['contact_number'] ?? 
                                   $loggedInUser['phone_number'] ?? 
                                   $loggedInUser['phone'] ?? '';
} catch (Exception $e) {
    $loggedInUser = [];
}

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
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $paymentProof = $_FILES['payment_proof'] ?? null;

    // ✅ Validate fields
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email.";
    } elseif (!preg_match('/@gmail\.com$/i', $email)) {
        $error = "⚠️ Only Gmail addresses are allowed.";
    } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
        $error = "⚠️ Phone number must start with 09 and be exactly 11 digits.";
    } elseif ($paymentMethod === 'gcash' && (!$paymentProof || $paymentProof['error'] !== UPLOAD_ERR_OK)) {
        $error = "⚠️ Please upload your payment proof for GCash payment.";
    } elseif ($name && $phone && $address && $service && $schedule && $stylist && $paymentMethod) {
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
                // ✅ Handle file upload for GCash payment
                $filename = null;
                if ($paymentMethod === 'gcash' && $paymentProof && $paymentProof['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . "/uploads/payment_proofs/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $ext = pathinfo($paymentProof['name'], PATHINFO_EXTENSION);
                    $filename = uniqid("proof_") . "." . strtolower($ext);
                    $targetPath = $uploadDir . $filename;

                    if (!move_uploaded_file($paymentProof['tmp_name'], $targetPath)) {
                        $error = "❌ Failed to upload payment proof.";
                    }
                }

                if (!$error) {
                    // ✅ Save appointment
                    $paymentStatus = ($paymentMethod === 'cash') ? 'Pending' : 'Pending';
                    $stmt = $pdo->prepare("INSERT INTO appointments 
                        (customer_name, email, phone, address, service, price, schedule, stylist, payment_status, payment_proof) 
                        VALUES (:name, :email, :phone, :address, :service, :price, :schedule, :stylist, :payment_status, :proof)");
                    $stmt->execute([
                        ':name'           => $name,
                        ':email'          => $email,
                        ':phone'          => $phone,
                        ':address'        => $address,
                        ':service'        => $service,
                        ':price'          => $price,
                        ':schedule'       => $schedule,
                        ':stylist'        => $stylist,
                        ':payment_status' => $paymentStatus,
                        ':proof'          => $filename
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
                        $paymentInfo = ($paymentMethod === 'cash') 
                            ? "Payment will be made in cash at the salon." 
                            : "Your payment proof has been uploaded. Please wait for admin verification.";
                        
                        $mail->Body    = "
                            <h2>Hi $name,</h2>
                            <p>Thank you for booking an appointment at <b>Shakira Salon</b>.</p>
                            <p><b>Service:</b> $service<br>
                               <b>Price:</b> ₱$price<br>
                               <b>Schedule:</b> $schedule<br>
                               <b>Stylist:</b> $stylist<br>
                               <b>Payment Method:</b> " . ucfirst($paymentMethod) . "</p>
                            <p>$paymentInfo</p>
                            <br><p>✨ Shakira Salon ✨</p>
                        ";

                        $mail->send();
                    } catch (Exception $e) {
                        $error = "📧 Email not sent: {$mail->ErrorInfo}";
                    }

                    $success = true;
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
  <link rel="stylesheet" href="assets/css/shared.css">
  <style>
    body { padding-top: 0; }
    .container-box {
      max-width:650px;margin:-30px auto 30px;padding:32px;
      background:var(--bg-white);border-radius:var(--radius-lg);
      box-shadow:var(--shadow-md);position:relative;z-index:2;
    }
    .container-box h2 {
      color: var(--primary);
      font-weight: 700;
      font-size: 1.3rem;
      margin-bottom: 20px;
      text-align: center;
    }
    .form-group { margin-bottom: 16px; }
    .form-group label {
      font-weight: 600; display: block; margin-bottom: 6px;
      font-size: 0.9rem; color: var(--text-dark);
    }
    .form-group input, .form-group select {
      width:100%;padding:12px 16px;border-radius:var(--radius-sm);
      border:2px solid #e8e8e8;font-size:0.95rem;
      font-family:var(--font-family);transition:var(--transition);
    }
    .form-group input:focus, .form-group select:focus {
      border-color:var(--primary);outline:none;
      box-shadow:0 0 0 4px rgba(255,64,129,0.12);
    }
    .form-group select {
      appearance:none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23888' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
      background-repeat:no-repeat;background-position:right 12px center;background-size:14px;padding-right:40px;
    }
    .form-group input[readonly] { background:#f5f5f5; cursor:not-allowed; }
    .success { padding:14px 16px; margin-bottom:18px; border-radius:var(--radius-sm); background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; font-weight:500; font-size:0.9rem; }
    .error { padding:14px 16px; margin-bottom:18px; border-radius:var(--radius-sm); background:#ffebee; color:#c62828; border:1px solid #ef9a9a; font-weight:500; font-size:0.9rem; }
    .alert-info { background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; border-radius:var(--radius-sm); padding:18px; margin-top:16px; }
    .alert-info strong { display:block; margin-bottom:8px; font-size:1rem; }
    .qr-box { text-align:center; margin:15px 0; }
    .qr-box img { max-width:180px; border:6px solid #fff; box-shadow:var(--shadow-md); border-radius:var(--radius-md); }
    #gcash-section { display:none; }
    @media (max-width:576px) {
      .container-box { margin:15px 12px 30px; padding:22px 18px; }
    }
  </style>
  <script>
    function updatePrice(){
      const prices={<?php foreach ($services as $s=>$p): ?>"<?= $s ?>":<?= (float)$p ?>,<?php endforeach; ?>};
      const s=document.getElementById("service").value;
      document.getElementById("price").value=prices[s]||0;
    }
    
    function togglePaymentSection() {
      const paymentMethod = document.getElementById("payment_method").value;
      const gcashSection = document.getElementById("gcash-section");
      const proofInput = document.getElementById("payment_proof");
      
      if (paymentMethod === "gcash") {
        gcashSection.style.display = "block";
        proofInput.required = true;
      } else {
        gcashSection.style.display = "none";
        proofInput.required = false;
      }
    }
    
    function validateEmail() {
      const emailInput = document.getElementById("email");
      const email = emailInput.value.trim();
      
      if (!email.toLowerCase().endsWith('@gmail.com')) {
        emailInput.setCustomValidity('Only Gmail addresses are allowed (e.g., yourname@gmail.com)');
      } else {
        emailInput.setCustomValidity('');
      }
    }
    
    function validatePhone() {
      const phoneInput = document.getElementById("phone");
      const phone = phoneInput.value.trim();
      
      if (!phone.startsWith('09') || phone.length !== 11) {
        phoneInput.setCustomValidity('Phone number must start with 09 and be exactly 11 digits (e.g., 09123456789)');
      } else if (!/^[0-9]+$/.test(phone)) {
        phoneInput.setCustomValidity('Phone number must contain only digits');
      } else {
        phoneInput.setCustomValidity('');
      }
    }
  </script>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="salon-navbar navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php"><i class="fa-solid fa-gears"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link active" href="book_appointment.php"><i class="fa-solid fa-calendar-check"></i> Book</a></li>
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
  <h1><i class="fa-solid fa-calendar-check me-2"></i>Book an Appointment</h1>
  <p>Fill in your details and we'll get you booked in</p>
</div>

<!-- ✅ Booking Form -->
<div class="container-box">
  <h2><i class="fa-solid fa-scissors me-2"></i>Book Appointment</h2>

  <?php if ($success): ?>
    <div class="success"><i class="fa-solid fa-circle-check me-1"></i> Appointment saved! Your booking has been confirmed. A confirmation email has been sent to you.</div>
  <?php elseif ($error): ?>
    <div class="error"><i class="fa-solid fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label><i class="fa-solid fa-user me-1"></i> Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($loggedInUser['full_name'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
      <label><i class="fa-solid fa-envelope me-1"></i> Email (Gmail only)</label>
      <input type="email" name="email" id="email" 
             value="<?= htmlspecialchars($loggedInUser['email'] ?? '') ?>"
             placeholder="yourname@gmail.com" 
             oninput="validateEmail()" 
             required>
    </div>
    
    <div class="form-group">
      <label><i class="fa-solid fa-phone me-1"></i> Phone (PH format: 09XXXXXXXXX)</label>
      <input type="text" name="phone" id="phone" 
             value="<?= htmlspecialchars($loggedInUser['user_phone'] ?? '') ?>"
             maxlength="11" 
             placeholder="09123456789" 
             pattern="09[0-9]{9}"
             oninput="validatePhone()" 
             required>
    </div>
    
    <div class="form-group">
      <label><i class="fa-solid fa-location-dot me-1"></i> Address</label>
      <input type="text" name="address" required>
    </div>

    <div class="form-group">
      <label><i class="fa-solid fa-spa me-1"></i> Service</label>
      <select name="service" id="service" onchange="updatePrice()" required>
        <option value="">-- Select a service --</option>
        <?php foreach($services as $s=>$p): ?>
          <option value="<?= $s ?>"><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><i class="fa-solid fa-peso-sign me-1"></i> Price (₱)</label>
      <input type="number" id="price" name="price" readonly required>
    </div>

    <div class="form-group">
      <label><i class="fa-solid fa-calendar-days me-1"></i> Schedule</label>
      <select name="schedule" required>
        <option value="">-- Select a time --</option>
        <?php foreach($schedules as $t): ?>
          <option value="<?= $t ?>"><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><i class="fa-solid fa-user-tie me-1"></i> Stylist</label>
      <select name="stylist" required>
        <option value="">-- Select a stylist --</option>
        <?php foreach($stylists as $st): ?>
          <option value="<?= $st ?>"><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label><i class="fa-solid fa-credit-card me-1"></i> Payment Method</label>
      <select name="payment_method" id="payment_method" onchange="togglePaymentSection()" required>
        <option value="">-- Select payment method --</option>
        <option value="cash"><i class="fa-solid fa-money-bill"></i> Cash</option>
        <option value="gcash">GCash</option>
      </select>
    </div>

    <!-- GCash Section (Hidden by default) -->
    <div id="gcash-section">
      <div class="alert alert-info text-center">
        <strong><i class="fa-solid fa-wallet me-1"></i> GCash Payment Instructions</strong>
        <p class="mb-1 mt-2">Send your payment to:</p>
        <p class="mb-1"><b>GCash Number:</b> <?= $gcashNumber ?></p>
        <p class="mb-2"><b>Account Name:</b> <?= $gcashName ?></p>
        <div class="qr-box">
          <p class="mb-2"><i class="fa-solid fa-qrcode me-1"></i> Scan this official QR Code to pay:</p>
          <img src="<?= $gcashQRPath ?>" alt="GCash QR Code">
        </div>
        <small class="text-muted"><i class="fa-solid fa-triangle-exclamation me-1"></i> After paying, upload your screenshot/receipt below to confirm your booking. Admin will verify your payment.</small>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-upload me-1"></i> Upload Payment Proof (Screenshot)</label>
        <input type="file" name="payment_proof" id="payment_proof" accept="image/*">
      </div>
    </div>

    <button type="submit" class="btn-submit" style="margin-top:10px;"><i class="fa-solid fa-check me-1"></i> Confirm Booking</button>
  </form>
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
