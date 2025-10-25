<?php
header('Content-Type: application/json');
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fullname'])) {
    $fullname = trim($_POST['fullname']);
    
    try {
        // Check if full name exists in database (case-insensitive)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(full_name) = LOWER(?)");
        $stmt->execute([$fullname]);
        $exists = $stmt->fetch() ? true : false;
        
        echo json_encode(['exists' => $exists]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
