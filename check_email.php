<?php
header('Content-Type: application/json');
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    try {
        // Check if email exists in database (case-insensitive)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        $exists = $stmt->fetch() ? true : false;
        
        echo json_encode(['exists' => $exists]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
