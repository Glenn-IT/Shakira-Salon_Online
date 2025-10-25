<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'customer') {
    header("Location: login.php");
    exit;
}

$selectedService = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';

$stylists = [
    ['name' => 'Alyssa Grace', 'specialization' => 'Haircut, Hair Coloring'],
    ['name' => 'Bea Miranda', 'specialization' => 'Rebonding, Haircut'],
    ['name' => 'Carla Santos', 'specialization' => 'Hair Coloring, Rebonding'],
];

// Filter stylists by selected service
$filteredStylists = array_filter($stylists, function($stylist) use ($selectedService) {
    return stripos($stylist['specialization'], $selectedService) !== false;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Stylists | Shakira Salon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(-45deg, #fff0f5, #ffe6ef, #ffd6e0, #ffedf1);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #333;
            margin: 0;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            max-width: 960px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        h2 {
            color: #d6005c;
            margin-bottom: 25px;
            font-weight: bold;
            border-bottom: 2px solid #ffcce0;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
        }

        th, td {
            padding: 16px;
            text-align: left;
        }

        th {
            background-color: #ffe3ec;
            color: #d6005c;
            font-weight: bold;
            font-size: 1.1rem;
        }

        tr:hover {
            background-color: #fff0f6;
            cursor: pointer;
        }

        .btn-pink {
            background-color: #ff4d88;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-pink:hover {
            background-color: #d6336c;
        }

        .alert {
            background-color: #fff0f5;
            color: #d6005c;
            border: 1px solid #ffcce0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-user-scissors me-2"></i>Stylists for: <?= $selectedService ? htmlspecialchars($selectedService) : 'All Services' ?></h2>

    <?php if (empty($filteredStylists)): ?>
        <div class="alert">No available stylists found for <strong><?= htmlspecialchars($selectedService) ?></strong>.</div>
    <?php else: ?>
        <table class="table table-bordered shadow-sm">
            <thead>
                <tr>
                    <th>Stylist Name</th>
                    <th>Specialization</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filteredStylists as $stylist): ?>
                    <tr>
                        <td><?= htmlspecialchars($stylist['name']) ?></td>
                        <td><?= htmlspecialchars($stylist['specialization']) ?></td>
                        <td>
                            <a href="book_now.php?service=<?= urlencode($selectedService) ?>&stylist=<?= urlencode($stylist['name']) ?>" class="btn btn-sm btn-pink">
                                <i class="fas fa-calendar-check me-1"></i>Select
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
