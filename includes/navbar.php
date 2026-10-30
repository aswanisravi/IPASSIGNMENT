<?php
require_once __DIR__ . '/../config/db.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
?>
<header class="site-header">
  <div class="container nav-container">
    <a href="index.php" class="logo-brand">
      <div class="logo-icon">&#127891;</div>
      <div>Student Login Page</div>
    </a>

    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu" aria-expanded="false">
      &#9776;
    </button>

    <nav>
      <ul class="nav-menu" id="navMenu">
        <li>
          <a href="index.php" class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">Home</a>
        </li>
        <li>
          <a href="dashboard.php" class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        </li>
        <li>
          <a href="contact.php" class="nav-link <?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>">Contact Us</a>
        </li>
        <li>
          <a href="setup.php" class="nav-link <?php echo ($current_page === 'setup.php') ? 'active' : ''; ?>">DB Status</a>
        </li>

        <?php if ($is_logged_in): ?>
          <li style="display:flex; align-items:center; gap:0.5rem; margin-left:0.5rem;">
            <span style="color:var(--gold-light); font-size:0.9rem; font-weight:600;">
              &#128100; <?php echo htmlspecialchars($user_display_name); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-gold btn-sm">Logout</a>
          </li>
        <?php else: ?>
          <li>
            <a href="login.php" class="nav-link <?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">Login</a>
          </li>
          <li>
            <a href="register.php" class="btn btn-gold btn-sm">Sign Up</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

