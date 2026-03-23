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

  <!-- Shared Design System -->
  <link rel="stylesheet" href="assets/css/shared.css">

  <style>
    /* ─── Landing-page specific styles ─── */
    body { padding-top:0; background:var(--bg-light); }

    /* Navbar — dark glass, pinned */
    .landing-nav {
      background:rgba(30,30,30,0.92)!important;
      backdrop-filter:blur(12px);
      -webkit-backdrop-filter:blur(12px);
      transition:var(--transition);
      padding:12px 0;
    }
    .landing-nav.scrolled { background:rgba(20,20,20,0.98)!important; box-shadow:var(--shadow-lg); padding:8px 0; }
    .landing-nav .navbar-brand { font-size:1.7rem;font-weight:700;letter-spacing:1px;color:#fff; }
    .landing-nav .navbar-brand i { color:var(--primary); margin-right:6px; }
    .landing-nav .nav-link { font-weight:500;color:rgba(255,255,255,0.85);transition:var(--transition);padding:8px 16px;border-radius:var(--radius-full); }
    .landing-nav .nav-link:hover,
    .landing-nav .nav-link:focus { color:#fff;background:rgba(255,64,129,0.15); }

    /* ─── Hero ─── */
    .hero {
      background:url('assets/images/salon-banner.webp') no-repeat center center/cover;
      min-height:100vh;position:relative;color:#fff;
      display:flex;align-items:center;justify-content:center;text-align:center;padding:0 1rem;
    }
    .hero::before { content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,0.55) 0%,rgba(0,0,0,0.7) 100%);z-index:0; }
    .hero .hero-content { position:relative;z-index:1;max-width:720px; }
    .hero h1 { font-size:clamp(2.5rem,5vw,3.8rem);font-weight:800;margin-bottom:18px;text-shadow:0 3px 20px rgba(0,0,0,0.5);line-height:1.15; }
    .hero h1 span { color:var(--primary); }
    .hero p.lead { font-size:1.2rem;margin-bottom:2rem;opacity:0.92;text-shadow:0 2px 8px rgba(0,0,0,0.4); }
    .hero-cta {
      display:inline-flex;align-items:center;gap:10px;
      background:var(--gradient);color:#fff;border:none;padding:14px 36px;
      border-radius:var(--radius-full);font-size:1.1rem;font-weight:600;
      text-decoration:none;transition:var(--transition);
      box-shadow:0 6px 30px rgba(233,30,99,0.45);
    }
    .hero-cta:hover { transform:translateY(-3px) scale(1.04);box-shadow:0 10px 40px rgba(233,30,99,0.55);color:#fff; }
    .scroll-down { margin-top:2.5rem;color:rgba(255,255,255,0.6);font-size:2rem;text-shadow:0 2px 8px rgba(0,0,0,0.5); }

    /* ─── Section titles ─── */
    .section-title {
      font-size:2.2rem;font-weight:700;margin-bottom:12px;letter-spacing:0.5px;color:var(--text-dark);
    }
    .section-title i { color:var(--primary);margin-right:8px; }
    .section-subtitle { color:#777;font-size:1.05rem;margin-bottom:40px; }

    /* ─── Signature style cards ─── */
    .style-card {
      border:none;border-radius:var(--radius-lg);overflow:hidden;
      box-shadow:var(--shadow-sm);background:#fff;transition:var(--transition);
    }
    .style-card:hover { transform:translateY(-8px);box-shadow:var(--shadow-lg); }
    .style-card .card-img-wrap { overflow:hidden;height:250px; }
    .style-card .card-img-wrap img { width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease; }
    .style-card:hover .card-img-wrap img { transform:scale(1.1); }
    .style-card .card-body { padding:1.4rem; }
    .style-card .card-title { font-weight:700;color:var(--primary);margin-bottom:8px; }
    .style-card .card-text { color:#666;font-size:0.95rem; }

    /* ─── Services Section ─── */
    .services-section { background:linear-gradient(135deg,#f5f7fa 0%,#e8ecf1 100%); }
    .service-card {
      border:none;border-radius:var(--radius-lg);overflow:hidden;
      transition:var(--transition);box-shadow:var(--shadow-sm);background:#fff;height:100%;
    }
    .service-card:hover { transform:translateY(-10px);box-shadow:0 18px 40px rgba(233,30,99,0.22); }
    .service-card .card-img-wrap { overflow:hidden;height:250px; }
    .service-card .card-img-wrap img { width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease; }
    .service-card:hover .card-img-wrap img { transform:scale(1.12); }
    .service-card .card-body { padding:1.5rem; }
    .service-price { font-size:1.7rem;font-weight:700;color:var(--primary);margin:10px 0; }
    .service-description { color:#666;font-size:0.95rem;min-height:50px; }
    .service-placeholder { height:250px;background:var(--gradient);display:flex;align-items:center;justify-content:center; }
    .service-placeholder i { font-size:4rem;color:#fff;opacity:0.4; }
    .btn-book-service {
      background:var(--gradient);color:#fff;border:none;padding:10px 26px;
      border-radius:var(--radius-full);font-weight:600;transition:var(--transition);
      box-shadow:0 4px 16px rgba(233,30,99,0.3);
    }
    .btn-book-service:hover { transform:scale(1.06);box-shadow:0 8px 24px rgba(233,30,99,0.45);color:#fff; }

    /* ─── Promos ─── */
    .promo-card {
      position:relative;overflow:hidden;border-radius:var(--radius-lg);
      transition:var(--transition);box-shadow:var(--shadow-sm);background:#fff;
    }
    .promo-card:hover { transform:translateY(-8px);box-shadow:0 16px 36px rgba(233,30,99,0.3); }
    .promo-badge {
      position:absolute;top:14px;right:14px;background:var(--gradient);
      color:#fff;padding:7px 16px;border-radius:var(--radius-full);font-weight:700;
      z-index:1;box-shadow:0 3px 12px rgba(0,0,0,0.25);font-size:0.85rem;
    }
    .promo-card .card-img-wrap { overflow:hidden;height:200px; }
    .promo-card .card-img-wrap img { width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease; }
    .promo-card:hover .card-img-wrap img { transform:scale(1.08); }
    .promo-placeholder { height:200px;background:var(--gradient);display:flex;align-items:center;justify-content:center; }
    .promo-placeholder i { font-size:3.5rem;color:#fff;opacity:0.4; }
    .promo-overlay {
      position:absolute;bottom:0;left:0;right:0;
      background:linear-gradient(to top,rgba(0,0,0,0.85),transparent);
      color:#fff;padding:22px;transform:translateY(100%);transition:transform 0.35s ease;
    }
    .promo-card:hover .promo-overlay { transform:translateY(0); }

    /* ─── Announcement bar ─── */
    .announcement-bar {
      background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
      color:#fff;padding:14px 0;overflow:hidden;
    }
    .announcement-content { display:flex;animation:marquee 22s linear infinite;white-space:nowrap; }
    .announcement-item { padding:0 50px;display:inline-flex;align-items:center;font-size:0.95rem; }
    .announcement-item i { margin-right:10px;font-size:1.1rem; }
    @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

    /* ─── CTA Section ─── */
    .cta-section {
      background:var(--gradient);color:#fff;padding:5rem 1rem;text-align:center;
      position:relative;overflow:hidden;
    }
    .cta-section::before {
      content:"";position:absolute;inset:0;
      background:radial-gradient(ellipse at center,rgba(255,255,255,0.08) 0%,transparent 70%);
    }
    .cta-section h2 { font-size:clamp(2rem,4vw,2.8rem);font-weight:800;margin-bottom:1rem;position:relative; }
    .cta-section p.lead { font-size:1.25rem;margin-bottom:2rem;opacity:0.92;position:relative; }
    .cta-btn {
      display:inline-flex;align-items:center;gap:8px;
      border:2px solid #fff;color:#fff;background:transparent;
      padding:14px 36px;border-radius:var(--radius-full);font-weight:600;
      font-size:1.05rem;text-decoration:none;transition:var(--transition);
      position:relative;
    }
    .cta-btn:hover { background:#fff;color:var(--primary);box-shadow:0 8px 30px rgba(255,255,255,0.3);color:var(--primary-dark); }

    /* ─── Responsive ─── */
    @media(max-width:768px) {
      .hero h1 { font-size:2rem; }
      .hero p.lead { font-size:1rem; }
      .style-card .card-img-wrap,.service-card .card-img-wrap { height:190px; }
      .announcement-item { padding:0 20px; }
      .section-title { font-size:1.6rem; }
      .cta-section h2 { font-size:1.6rem; }
      .cta-section p.lead { font-size:1rem; }
    }
    @media(max-width:480px) {
      .hero { padding:90px 1rem 40px; }
      .service-price { font-size:1.4rem; }
    }
  </style>
</head>
<body>

<nav class="landing-nav navbar navbar-expand-lg navbar-dark fixed-top" aria-label="Primary Navigation">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-scissors"></i> Shakira Salon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav gap-1">
        <li class="nav-item">
          <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Sign Up</a>
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
    <h1>Transform Your <span>Look</span></h1>
    <p class="lead">Get the perfect style that suits your personality at Shakira Salon.</p>
    <a href="register.php" class="hero-cta" role="button">
      <i class="fas fa-calendar-check"></i> Book Your Appointment
    </a>
    <div class="scroll-down animate__animated animate__bounce animate__infinite">
      <i class="fas fa-chevron-down"></i>
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
    <p class="text-center section-subtitle" data-aos="fade-in">
      Discover our wide range of professional salon services tailored just for you
    </p>
    <div class="row g-4">
      <?php foreach ($services as $index => $service): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($index * 100) ?>">
          <div class="service-card h-100">
            <?php if ($service['image'] && file_exists($service['image'])): ?>
              <div class="card-img-wrap">
                <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['name']) ?>">
              </div>
            <?php else: ?>
              <div class="service-placeholder">
                <i class="fas fa-scissors"></i>
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
    <h2 class="text-center section-title" data-aos="zoom-in"><i class="fas fa-star"></i> Our Signature Styles</h2>
    <p class="text-center section-subtitle">Handcrafted looks that make a statement</p>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-right" data-aos-delay="100">
        <div class="style-card">
          <div class="card-img-wrap"><img src="assets/images/fade.webp" alt="Fade Haircut" /></div>
          <div class="card-body text-center">
            <h5 class="card-title">Fade Cut</h5>
            <p class="card-text">Clean fades that give you a modern, sharp look.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="style-card">
          <div class="card-img-wrap"><img src="assets/images/layered.webp" alt="Layered Haircut" /></div>
          <div class="card-body text-center">
            <h5 class="card-title">Layered Cut</h5>
            <p class="card-text">Add volume and dynamic flow with our professional layers.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-left" data-aos-delay="300">
        <div class="style-card">
          <div class="card-img-wrap"><img src="images/after/sample2.jpg" alt="Bob Haircut" /></div>
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
    <p class="text-center section-subtitle">Don't miss out on these limited-time deals</p>
    <div class="row g-4">
      <?php foreach ($promos as $promo): ?>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="promo-card card h-100">
            <?php if ($promo['discount_percentage']): ?>
              <div class="promo-badge"><i class="fas fa-percent"></i> <?= $promo['discount_percentage'] ?>% OFF</div>
            <?php elseif ($promo['discount_amount']): ?>
              <div class="promo-badge">₱<?= number_format($promo['discount_amount'], 0) ?> OFF</div>
            <?php endif; ?>
            
            <?php if ($promo['image']): ?>
              <div class="card-img-wrap"><img src="<?= htmlspecialchars($promo['image']) ?>" alt="<?= htmlspecialchars($promo['title']) ?>"></div>
            <?php else: ?>
              <div class="promo-placeholder">
                <i class="fas fa-tag"></i>
              </div>
            <?php endif; ?>
            
            <div class="card-body">
              <h5 class="card-title text-center"><?= htmlspecialchars($promo['title']) ?></h5>
              <p class="card-text text-center"><?= htmlspecialchars($promo['description']) ?></p>
              <?php if ($promo['promo_code']): ?>
                <div class="text-center mt-2">
                  <small class="text-muted">Use code:</small>
                  <div class="badge bg-dark" style="font-size:0.95rem;padding:8px 16px;border-radius:20px;letter-spacing:1px;">
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
    <a href="register.php" class="cta-btn" role="button">
      <i class="fas fa-sparkles"></i> Get Started
    </a>
  </div>
</section>

<footer class="salon-footer text-center">
  <div class="container">
    <div class="mb-2">
      <i class="fa-solid fa-scissors" style="color:var(--primary);font-size:1.3rem;"></i>
    </div>
    <p class="mb-2">&copy; <?= date("Y") ?> Shakira Salon | Tuao West, Cagayan</p>
    <div class="social-icons d-flex justify-content-center gap-3">
      <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
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
