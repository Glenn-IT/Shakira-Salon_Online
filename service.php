<?php
$services = [
    "Haircut" => 1000,
    "Hair Coloring" => 2500,
    "Hair Styling" => 1500,
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Service and Price</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f7fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        width: 320px;
        text-align: center;
    }
    h2 {
        margin-bottom: 25px;
        color: #333;
    }
    label {
        display: block;
        text-align: left;
        margin: 15px 0 8px 0;
        font-weight: 600;
        color: #555;
    }
    select, input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }
    select:focus, input[type="text"]:focus {
        border-color: #007bff;
        outline: none;
    }
    input[readonly] {
        background-color: #f0f0f0;
        color: #555;
    }
    button {
        margin-top: 30px;
        width: 100%;
        padding: 12px;
        font-size: 18px;
        font-weight: 700;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #0056b3;
    }
</style>
<script>
    const services = <?= json_encode($services) ?>;
    function updatePrice() {
        const serviceSelect = document.getElementById('service');
        const priceInput = document.getElementById('price');
        const selected = serviceSelect.value;
        priceInput.value = services[selected] ? "₱ " + services[selected] : '';
    }
</script>
</head>
<body>
    <div class="container">
        <h2>Select Service & Price</h2>
        <form onsubmit="event.preventDefault(); alert('Confirmed!');">
            <label for="service">Service Needed:</label>
            <select id="service" name="service" onchange="updatePrice()" required>
                <option value="">-- Select Service --</option>
                <?php foreach ($services as $service => $price): ?>
                    <option value="<?= htmlspecialchars($service) ?>"><?= htmlspecialchars($service) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" readonly placeholder="₱ 0">

            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>
