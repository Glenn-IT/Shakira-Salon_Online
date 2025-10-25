<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect booking details from form
    $booking = $_POST;
} else {
    header("Location: book_appointment.php");
    exit;
}

// ✅ If payment is confirmed, insert booking
if (isset($_POST['confirm_payment'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO appointments 
          (customer_name, phone, address, service, price, schedule, stylist) 
          VALUES (:name,:phone,:address,:service,:price,:schedule,:stylist)");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':phone' => $_POST['phone'],
            ':address' => $_POST['address'],
            ':service' => $_POST['service'],
            ':price' => $_POST['price'],
            ':schedule' => $_POST['schedule'],
            ':stylist' => $_POST['stylist']
        ]);
        $success = true;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GCash Payment</title>
  <style>
    body { font-family: Arial, sans-serif; background:#fdf3f7; }
    .container { background:#fff; max-width:500px; margin:50px auto; padding:30px; border-radius:15px; text-align:center; box-shadow:0 6px 15px rgba(0,0,0,0.1); }
    .success { background:#d4edda; padding:15px; border-radius:8px; color:#155724; }
    .error { background:#f8d7da; padding:15px; border-radius:8px; color:#721c24; }
    button { padding:12px 20px; border:none; border-radius:8px; background:#28a745; color:#fff; font-size:1rem; cursor:pointer; }
    button:hover { opacity:.9; }
  </style>
</head>
<body>
  <div class="container">
    <h2>📱 Pay via GCash</h2>
    <?php if (!empty($success)): ?>
      <div class="success">✅ Payment Successful! Booking confirmed.</div>
      <a href="book_appointment.php">← Back to Booking</a>
    <?php elseif (!empty($error)): ?>
      <div class="error">❌ <?= $error ?></div>
    <?php else: ?>
      <p>Scan this QR to pay via GCash:</p>
      <img src="gcash_qr.png" alt="GCash QR" width="200"><br><br>
      <form method="POST">
        <?php foreach ($booking as $k => $v): ?>
          <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
        <?php endforeach; ?>
        <button type="submit" name="confirm_payment">✅ I have paid with GCash</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
