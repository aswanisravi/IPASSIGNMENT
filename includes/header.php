<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/csrf.php';

$is_logged_in = !empty($_SESSION['student_id']);
$student_name = $_SESSION['first_name'] ?? 'Student';
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = isset($page_title) ? $page_title . " - " . COLLEGE_NAME : PORTAL_NAME . " - " . COLLEGE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
  <div class="header-container">
    <a href="<?php echo $is_logged_in ? 'dashboard.php' : 'index.php'; ?>" class="brand-group">
      <div class="brand-logo-badge">
        <svg class="brand-cap-icon" viewBox="0 0 24 24" width="26" height="26" fill="currentColor">
          <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.5 8.13 21.36 12 21.36C15.87 21.36 19 19.5 19 17.18V13.18L12 17L5 13.18Z"/>
        </svg>
      </div>
      <div class="brand-text">
        <span class="brand-college"><?php echo htmlspecialchars(COLLEGE_NAME); ?></span>
        <span class="brand-portal"><?php echo htmlspecialchars(PORTAL_NAME); ?></span>
      </div>
    </a>

    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu" aria-expanded="false">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>

    <nav class="nav-menu" id="navMenu">
      <?php if ($is_logged_in): ?>
        <a href="dashboard.php" class="nav-item <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        <div class="user-pill">
          <span class="user-dot"></span>
          <span><?php echo htmlspecialchars($student_name); ?></span>
        </div>
        <a href="logout.php" class="btn-header-logout">Logout</a>
      <?php else: ?>
        <a href="index.php" class="nav-item <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">Login</a>
        <a href="register.php" class="nav-item <?php echo ($current_page === 'register.php') ? 'active' : ''; ?>">Create Account</a>
        <a href="forgot.php" class="nav-item <?php echo ($current_page === 'forgot.php') ? 'active' : ''; ?>">Forgot Password</a>
        <a href="register.php" class="btn btn-gold btn-sm">Get Started</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
