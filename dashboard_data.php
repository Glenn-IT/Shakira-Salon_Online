<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$resultUsers = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = ($resultUsers) ? $resultUsers->fetch_assoc()['total'] : 0;

$resultPending = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'pending'");
$totalPending = ($resultPending) ? $resultPending->fetch_assoc()['total'] : 0;

$resultApproved = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'approved'");
$totalApproved = ($resultApproved) ? $resultApproved->fetch_assoc()['total'] : 0;

echo json_encode([
    "totalUsers" => $totalUsers,
    "totalPending" => $totalPending,
    "totalApproved" => $totalApproved
]);

$conn->close();
