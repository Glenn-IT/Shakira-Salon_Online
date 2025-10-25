<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);

// Check user role first
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: manage_users.php?error=UserNotFound");
    exit;
}

$user = $result->fetch_assoc();

if ($user['role'] !== 'customer') {
    $stmt->close();
    $conn->close();
    header("Location: manage_users.php?error=CannotDeleteAdminOrStaff");
    exit;
}

// Proceed to delete customer
$stmt->close();

$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();
$deleteStmt->close();
$conn->close();

header("Location: manage_users.php?success=Deleted");
exit;
?>
