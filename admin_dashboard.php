<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "shakira_salon");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Total users (customers only)
$resultUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
$totalUsers = ($resultUsers) ? $resultUsers->fetch_assoc()['total'] : 0;

// Pending bookings
$resultPending = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'pending'");
$totalPending = ($resultPending) ? $resultPending->fetch_assoc()['total'] : 0;

// Approved bookings
$resultApproved = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'approved'");
$totalApproved = ($resultApproved) ? $resultApproved->fetch_assoc()['total'] : 0;

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Shakira Salon</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="assets/css/shared.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    * { box-sizing: border-box; }
    body { margin:0; font-family:'Nunito',sans-serif; background:#f0f2f8; color:#333; }
    .section-header { margin-bottom:25px; }
    .section-header h1 { color:var(--primary);margin:0 0 4px;font-size:1.6rem; }
    .section-header p { margin:0;color:#777;font-size:0.9rem; }

    /* ── Summary Cards ── */
    .cards { display:flex;gap:20px;flex-wrap:wrap;margin-bottom:30px; }
    .card { flex:1;min-width:180px;border-radius:var(--radius-lg);padding:24px 20px;text-align:center;color:#fff;box-shadow:var(--shadow-md);transition:var(--transition); }
    .card:hover { transform:translateY(-4px);box-shadow:var(--shadow-lg); }
    .card h3 { font-size:0.85rem;margin-bottom:8px;opacity:.9;text-transform:uppercase;letter-spacing:.5px; }
    .card p { font-size:2.2rem;font-weight:700;margin:0; }
    .card-red { background:linear-gradient(145deg,#ff6b6b,#ff4757); }
    .card-yellow { background:linear-gradient(145deg,#feca57,#f6b93b);color:#333; }
    .card-blue { background:linear-gradient(145deg,#54a0ff,#2e86de); }

    /* ── Chart Section ── */
    .charts { background:#fff;padding:24px;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow-x:auto; }
    .charts-title { font-size:1rem;font-weight:700;color:#333;margin-bottom:16px; }
    .tabs { display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap; }
    .tabs button { padding:7px 18px;border:2px solid var(--primary);border-radius:var(--radius-full);cursor:pointer;background:#fff;color:var(--primary);font-weight:600;font-size:0.85rem;transition:var(--transition); }
    .tabs button.active { background:var(--primary);color:#fff; }
    .tabs button:hover:not(.active) { background:#fff0f5; }
    canvas { max-height:380px;width:100% !important; }

    @media (max-width:576px) {
        .cards { flex-direction:column; }
    }
</style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">
            <i class="fa-solid fa-scissors"></i> Shakira
            <small>Admin Panel</small>
        </div>
        <nav>
            <a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="manage_bookings.php" class="<?= $currentPage === 'manage_bookings.php' ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> Manage Bookings</a>
            <a href="hairstyle.php" class="<?= $currentPage === 'hairstyle.php' ? 'active' : '' ?>"><i class="fa-solid fa-scissors"></i> Hairstyles</a>
            <a href="admin_messages.php" class="<?= $currentPage === 'admin_messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="gallery_admin.php" class="<?= $currentPage === 'gallery_admin.php' ? 'active' : '' ?>"><i class="fa-solid fa-image"></i> Gallery</a>
            <a href="announcement.php" class="<?= $currentPage === 'announcement.php' ? 'active' : '' ?>"><i class="fa-solid fa-clock"></i> Business Hours</a>
            <a href="manage_announcements.php" class="<?= $currentPage === 'manage_announcements.php' ? 'active' : '' ?>"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
            <div class="nav-divider"></div>
            <a href="insert.php" class="<?= $currentPage === 'insert.php' ? 'active' : '' ?>"><i class="fa-solid fa-plus"></i> Add Service</a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-topbar">
        <span class="page-title"><i class="fa-solid fa-gauge-high"></i> Dashboard Overview</span>
        <div class="admin-info">
            <i class="fa-solid fa-user-shield"></i>
            <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></span>
        </div>
    </div>

    <div class="admin-main">
        <div class="section-header">
            <h1>Dashboard</h1>
            <p>Hello, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>! Here's an overview of your salon performance.</p>
        </div>

        <div class="cards">
            <div class="card card-red"><h3>Total Users</h3><p><?= $totalUsers ?></p></div>
            <div class="card card-yellow"><h3>Pending Bookings</h3><p><?= $totalPending ?></p></div>
            <div class="card card-blue"><h3>Approved Bookings</h3><p><?= $totalApproved ?></p></div>
        </div>

        <div class="charts">
            <div class="charts-title"><i class="fa-solid fa-chart-bar"></i> Income Overview</div>
            <div class="tabs">
                <button class="tab-btn active" data-target="weeklyChart">Weekly</button>
                <button class="tab-btn" data-target="monthlyChart">Monthly</button>
                <button class="tab-btn" data-target="quarterlyChart">Quarterly</button>
                <button class="tab-btn" data-target="annualChart">Annual</button>
            </div>
            <canvas id="weeklyChart"></canvas>
            <canvas id="monthlyChart" style="display:none"></canvas>
            <canvas id="quarterlyChart" style="display:none"></canvas>
            <canvas id="annualChart" style="display:none"></canvas>
        </div>
    </div>

<script>
let charts = {};
const monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

function makeChart(ctxId, labels, data, label) {
    if (charts[ctxId]) {
        charts[ctxId].destroy();
    }
    charts[ctxId] = new Chart(document.getElementById(ctxId), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(255,64,129,0.6)',
                borderColor: '#ff4081',
                borderWidth: 2
            }]
        },
        options: { responsive:true, plugins:{ legend:{ display:true } } }
    });
}

async function loadCharts() {
    const response = await fetch("get_chart_data.php");
    const data = await response.json();

    makeChart("weeklyChart", Object.keys(data.weekly), Object.values(data.weekly), "Weekly Income (₱)");
    makeChart("monthlyChart", Object.keys(data.monthly).map(m => monthNames[m-1] ?? m), Object.values(data.monthly), "Monthly Income (₱)");
    makeChart("quarterlyChart", Object.keys(data.quarterly).map(q => "Q" + q), Object.values(data.quarterly), "Quarterly Income (₱)");
    makeChart("annualChart", Object.keys(data.annual), Object.values(data.annual), "Annual Income (₱)");
}

// Auto refresh every 10 seconds
setInterval(loadCharts, 10000);
loadCharts();

// Tab switching
const buttons = document.querySelectorAll(".tab-btn");
const canvases = document.querySelectorAll("canvas");
buttons.forEach(btn => {
    btn.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        canvases.forEach(c => c.style.display="none");
        document.getElementById(btn.dataset.target).style.display="block";
    });
});
</script>
<?php $conn->close(); ?>
</body>
</html>
