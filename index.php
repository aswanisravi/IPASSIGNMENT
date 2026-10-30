<?php
$page_title = "Home - Student Login Page";
require_once __DIR__ . '/config/db.php';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main>
  <!-- Hero Section (White Background with Gold Line) -->
  <section class="hero-section">
    <div class="container hero-content">
      <div class="hero-badge">
        <span>&#127891;</span> Student Portal | Internet Programming
      </div>
      <h1 class="hero-title">
        Welcome to the <span class="gold-highlight">Student Login Portal</span>
      </h1>
      <p class="hero-subtitle">
        Sign in with your name, email and password to open your student profile. New here? Create your account in under a minute.
      </p>
      <div class="hero-actions">
        <a href="login.php" class="btn btn-gold">
          Student Login
        </a>
        <a href="register.php" class="btn btn-outline-navy">
          Create Account
        </a>
        <a href="contact.php" class="btn btn-outline-navy">
          Contact Us
        </a>
      </div>
    </div>
  </section>

  <!-- Project Requirements Alignment Banner -->
  <section style="background-color: #f1f5f9; border-bottom: 1px solid var(--border-light); padding: 1.25rem 0;">
    <div class="container" style="display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; gap: 1rem; font-size: 0.9rem; font-weight: 600; color: var(--navy-secondary);">
      <div><span style="color: var(--gold-dark);">&#10004;</span> 5 Complete Web Pages</div>
      <div><span style="color: var(--gold-dark);">&#10004;</span> PHP 8 Backend</div>
      <div><span style="color: var(--gold-dark);">&#10004;</span> MySQL / Database Connectivity</div>
      <div><span style="color: var(--gold-dark);">&#10004;</span> Real-Time & Server Form Validation</div>
      <div><span style="color: var(--gold-dark);">&#10004;</span> Name + Email + Password Login</div>
      <div><span style="color: var(--gold-dark);">&#10004;</span> White, Navy & Gold Theme</div>
    </div>
  </section>

  <!-- Features Section (White Background, Navy Cards with Gold Icons) -->
  <section class="section-padding" id="features">
    <div class="container">
      <div class="section-header">
        <span class="section-tag">Core Specifications</span>
        <h2 class="section-title">Assignment Architecture & Modules</h2>
        <p class="section-subtitle">
          Every requirement from the IP assignment prompt implemented with production-grade coding standards.
        </p>
      </div>

      <div class="grid-3">
        <!-- Card 1 -->
        <div class="card">
          <div class="card-icon">&#128241;</div>
          <h3 class="card-title">100% Responsive Design</h3>
          <p class="card-text">
            Built from the ground up using pure CSS3 Flexbox and CSS Grid. Seamlessly adapts from small mobile screens (320px) to wide 4K displays with a responsive mobile drawer menu.
          </p>
        </div>

        <!-- Card 2 -->
        <div class="card">
          <div class="card-icon">&#128272;</div>
          <h3 class="card-title">3-Field Authentication</h3>
          <p class="card-text">
            Specialized login verification requiring <strong>Full Name, Email ID, and Password</strong>. Validates both frontend format and backend database records with distinct valid and invalid feedback banners.
          </p>
        </div>

        <!-- Card 3 -->
        <div class="card">
          <div class="card-icon">&#128451;</div>
          <h3 class="card-title">Database Connectivity</h3>
          <p class="card-text">
            Supports standard MySQL via MySQLi/PDO with parameterized prepared statements preventing SQL injection attacks, supplemented with instant auto-fallback to SQLite.
          </p>
        </div>

        <!-- Card 4 -->
        <div class="card">
          <div class="card-icon">&#9989;</div>
          <h3 class="card-title">Strict Form Validation</h3>
          <p class="card-text">
            Dual-tier validation (instant JavaScript client feedback + bulletproof PHP server-side validation) applied across Login, Registration, and Contact pages with custom error highlights.
          </p>
        </div>

        <!-- Card 5 -->
        <div class="card">
          <div class="card-icon">&#127912;</div>
          <h3 class="card-title">Navy Blue & Gold Theme</h3>
          <p class="card-text">
            Sophisticated aesthetic featuring crisp white content backgrounds, royal navy blue headers and structural containers, complemented by metallic gold buttons, badges, and borders.
          </p>
        </div>

        <!-- Card 6 -->
        <div class="card">
          <div class="card-icon">&#128100;</div>
          <h3 class="card-title">Session-Based Portal</h3>
          <p class="card-text">
            Authenticated sessions store user credentials securely, granting access to a protected user dashboard displaying live database stats, activity logs, and one-click logout.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Project Stats -->
  <section style="background-color: var(--navy-primary); color: #ffffff; padding: 3.5rem 0; border-top: 2px solid var(--gold-primary); border-bottom: 2px solid var(--gold-primary);">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
        <div>
          <div style="font-size: 2.75rem; font-weight: 800; color: var(--gold-light);">5</div>
          <div style="color: #cbd5e1; font-size: 0.95rem; font-weight: 600;">Full Web Pages</div>
        </div>
        <div>
          <div style="font-size: 2.75rem; font-weight: 800; color: var(--gold-light);">3</div>
          <div style="color: #cbd5e1; font-size: 0.95rem; font-weight: 600;">Login Credentials Required</div>
        </div>
        <div>
          <div style="font-size: 2.75rem; font-weight: 800; color: var(--gold-light);">100%</div>
          <div style="color: #cbd5e1; font-size: 0.95rem; font-weight: 600;">Responsive Mobile Ready</div>
        </div>
        <div>
          <div style="font-size: 2.75rem; font-weight: 800; color: var(--gold-light);">PDO/MySQL</div>
          <div style="color: #cbd5e1; font-size: 0.95rem; font-weight: 600;">Secure Database Engine</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Interactive Call-To-Action Banner -->
  <section class="section-padding" style="background-color: var(--bg-surface);">
    <div class="container" style="text-align: center; max-width: 720px;">
      <h2 style="font-size: 2rem; color: var(--navy-primary); margin-bottom: 1rem;">
        Ready to evaluate the assignment?
      </h2>
      <p style="color: var(--text-muted); font-size: 1.05rem; margin-bottom: 2rem;">
        Test valid and invalid login scenarios, register a new user into the database, or inspect the backend database connectivity diagnostics.
      </p>
      <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="login.php" class="btn btn-gold" style="font-size: 1rem; padding: 0.8rem 1.75rem;">
          &#128273; Open Login Form
        </a>
        <a href="register.php" class="btn btn-navy" style="font-size: 1rem; padding: 0.8rem 1.75rem;">
          &#9997; Register New Account
        </a>
        <a href="setup.php" class="btn btn-outline-navy" style="font-size: 1rem; padding: 0.8rem 1.75rem;">
          &#9881; Database Diagnostic Tool
        </a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

