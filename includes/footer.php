<?php
require_once __DIR__ . '/../config.php';
?>
<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-grid">
      <div class="footer-brand-col">
        <div class="footer-brand">
          <div class="brand-logo-badge brand-logo-badge-sm">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.5 8.13 21.36 12 21.36C15.87 21.36 19 19.5 19 17.18V13.18L12 17L5 13.18Z"/>
            </svg>
          </div>
          <div>
            <div class="footer-college"><?php echo htmlspecialchars(COLLEGE_NAME); ?></div>
            <div class="footer-tagline"><?php echo htmlspecialchars(PORTAL_NAME); ?></div>
          </div>
        </div>
        <p class="footer-desc">
          Official self-service academic portal for enrolled students. Access course schedules, examination timetables, library cards, and profile verification.
        </p>
      </div>

      <div class="footer-nav-col">
        <h4 class="footer-title">Portal Navigation</h4>
        <ul class="footer-link-list">
          <li><a href="index.php">Student Login</a></li>
          <li><a href="register.php">Create Account</a></li>
          <li><a href="forgot.php">Forgot Password</a></li>
          <li><a href="dashboard.php">Student Dashboard</a></li>
        </ul>
      </div>

      <div class="footer-nav-col">
        <h4 class="footer-title">Student Help Desk</h4>
        <ul class="footer-link-list">
          <li><span>Office: Academic Affairs Hall, Room 102</span></li>
          <li><span>Email: helpdesk@apextech.edu</span></li>
          <li><span>Hours: Mon - Fri, 9:00 AM - 5:00 PM</span></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(COLLEGE_NAME); ?>. All rights reserved.</div>
      <div>Authorized Student Access Portal &bull; Encrypted Session Security</div>
    </div>
  </div>
</footer>

<script src="assets/js/validation.js"></script>
</body>
</html>
