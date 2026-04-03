<?php 
session_start();

$host = 'localhost';
$db   = 'shakira_salon';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Active announcements
    $announcements = $pdo->query("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

    // Active promos
    $today  = date('Y-m-d');
    $promos = $pdo->query("SELECT * FROM promos WHERE status='active' AND valid_from <= '$today' AND valid_until >= '$today' ORDER BY created_at DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

    // Services
    $services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $announcements = [];
    $promos        = [];
    $services      = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shakira Salon — Online Appointment System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/shared.css">

  <style>
    body { padding-top: 0; background: var(--bg-body); }

    /* ── Hero ── */
    .hero {
      min-height: 100vh;
      background: url('assets/images/salon-banner.webp') center center / cover no-repeat;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      padding: 0 1rem;
    }
    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(170deg, rgba(0,0,0,0.55) 0%, rgba(194,24,91,0.45) 100%);
    }
    .hero-content { position: relative; z-index: 1; max-width: 740px; }
    .hero-badge {
      display: inline-block;
      background: rgba(255,64,129,0.25);
      border: 1px solid rgba(255,255,255,0.35);
      color: #fff;
      padding: 6px 20px;
      border-radius: var(--radius-pill);
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 22px;
      backdrop-filter: blur(6px);
    }
    .hero h1 {
      font-size: clamp(2.4rem, 5.5vw, 4rem);
      font-weight: 800;
      line-height: 1.12;
      margin-bottom: 18px;
      text-shadow: 0 3px 20px rgba(0,0,0,0.45);
    }
    .hero h1 span { color: var(--primary-light); }
    .hero .lead {
      font-size: 1.15rem;
      opacity: 0.92;
      margin-bottom: 2.2rem;
      text-shadow: 0 2px 8px rgba(0,0,0,0.35);
    }
    .hero-cta {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--gradient);
      color: #fff;
      padding: 14px 38px;
      border-radius: var(--radius-pill);
      font-size: 1.05rem;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 8px 30px rgba(233,30,99,0.5);
      transition: var(--transition);
    }
    .hero-cta:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 12px 40px rgba(233,30,99,0.6); color: #fff; }
    .hero-cta-outline {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      border: 2px solid rgba(255,255,255,0.7);
      color: #fff;
      padding: 12px 30px;
      border-radius: var(--radius-pill);
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
      margin-left: 12px;
    }
    .hero-cta-outline:hover { background: rgba(255,255,255,0.15); color: #fff; }
    .scroll-indicator {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1;
      color: rgba(255,255,255,0.6);
      font-size: 1.4rem;
      animation: bounce 1.8s infinite;
    }
    @keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(8px)} }

    /* ── Announcement ticker ── */
    .announcement-ticker {
      background: linear-gradient(90deg, var(--primary-darker) 0%, var(--primary-dark) 100%);
      color: #fff;
      padding: 10px 0;
      overflow: hidden;
      border-bottom: 2px solid rgba(255,255,255,0.1);
    }
    .ticker-label {
      background: rgba(255,255,255,0.2);
      padding: 2px 14px;
      border-radius: var(--radius-pill);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      white-space: nowrap;
      flex-shrink: 0;
    }
    .ticker-wrap { overflow: hidden; flex: 1; }
    .ticker-content {
      display: flex;
      animation: ticker-scroll 28s linear infinite;
      white-space: nowrap;
    }
    .ticker-item {
      padding: 0 48px;
      font-size: 0.9rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .ticker-item i { color: var(--primary-light); font-size: 0.85rem; }
    @keyframes ticker-scroll { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

    /* ── Section shared ── */
    .sec { padding: 80px 0; }
    .sec-alt { background: linear-gradient(135deg, #f8f0f5 0%, #fce4ec 100%); }
    .sec-dark { background: linear-gradient(135deg, #2d2d2d 0%, #1a1a2e 100%); color: #fff; }
    .sec-label {
      display: inline-block;
      background: var(--primary-lighter);
      color: var(--primary);
      padding: 4px 16px;
      border-radius: var(--radius-pill);
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .sec-dark .sec-label { background: rgba(255,64,129,0.2); color: var(--primary-light); }
    .sec-title {
      font-size: clamp(1.7rem, 3.5vw, 2.4rem);
      font-weight: 800;
      margin-bottom: 12px;
      color: var(--text-dark);
      letter-spacing: -0.3px;
    }
    .sec-dark .sec-title { color: #fff; }
    .sec-sub {
      font-size: 1rem;
      color: var(--text-muted);
      margin-bottom: 44px;
      max-width: 560px;
      margin-left: auto;
      margin-right: auto;
    }
    .sec-dark .sec-sub { color: rgba(255,255,255,0.65); }

    /* ── Service cards ── */
    .service-card {
      border: none;
      border-radius: var(--radius-lg);
      overflow: hidden;
      background: #fff;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .service-card:hover { transform: translateY(-10px); box-shadow: 0 20px 44px rgba(233,30,99,0.2); }
    .service-card .img-wrap { overflow: hidden; height: 230px; flex-shrink: 0; }
    .service-card .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .service-card:hover .img-wrap img { transform: scale(1.1); }
    .service-card .img-placeholder {
      height: 230px;
      background: var(--gradient);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .service-card .img-placeholder i { font-size: 3.5rem; color: rgba(255,255,255,0.4); }
    .service-card .card-body { padding: 1.4rem; display: flex; flex-direction: column; flex: 1; }
    .service-card .card-title { font-weight: 700; color: var(--text-dark); font-size: 1.05rem; margin-bottom: 8px; }
    .service-card .card-desc { color: var(--text-muted); font-size: 0.88rem; flex: 1; min-height: 44px; }
    .service-price { font-size: 1.6rem; font-weight: 800; color: var(--primary); margin: 12px 0; }
    .btn-book-now {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 10px;
      background: var(--gradient);
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      font-weight: 600;
      font-size: 0.9rem;
      text-decoration: none;
      transition: var(--transition);
      cursor: pointer;
    }
    .btn-book-now:hover { background: linear-gradient(135deg,#e91e63,#c2185b); color: #fff; transform: translateY(-2px); box-shadow: var(--shadow-pink); }

    /* ── Promo cards ── */
    .promo-card {
      border: none;
      border-radius: var(--radius-lg);
      overflow: hidden;
      background: #fff;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      position: relative;
      height: 100%;
    }
    .promo-card:hover { transform: translateY(-8px); box-shadow: 0 18px 40px rgba(233,30,99,0.25); }
    .promo-card .img-wrap { overflow: hidden; height: 190px; }
    .promo-card .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .promo-card:hover .img-wrap img { transform: scale(1.08); }
    .promo-card .img-placeholder {
      height: 190px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .promo-card .img-placeholder i { font-size: 3rem; color: rgba(255,255,255,0.45); }
    .promo-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: var(--gradient);
      color: #fff;
      padding: 5px 14px;
      border-radius: var(--radius-pill);
      font-size: 0.8rem;
      font-weight: 700;
      z-index: 1;
      box-shadow: 0 3px 12px rgba(0,0,0,0.2);
    }
    .promo-card .card-body { padding: 1.3rem; }
    .promo-card .promo-title { font-weight: 700; font-size: 1rem; color: var(--text-dark); margin-bottom: 6px; }
    .promo-card .promo-desc { font-size: 0.87rem; color: var(--text-muted); margin-bottom: 10px; }
    .promo-code-chip {
      display: inline-block;
      background: #1a1a2e;
      color: #fff;
      padding: 4px 14px;
      border-radius: var(--radius-pill);
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 1.5px;
    }
    .promo-valid { font-size: 0.8rem; color: var(--text-muted); margin-top: 8px; }

    /* ── Announcement cards ── */
    .announcement-card {
      background: #fff;
      border-radius: var(--radius-lg);
      padding: 24px;
      box-shadow: var(--shadow-sm);
      border-left: 4px solid var(--primary);
      transition: var(--transition);
      height: 100%;
    }
    .announcement-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-pink); }
    .announcement-card .ann-icon {
      width: 42px;
      height: 42px;
      background: var(--primary-lighter);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--radius-sm);
      font-size: 1rem;
      margin-bottom: 12px;
      flex-shrink: 0;
    }
    .announcement-card .ann-title { font-weight: 700; font-size: 1rem; color: var(--text-dark); margin-bottom: 6px; }
    .announcement-card .ann-body { font-size: 0.87rem; color: var(--text-muted); line-height: 1.55; }
    .announcement-card .ann-date { font-size: 0.78rem; color: var(--text-light); margin-top: 10px; }

    /* ── Style showcase ── */
    .style-card {
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      position: relative;
    }
    .style-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); }
    .style-card img { width: 100%; height: 260px; object-fit: cover; display: block; transition: transform 0.5s ease; }
    .style-card:hover img { transform: scale(1.07); }
    .style-overlay {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.75), transparent);
      color: #fff;
      padding: 28px 20px 20px;
    }
    .style-overlay h5 { font-weight: 700; margin-bottom: 3px; }
    .style-overlay small { opacity: 0.8; font-size: 0.82rem; }

    /* ── Stats strip ── */
    .stats-strip {
      background: var(--gradient);
      padding: 50px 0;
      color: #fff;
    }
    .stat-item { text-align: center; }
    .stat-item .stat-num { font-size: 2.8rem; font-weight: 800; line-height: 1; margin-bottom: 6px; }
    .stat-item .stat-label { font-size: 0.9rem; opacity: 0.88; font-weight: 500; }

    /* ── CTA ── */
    .cta-section {
      padding: 90px 1rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cta-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at center, rgba(255,255,255,0.06) 0%, transparent 70%);
      pointer-events: none;
    }
    .cta-section h2 { font-size: clamp(1.9rem,4vw,2.8rem); font-weight: 800; margin-bottom: 14px; }
    .cta-section p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem; }
    .btn-cta-outline {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 2px solid #fff;
      color: #fff;
      background: transparent;
      padding: 13px 36px;
      border-radius: var(--radius-pill);
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      transition: var(--transition);
    }
    .btn-cta-outline:hover { background: #fff; color: var(--primary-dark); box-shadow: 0 8px 28px rgba(255,255,255,0.3); }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .sec { padding: 60px 0; }
      .hero h1 { font-size: 2.1rem; }
      .stat-item .stat-num { font-size: 2.1rem; }
      .hero-cta-outline { display: none; }
    }
    @media (max-width: 480px) {
      .hero { min-height: 92vh; padding-top: 70px; }
    }
  </style>
</head>
<body>

<!-- ══ NAVBAR ══ -->
<nav class="salon-navbar navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fa-solid fa-scissors"></i> Shakira Salon
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fa-solid fa-house me-1"></i>Home</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php"><i class="fa-solid fa-user-plus me-1"></i>Sign Up</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php if (!empty($announcements)): ?>
<!-- ══ ANNOUNCEMENT TICKER ══ -->
<div class="announcement-ticker" style="margin-top: var(--navbar-height);">
  <div class="container-fluid d-flex align-items-center gap-3 px-4">
    <span class="ticker-label"><i class="fa-solid fa-bullhorn me-1"></i> News</span>
    <div class="ticker-wrap">
      <div class="ticker-content">
        <?php
          $doubled = array_merge($announcements, $announcements);
          foreach ($doubled as $ann):
        ?>
          <span class="ticker-item">
            <i class="fa-solid fa-circle-dot"></i>
            <strong><?= htmlspecialchars($ann['title']) ?>:</strong>
            <?= htmlspecialchars($ann['content']) ?>
          </span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
  <div style="height: var(--navbar-height);"></div>
<?php endif; ?>

<!-- ══ HERO ══ -->
<section class="hero">
  <div class="hero-content" data-aos="fade-up" data-aos-duration="900">
    <div class="hero-badge"><i class="fa-solid fa-star me-1"></i> Tuao West, Cagayan</div>
    <h1>Transform Your <span>Look</span><br>At Shakira Salon</h1>
    <p class="lead">Professional hair & beauty services crafted to bring out your best self.</p>
    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
      <a href="register.php" class="hero-cta">
        <i class="fa-solid fa-calendar-check"></i> Book Appointment
      </a>
      <a href="login.php" class="hero-cta-outline">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </a>
    </div>
  </div>
  <div class="scroll-indicator">
    <i class="fa-solid fa-chevron-down"></i>
  </div>
</section>

<!-- ══ STATS STRIP ══ -->
<div class="stats-strip" data-aos="fade-up">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="stat-num">500+</div>
          <div class="stat-label">Happy Clients</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="stat-num"><?= count($services) ?>+</div>
          <div class="stat-label">Services Offered</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="stat-num">5+</div>
          <div class="stat-label">Expert Stylists</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="stat-num">5★</div>
          <div class="stat-label">Client Rating</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($services)): ?>
<!-- ══ SERVICES ══ -->
<section class="sec" id="services">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <span class="sec-label"><i class="fa-solid fa-spa me-1"></i> What We Offer</span>
      <h2 class="sec-title">Our Beauty Services</h2>
      <p class="sec-sub">Discover our wide range of professional salon services tailored just for you</p>
    </div>
    <div class="row g-4">
      <?php foreach ($services as $i => $svc): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 6) * 80 ?>">
          <div class="service-card">
            <?php if (!empty($svc['image']) && file_exists($svc['image'])): ?>
              <div class="img-wrap"><img src="<?= htmlspecialchars($svc['image']) ?>" alt="<?= htmlspecialchars($svc['name']) ?>"></div>
            <?php else: ?>
              <div class="img-placeholder"><i class="fa-solid fa-scissors"></i></div>
            <?php endif; ?>
            <div class="card-body">
              <div class="card-title"><?= htmlspecialchars($svc['name']) ?></div>
              <div class="card-desc"><?= htmlspecialchars($svc['description']) ?></div>
              <div class="service-price">₱<?= number_format($svc['price'], 2) ?></div>
              <a href="register.php" class="btn-book-now">
                <i class="fa-solid fa-calendar-check"></i> Book Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══ SIGNATURE STYLES ══ -->
<section class="sec sec-alt" id="styles">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <span class="sec-label"><i class="fa-solid fa-wand-magic-sparkles me-1"></i> Showcased Looks</span>
      <h2 class="sec-title">Our Signature Styles</h2>
      <p class="sec-sub">Handcrafted looks that make a lasting statement</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
        <div class="style-card">
          <img src="assets/images/fade.webp" alt="Fade Cut">
          <div class="style-overlay">
            <h5>Fade Cut</h5>
            <small>Clean, modern & sharp</small>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="style-card">
          <img src="assets/images/layered.webp" alt="Layered Cut">
          <div class="style-overlay">
            <h5>Layered Cut</h5>
            <small>Volume & dynamic flow</small>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="style-card">
          <img src="assets/images/salon-banner.webp" alt="Classic Bob">
          <div class="style-overlay">
            <h5>Classic Bob</h5>
            <small>Timeless & elegant</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($promos)): ?>
<!-- ══ PROMOS ══ -->
<section class="sec" id="promos">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <span class="sec-label"><i class="fa-solid fa-tags me-1"></i> Limited Time</span>
      <h2 class="sec-title">Special Promos &amp; Offers</h2>
      <p class="sec-sub">Don't miss out on these exclusive deals just for you</p>
    </div>
    <div class="row g-4">
      <?php foreach ($promos as $i => $promo): ?>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
          <div class="promo-card">
            <?php if ($promo['discount_percentage']): ?>
              <div class="promo-badge"><i class="fa-solid fa-percent me-1"></i><?= $promo['discount_percentage'] ?>% OFF</div>
            <?php elseif ($promo['discount_amount']): ?>
              <div class="promo-badge">₱<?= number_format($promo['discount_amount'], 0) ?> OFF</div>
            <?php endif; ?>

            <?php if (!empty($promo['image'])): ?>
              <div class="img-wrap"><img src="<?= htmlspecialchars($promo['image']) ?>" alt="<?= htmlspecialchars($promo['title']) ?>"></div>
            <?php else: ?>
              <div class="img-placeholder"><i class="fa-solid fa-tag"></i></div>
            <?php endif; ?>

            <div class="card-body">
              <div class="promo-title"><?= htmlspecialchars($promo['title']) ?></div>
              <div class="promo-desc"><?= htmlspecialchars($promo['description']) ?></div>
              <?php if (!empty($promo['promo_code'])): ?>
                <div class="mb-1"><small class="text-muted">Code:</small></div>
                <span class="promo-code-chip"><?= htmlspecialchars($promo['promo_code']) ?></span>
              <?php endif; ?>
              <div class="promo-valid">
                <i class="fa-solid fa-calendar me-1"></i>
                Valid until <strong><?= date('M d, Y', strtotime($promo['valid_until'])) ?></strong>
              </div>
              <a href="register.php" class="btn-book-now mt-3">
                <i class="fa-solid fa-gift me-1"></i> Avail Now
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($announcements)): ?>
<!-- ══ ANNOUNCEMENTS CARDS ══ -->
<section class="sec sec-alt" id="announcements">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <span class="sec-label"><i class="fa-solid fa-bullhorn me-1"></i> Stay Updated</span>
      <h2 class="sec-title">Latest Announcements</h2>
      <p class="sec-sub">Important updates and news from Shakira Salon</p>
    </div>
    <div class="row g-4">
      <?php foreach ($announcements as $i => $ann): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
          <div class="announcement-card">
            <div class="ann-icon"><i class="fa-solid fa-megaphone"></i></div>
            <div class="ann-title"><?= htmlspecialchars($ann['title']) ?></div>
            <div class="ann-body"><?= htmlspecialchars($ann['content']) ?></div>
            <div class="ann-date"><i class="fa-regular fa-clock me-1"></i><?= date('M d, Y', strtotime($ann['created_at'])) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══ CTA ══ -->
<section class="cta-section sec-dark">
  <div class="container" data-aos="zoom-in">
    <h2>Ready to Look Your Best?</h2>
    <p>Book your appointment at Shakira Salon today — we can't wait to see you!</p>
    <a href="register.php" class="btn-cta-outline">
      <i class="fa-solid fa-calendar-check"></i> Get Started — It's Free
    </a>
  </div>
</section>

<!-- ══ FOOTER ══ -->
<footer class="salon-footer">
  <div class="container">
    <h5><i class="fa-solid fa-scissors me-2"></i>Shakira Salon</h5>
    <p><i class="fa-solid fa-location-dot me-1"></i> Tuao West, Cagayan, Philippines</p>
    <p><i class="fa-solid fa-phone me-1"></i> +63 912 345 6789</p>
    <p><i class="fa-solid fa-envelope me-1"></i>
      <a href="mailto:shakirabeautysalon@email.com">shakirabeautysalon@email.com</a>
    </p>
    <div class="social d-flex justify-content-center gap-3 mt-3">
      <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
    </div>
    <hr>
    <p class="copyright">&copy; <?= date('Y') ?> Shakira Salon. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true, offset: 60 });
</script>
</body>
</html>
