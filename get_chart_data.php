<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Weekly (last 7 days based on approved_at)
$weekly = [];
$result = $conn->query("
    SELECT DATE_FORMAT(approved_at, '%a') AS day, COALESCE(SUM(price),0) AS total
    FROM appointments
    WHERE status = 'approved' AND approved_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY day
    ORDER BY FIELD(day, 'Mon','Tue','Wed','Thu','Fri','Sat','Sun')
");
while ($row = $result->fetch_assoc()) {
    $weekly[$row['day']] = (float)$row['total'];
}

// Monthly (this year)
$monthly = [];
$result = $conn->query("
    SELECT MONTH(approved_at) AS month, COALESCE(SUM(price),0) AS total
    FROM appointments
    WHERE status = 'approved' AND YEAR(approved_at) = YEAR(CURDATE())
    GROUP BY month
");
while ($row = $result->fetch_assoc()) {
    $monthly[$row['month']] = (float)$row['total'];
}

// Quarterly (this year)
$quarterly = [];
$result = $conn->query("
    SELECT QUARTER(approved_at) AS quarter, COALESCE(SUM(price),0) AS total
    FROM appointments
    WHERE status = 'approved' AND YEAR(approved_at) = YEAR(CURDATE())
    GROUP BY quarter
");
while ($row = $result->fetch_assoc()) {
    $quarterly[$row['quarter']] = (float)$row['total'];
}

$annual = [];
$result = $conn->query("
    SELECT YEAR(approved_at) AS year, COALESCE(SUM(price),0) AS total
    FROM appointments
    WHERE status = 'approved'
    GROUP BY year
");
while ($row = $result->fetch_assoc()) {
    $annual[$row['year']] = (float)$row['total'];
}

$totalIncome = 0;
$result = $conn->query("
    SELECT COALESCE(SUM(price),0) AS total
    FROM appointments
    WHERE status = 'approved'
");
if ($row = $result->fetch_assoc()) {
    $totalIncome = (float)$row['total'];
}

echo json_encode([
    "weekly" => $weekly,
    "monthly" => $monthly,
    "quarterly" => $quarterly,
    "annual" => $annual,
    "total_income" => $totalIncome 
]);

$conn->close();
