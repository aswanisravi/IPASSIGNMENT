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

  <!-- Section A: What you can do after logging in -->
  <section class="student-section">
    <div class="container">
      <div class="student-section-header">
        <h2 class="student-section-title">What you can do after logging in</h2>
      </div>
      <div class="student-cards-grid">
        <div class="student-card">
          <div class="student-card-icon">&#128100;</div>
          <h3 class="student-card-title">My Profile</h3>
          <p class="student-card-text">View your name, email and student details.</p>
        </div>
        <div class="student-card">
          <div class="student-card-icon">&#128218;</div>
          <h3 class="student-card-title">Course Materials</h3>
          <p class="student-card-text">Find notes and reading lists for your subjects.</p>
        </div>
        <div class="student-card">
          <div class="student-card-icon">&#128197;</div>
          <h3 class="student-card-title">Notices and Deadlines</h3>
          <p class="student-card-text">See college announcements and submission dates.</p>
        </div>
        <div class="student-card">
          <div class="student-card-icon">&#128172;</div>
          <h3 class="student-card-title">Help Desk</h3>
          <p class="student-card-text">Send a question to the office from the Contact page.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section B: How to log in -->
  <section class="student-section student-section-alt">
    <div class="container">
      <div class="student-section-header">
        <h2 class="student-section-title">How to log in</h2>
      </div>
      <div class="student-steps-grid">
        <div class="student-step-card">
          <div class="student-step-number">1</div>
          <p class="student-step-text">Create your account with your name, email and a strong password.</p>
        </div>
        <div class="student-step-card">
          <div class="student-step-number">2</div>
          <p class="student-step-text">Log in using the same name, email and password.</p>
        </div>
        <div class="student-step-card">
          <div class="student-step-number">3</div>
          <p class="student-step-text">Open your dashboard to see your details.</p>
        </div>
      </div>
      <div class="student-steps-action">
        <a href="login.php" class="btn btn-gold">Student Login</a>
      </div>
    </div>
  </section>

  <!-- Section C: Notice board & Section D: Password tips -->
  <section class="student-section">
    <div class="container student-content-container">
      
      <!-- C. Notice board -->
      <div class="student-notices-block">
        <div class="student-section-header-left">
          <h2 class="student-section-title">Notice board</h2>
        </div>
        <div class="student-notices-list">
          <div class="student-notice-item">
            <span class="student-notice-date">Nov 05</span>
            <span class="student-notice-text">Internal exam timetable published.</span>
          </div>
          <div class="student-notice-item">
            <span class="student-notice-date">Nov 12</span>
            <span class="student-notice-text">Library timing changed for exam week.</span>
          </div>
          <div class="student-notice-item">
            <span class="student-notice-date">Nov 20</span>
            <span class="student-notice-text">Last date to submit project reports.</span>
          </div>
        </div>
      </div>

      <!-- D. Password tips -->
      <div class="student-password-block">
        <div class="student-password-box">
          <h3 class="student-password-title">&#128274; Password tips</h3>
          <p class="student-password-text">
            Use at least 8 characters with uppercase, lowercase, a number and a special character such as @ # $ %. Never share your password.
          </p>
        </div>
      </div>

    </div>
  </section>

  <!-- Section E: Need help? strip -->
  <section class="student-help-strip">
    <div class="container student-help-container">
      <p class="student-help-text">Forgot something or facing a problem? Contact the help desk.</p>
      <a href="contact.php" class="btn btn-gold">Contact Us</a>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
