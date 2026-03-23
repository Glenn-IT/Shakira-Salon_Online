<?php 
session_start();

// Database connection
$host = 'localhost';
$db = 'shakira_salon';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch active announcements
    $announcements = $pdo->query("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch active promos
    $today = date('Y-m-d');
    $promos = $pdo->query("SELECT * FROM promos WHERE status='active' AND valid_from <= '$today' AND valid_until >= '$today' ORDER BY created_at DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch services
    $services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $announcements = [];
    $promos = [];
    $services = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Online Appointment Management System of Hairstyle at Shakira Salon Located at Tuao West Cagayan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- AOS (Animate On Scroll) CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

  <!-- Favicon -->
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon" />

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fff;
      scroll-behavior: smooth;
    }

    .navbar {
      transition: all 0.3s ease-in-out;
    }

    .navbar-brand {
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .nav-link {
      font-weight: 500;
      transition: color 0.3s ease;
    }
    .nav-link:hover,
    .nav-link.active {
      color: #e91e63 !important;
      font-weight: 700;
    }

    .hero {
      background: url('assets/images/salon-banner.webp') no-repeat center center/cover;
      height: 100vh;
      position: relative;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 0 1rem;
    }
    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.6);
      z-index: 0;
    }
    .hero .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
    }
    .hero h1 {
      font-size: clamp(2.5rem, 5vw, 3.5rem);
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 8px rgba(0,0,0,0.6);
    }
    .hero p.lead {
      font-size: 1.2rem;
      margin-bottom: 1.5rem;
      text-shadow: 0 2px 6px rgba(0,0,0,0.5);
    }
    .btn-primary {
      background-color: #e91e63;
      border: none;
      box-shadow: 0 4px 15px rgba(233,30,99,0.5);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-primary:hover,
    .btn-primary:focus {
      background-color: #c2185b;
      box-shadow: 0 6px 20px rgba(194,24,91,0.7);
    }

    .scroll-down {
      margin-top: 2rem;
      color: #f8bbd0;
      cursor: pointer;
      font-size: 2rem;
      text-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }

    .section-title {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 40px;
      letter-spacing: 1.2px;
      color: #333;
    }

    .card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 0 15px rgba(0,0,0,0.08);
      background-color: #fff;
    }
    .card:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 25px rgba(233,30,99,0.3);
    }
    .card img {
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    .card:hover img {
      transform: scale(1.1);
    }
    .card-body {
      padding: 1.5rem 1.25rem;
    }
    .card-title {
      font-weight: 700;
      color: #e91e63;
      margin-bottom: 10px;
    }
    .card-text {
      color: #555;
      font-size: 1rem;
    }

    .cta-section {
      background-color: #e91e63;
      color: white;
      padding: 4rem 1rem;
      text-align: center;
      transition: background-color 0.3s ease;
    }
    .cta-section h2 {
      font-size: clamp(2rem, 4vw, 2.8rem);
      font-weight: 700;
      margin-bottom: 1rem;
    }
    .cta-section p.lead {
      font-size: 1.3rem;
      margin-bottom: 2rem;
    }
    .btn-outline-light {
      border-color: #fff;
      color: #fff;
      font-weight: 600;
      padding: 0.75rem 2rem;
      transition: background-color 0.3s ease, color 0.3s ease;
      box-shadow: 0 3px 10px rgba(255, 255, 255, 0.3);
      animation: glowPulse 2s infinite;
    }
    .btn-outline-light:hover,
    .btn-outline-light:focus {
      background-color: #fff;
      color: #e91e63;
      box-shadow: 0 6px 15px rgba(255, 255, 255, 0.5);
    }

    @keyframes glowPulse {
      0% { box-shadow: 0 0 10px rgba(255,255,255,0.3); }
      50% { box-shadow: 0 0 20px rgba(255,255,255,0.6); }
      100% { box-shadow: 0 0 10px rgba(255,255,255,0.3); }
    }

    footer {
      background-color: #343a40;
      color: white;
      padding: 20px 0;
    }
    footer p {
      margin-bottom: 8px;
      font-size: 0.9rem;
    }
    .social-icons a {
      color: white;
      margin: 0 12px;
      font-size: 1.4rem;
      transition: color 0.3s ease;
    }
    .social-icons a:hover,
    .social-icons a:focus {
      color: #e91e63;
    }

    /* Announcements & Promos Styles */
    .announcement-bar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px 0;
      overflow: hidden;
    }
    .announcement-content {
      display: flex;
      animation: scroll 20s linear infinite;
      white-space: nowrap;
    }
    .announcement-item {
      padding: 0 50px;
      display: inline-flex;
      align-items: center;
    }
    .announcement-item i {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    @keyframes scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    
    .promo-card {
      position: relative;
      overflow: hidden;
      border-radius: 15px;
      transition: all 0.3s ease;
    }
    .promo-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(233,30,99,0.4);
    }
    .promo-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #e91e63;
      color: white;
      padding: 8px 15px;
      border-radius: 25px;
      font-weight: bold;
      z-index: 1;
      box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    }
    .promo-card img {
      height: 200px;
      object-fit: cover;
      width: 100%;
    }
    .promo-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
      color: white;
      padding: 20px;
      transform: translateY(100%);
      transition: transform 0.3s ease;
    }
    .promo-card:hover .promo-overlay {
      transform: translateY(0);
    }

    /* Services Section Styles */
    .services-section {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .service-card {
      border: none;
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.4s ease;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      background: white;
      height: 100%;
    }
    .service-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 15px 40px rgba(233,30,99,0.3);
    }
    .service-card img {
      height: 250px;
      object-fit: cover;
      width: 100%;
      transition: transform 0.5s ease;
    }
    .service-card:hover img {
      transform: scale(1.15);
    }
    .service-card .card-body {
      padding: 1.5rem;
    }
    .service-price {
      font-size: 1.8rem;
      font-weight: 700;
      color: #e91e63;
      margin: 10px 0;
    }
    .service-description {
      color: #666;
      font-size: 0.95rem;
      min-height: 60px;
    }
    .btn-book-service {
      background: linear-gradient(135deg, #e91e63 0%, #ff4081 100%);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(233,30,99,0.3);
    }
    .btn-book-service:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(233,30,99,0.5);
      background: linear-gradient(135deg, #c2185b 0%, #e91e63 100%);
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2rem;
      }
      .hero p.lead {
        font-size: 1rem;
      }
      .card img {
        height: 190px;
      }
      .announcement-item {
        padding: 0 25px;
      }
      .section-title {
        font-size: 1.6rem;
      }
      .cta-section h2 {
        font-size: 1.6rem;
      }
      .cta-section p.lead {
        font-size: 1rem;
      }
    }
    @media (max-width: 480px) {
      .hero {
        height: auto;
        min-height: 100vh;
        padding: 80px 1rem 40px;
      }
      .service-price {
        font-size: 1.4rem;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm" aria-label="Primary Navigation">
  <div class="container">
    <a class="navbar-brand" href="#" aria-label="Shakira Salon Home">Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Sign Up</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="forgot_password.php"><i class="fas fa-key"></i> Forgot Password</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<?php if (!empty($announcements)): ?>
<!-- Announcements Bar -->
<div class="announcement-bar">
  <div class="announcement-content">
    <?php 
    // Duplicate announcements for seamless loop
    $duplicated_announcements = array_merge($announcements, $announcements);
    foreach ($duplicated_announcements as $announcement): 
    ?>
      <div class="announcement-item">
        <i class="fas fa-bullhorn"></i>
        <strong><?= htmlspecialchars($announcement['title']) ?>:</strong>
        <?= htmlspecialchars($announcement['content']) ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<section class="hero">
  <div class="hero-content animate__animated animate__fadeInUp" data-aos="zoom-in">
    <h1>Transform Your Look</h1>
    <p class="lead">Get the perfect style that suits your personality.</p>
    <a href="register.php" class="btn btn-primary btn-lg mt-4" role="button">Book Your Appointment</a>
    <div class="scroll-down animate__animated animate__bounce animate__infinite">
      <i class="fas fa-angle-down"></i>
    </div>
  </div>
</section>

<?php if (!empty($services)): ?>
<!-- Services Section -->
<section class="py-5 services-section" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center section-title" data-aos="zoom-in">
      <i class="fas fa-cut"></i> Our Services
    </h2>
    <p class="text-center text-muted mb-5" data-aos="fade-in">
      Discover our wide range of professional salon services tailored just for you
    </p>
    <div class="row g-4">
      <?php foreach ($services as $index => $service): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($index * 100) ?>">
          <div class="service-card card h-100">
            <?php if ($service['image'] && file_exists($service['image'])): ?>
              <img src="<?= htmlspecialchars($service['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($service['name']) ?>">
            <?php else: ?>
              <div style="height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-scissors" style="font-size: 5rem; color: white; opacity: 0.5;"></i>
              </div>
            <?php endif; ?>
            
            <div class="card-body text-center d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($service['name']) ?></h5>
              <p class="service-description flex-grow-1"><?= htmlspecialchars($service['description']) ?></p>
              <div class="service-price">₱<?= number_format($service['price'], 2) ?></div>
              <a href="register.php" class="btn btn-book-service mt-3">
                <i class="fas fa-calendar-check"></i> Book Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="py-5 bg-light" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center section-title" data-aos="zoom-in">Our Signature Styles</h2>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-right" data-aos-delay="100">
        <div class="card">
          <img src="assets/images/fade.webp" class="card-img-top" alt="Fade Haircut" />
          <div class="card-body text-center">
            <h5 class="card-title">Fade Cut</h5>
            <p class="card-text">Clean fades that give you a modern, sharp look.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card">
          <img src="assets/images/layered.webp" class="card-img-top" alt="Layered Haircut" />
          <div class="card-body text-center">
            <h5 class="card-title">Layered Cut</h5>
            <p class="card-text">Add volume and dynamic flow with our professional layers.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-left" data-aos-delay="300">
        <div class="card">
          <img src="images/after/sample2.jpg" class="card-img-top" alt="Bob Haircut" />
          <div class="card-body text-center">
            <h5 class="card-title">Classic Bob</h5>
            <p class="card-text">A timeless style for elegant and confident individuals.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($promos)): ?>
<!-- Promos Section -->
<section class="py-5" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center section-title" data-aos="zoom-in">
      <i class="fas fa-tags"></i> Special Promos & Offers
    </h2>
    <div class="row g-4">
      <?php foreach ($promos as $promo): ?>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="promo-card card h-100">
            <?php if ($promo['discount_percentage']): ?>
              <div class="promo-badge"><?= $promo['discount_percentage'] ?>% OFF</div>
            <?php elseif ($promo['discount_amount']): ?>
              <div class="promo-badge">₱<?= number_format($promo['discount_amount'], 0) ?> OFF</div>
            <?php endif; ?>
            
            <?php if ($promo['image']): ?>
              <img src="<?= htmlspecialchars($promo['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($promo['title']) ?>">
            <?php else: ?>
              <div style="height: 200px; background: linear-gradient(135deg, #e91e63 0%, #ff4081 100%); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-tag" style="font-size: 4rem; color: white; opacity: 0.5;"></i>
              </div>
            <?php endif; ?>
            
            <div class="card-body">
              <h5 class="card-title text-center"><?= htmlspecialchars($promo['title']) ?></h5>
              <p class="card-text text-center"><?= htmlspecialchars($promo['description']) ?></p>
              <?php if ($promo['promo_code']): ?>
                <div class="text-center mt-2">
                  <small class="text-muted">Use code:</small>
                  <div class="badge bg-dark" style="font-size: 1rem; padding: 8px 15px;">
                    <?= htmlspecialchars($promo['promo_code']) ?>
                  </div>
                </div>
              <?php endif; ?>
              <div class="text-center mt-3">
                <small class="text-muted">
                  Valid until: <strong><?= date('M d, Y', strtotime($promo['valid_until'])) ?></strong>
                </small>
              </div>
            </div>
            
            <div class="promo-overlay">
              <p class="mb-1"><i class="fas fa-calendar"></i> <?= date('M d', strtotime($promo['valid_from'])) ?> - <?= date('M d, Y', strtotime($promo['valid_until'])) ?></p>
              <a href="register.php" class="btn btn-light btn-sm mt-2">
                <i class="fas fa-gift"></i> Avail Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-section" data-aos="zoom-in">
  <div class="container">
    <h2 data-aos="fade-up">Ready to Shine?</h2>
    <p class="lead" data-aos="fade-in">Book your appointment with Shakira Salon today!</p>
    <a href="register.php" class="btn btn-outline-light btn-lg" role="button">Get Started</a>
  </div>
</section>

<footer class="text-center">
  <div class="container">
    <p class="mb-2">&copy; <?= date("Y") ?> Shakira Salon | Tuao West, Cagayan</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
  </div>
</footer>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
  AOS.init({
    duration: 1200,
    once: true
  });

  // Navbar scroll effect
  window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 20) {
      navbar.classList.add('shadow-lg', 'bg-dark');
    } else {
      navbar.classList.remove('shadow-lg', 'bg-dark');
    }
  });
</script>

</body>
</html>
