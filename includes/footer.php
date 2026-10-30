<?php
require_once __DIR__ . '/../config/db.php';
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="logo-brand" style="margin-bottom: 1rem;">
          <div class="logo-icon">&#127891;</div>
          <div>Student Login Page</div>
        </div>
        <p style="font-size: 0.9rem; line-height: 1.6; color: #cbd5e1;">
          Responsive Internet Programming (IP) Assignment showcasing PHP backend integration, secure database connectivity, dynamic form validations, and custom White, Navy Blue & Gold aesthetic.
        </p>
      </div>

      <div>
        <h4 class="footer-heading">Quick Pages</h4>
        <ul class="footer-links">
          <li><a href="index.php">&#8250; Home Page</a></li>
          <li><a href="login.php">&#8250; Login Portal</a></li>
          <li><a href="register.php">&#8250; New Registration</a></li>
          <li><a href="dashboard.php">&#8250; User Dashboard</a></li>
          <li><a href="contact.php">&#8250; Contact & Feedback</a></li>
        </ul>
      </div>

      <div>
        <h4 class="footer-heading">Technology</h4>
        <ul class="footer-links">
          <li><a href="#!">&#8250; PHP 8 Backend</a></li>
          <li><a href="#!">&#8250; MySQL / MariaDB</a></li>
          <li><a href="#!">&#8250; Prepared Statements</a></li>
          <li><a href="#!">&#8250; CSS3 Flexbox & Grid</a></li>
          <li><a href="#!">&#8250; JavaScript Validation</a></li>
        </ul>
      </div>

      <div>
        <h4 class="footer-heading">System Status</h4>
        <p style="font-size: 0.85rem; margin-bottom: 0.75rem; color:#cbd5e1;">
          Backend Connectivity:
        </p>
        <div style="margin-bottom: 1rem;">
          <?php echo db_status_badge(); ?>
        </div>
        <a href="setup.php" class="btn btn-navy btn-sm" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
          &#9881; View Database Tools
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      <div>
        &copy; <?php echo date('Y'); ?> <strong>Student Login Page</strong>. Designed with White, Navy Blue & Gold.
      </div>
      <div>
        Tested for Cross-Device Responsiveness (Mobile, Tablet, Desktop)
      </div>
    </div>
  </div>
</footer>

<script src="assets/js/validation.js"></script>
</body>
</html>

