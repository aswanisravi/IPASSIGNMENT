<?php
$page_title = "Student Login";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

// Redirect to dashboard if already logged in
if (!empty($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$entered_student_id = '';

// Check for redirect flash messages (e.g. from password reset)
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

// Handle Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submitted_token)) {
        $error_message = "Security token expired. Please refresh the page and try again.";
    } else {
        $entered_student_id = trim($_POST['student_id'] ?? '');
        $entered_password = $_POST['password'] ?? '';

        if (empty($entered_student_id) || empty($entered_password)) {
            $error_message = "Please enter both your Student ID and Password.";
        } else {
            $pdo = get_pdo();
            // Query by student_id using prepared statement
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
            $stmt->execute([$entered_student_id]);
            $student = $stmt->fetch();

            if ($student) {
                // Student ID exists -> verify password hash
                if (password_verify($entered_password, $student['password_hash'])) {
                    // Successful login: regenerate session ID for security
                    session_regenerate_id(true);
                    $_SESSION['student_id'] = $student['student_id'];
                    $_SESSION['user_id'] = $student['id'];
                    $_SESSION['first_name'] = $student['first_name'];
                    $_SESSION['last_name'] = $student['last_name'];
                    $_SESSION['email'] = $student['email'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Password does not match
                    $error_message = "Wrong password";
                }
            } else {
                // Student ID does not exist in database
                $error_message = "Student ID not found. Please create an account.";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="auth-page-wrapper">
  <div class="portal-card">
    
    <div class="portal-card-header">
      <div class="header-icon-circle">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor">
          <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.5 8.13 21.36 12 21.36C15.87 21.36 19 19.5 19 17.18V13.18L12 17L5 13.18Z"/>
        </svg>
      </div>
      <h1 class="portal-card-title">Student Login</h1>
      <p class="portal-card-subtitle">Sign in to your official student account</p>
    </div>

    <!-- Quick Test Credentials Box -->
    <div class="quick-test-box">
      <div class="quick-test-header">
        <span>Quick Test Account</span>
        <button type="button" class="quick-btn" onclick="document.getElementById('login_student_id').value='20240101'; document.getElementById('login_password').value='Password123';">
          Auto-Fill Demo
        </button>
      </div>
      <div>ID: <code>20240101</code> &bull; Pass: <code>Password123</code></div>
    </div>

    <!-- Green Success Alert (e.g. Password changed) -->
    <?php if (!empty($flash_success)): ?>
      <div class="alert alert-success" role="alert">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <div><?php echo htmlspecialchars($flash_success); ?></div>
      </div>
    <?php endif; ?>

    <!-- Red Error Alert -->
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger" role="alert">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <div><?php echo htmlspecialchars($error_message); ?></div>
      </div>
    <?php endif; ?>

    <!-- Login Form with only: Student ID and Password -->
    <form action="index.php" method="POST" id="loginForm" novalidate>
      <?php echo csrf_field(); ?>

      <!-- Student ID -->
      <div class="form-group">
        <label for="login_student_id" class="form-label">
          Student ID <span class="req">*</span>
        </label>
        <input 
          type="text" 
          name="student_id" 
          id="login_student_id" 
          class="form-input <?php echo (!empty($error_message) && $error_message !== 'Wrong password') ? 'is-invalid' : ''; ?>" 
          placeholder="Enter your 6-12 digit Student ID" 
          value="<?php echo htmlspecialchars($entered_student_id); ?>"
          required
          autocomplete="username"
        >
      </div>

      <!-- Password -->
      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
          <label for="login_password" class="form-label" style="margin-bottom: 0;">
            Password <span class="req">*</span>
          </label>
          <a href="forgot.php" class="quick-btn" style="font-size: 0.82rem;">Forgot password?</a>
        </div>
        <input 
          type="password" 
          name="password" 
          id="login_password" 
          class="form-input <?php echo ($error_message === 'Wrong password') ? 'is-invalid' : ''; ?>" 
          placeholder="Enter your password" 
          required
          autocomplete="current-password"
        >
      </div>

      <!-- Submit Button (Gold with Navy Text) -->
      <div style="margin-top: 1.75rem;">
        <button type="submit" class="btn btn-gold btn-block">
          Login to Portal
        </button>
      </div>
    </form>

    <!-- Registration Link below the form -->
    <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-light); font-size: 0.92rem; color: var(--text-muted);">
      New student? <a href="register.php" style="font-weight: 700; color: var(--navy); text-decoration: underline;">Create an account</a>
    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
