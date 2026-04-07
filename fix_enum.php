<?php
$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Connect error: " . $conn->connect_error);
}
$sql = "ALTER TABLE appointments MODIFY COLUMN status ENUM('pending','approved','rejected','completed','cancelled') DEFAULT 'pending'";
$res = $conn->query($sql);
echo $res ? "ALTER TABLE succeeded!" : "Error: " . $conn->error;
$conn->close();
