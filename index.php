<?php session_start(); ?>
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

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }
      .card img {
        height: 200px;
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
