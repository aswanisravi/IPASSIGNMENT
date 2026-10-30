<?php
$page_title = "Login";
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$login_status = ''; // 'valid', 'invalid', or ''
$error_message = '';
$success_message = '';
$entered_name = '';
$entered_email = '';
$field_errors = [];

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_name = trim($_POST['name'] ?? '');
    $entered_email = trim($_POST['email'] ?? '');
    $entered_password = $_POST['password'] ?? '';

    // Server-side Input Format Validation
    if (empty($entered_name)) {
        $field_errors['name'] = 'Full Name is required.';
    } elseif (strlen($entered_name) < 2) {
        $field_errors['name'] = 'Name must be at least 2 characters.';
    }

    if (empty($entered_email)) {
        $field_errors['email'] = 'Email ID is required.';
    } elseif (!filter_var($entered_email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = 'Please enter a valid Email ID.';
    }

    if (empty($entered_password)) {
        $field_errors['password'] = 'Password is required.';
    }

    // If format is valid, verify against Database
    if (empty($field_errors)) {
        // Query user record by Email
        $user = db_fetch_one("SELECT * FROM users WHERE LOWER(email) = LOWER(?)", [$entered_email]);

        if ($user) {
            // Verify both Name and Password
            $name_matches = (strcasecmp(trim($user['name']), $entered_name) === 0);
            $password_matches = password_verify($entered_password, $user['password']) || ($entered_password === $user['password']);

            if ($name_matches && $password_matches) {
                // VALID STATE: Set session and prepare success UI
                $login_status = 'valid';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['login_time'] = time();

                $success_message = "Authentication successful! Welcome back, <strong>" . htmlspecialchars($user['name']) . "</strong>. Redirecting to your dashboard...";
            } else {
                // INVALID STATE: Mismatch
                $login_status = 'invalid';
                if (!$name_matches && !$password_matches) {
                    $error_message = "Invalid Credentials: Both Name and Password do not match the account registered with this email.";
                    $field_errors['name'] = "Name mismatch for this account.";
                    $field_errors['password'] = "Incorrect password.";
                } elseif (!$name_matches) {
                    $error_message = "Invalid Name: The name entered does not match the registered name for this Email ID.";
                    $field_errors['name'] = "Registered name does not match.";
                } else {
                    $error_message = "Invalid Password: The password you entered is incorrect. Please try again.";
                    $field_errors['password'] = "Incorrect password.";
                }
            }
        } else {
            // User does not exist in database
            $login_status = 'invalid';
            $error_message = "Invalid Credentials: No registered account found with Email ID <strong>" . htmlspecialchars($entered_email) . "</strong>.";
            $field_errors['email'] = "Email ID not found in database.";
        }
    } else {
        $login_status = 'invalid';
        $error_message = "Form Validation Failed: Please fix the errors highlighted below.";
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="auth-wrapper">
  <div class="form-card <?php echo ($login_status === 'invalid') ? 'shake' : ''; ?>">
    <div class="form-header">
      <div class="logo-icon" style="margin: 0 auto 1rem; width: 48px; height: 48px; font-size: 1.5rem;">&#128272;</div>
      <h2>Student & Member Login</h2>
      <p>Enter your registered <strong>Name, Email ID, and Password</strong> to access your dashboard.</p>
    </div>

    <!-- Quick Test / Demo Credentials Box for Evaluation -->
    <div class="demo-box">
      <div class="demo-box-header">
        <span>&#9889; Quick Evaluation Credentials</span>
        <button type="button" class="btn btn-navy btn-sm" onclick="fillDemoCredentials('Alex Morgan', 'alex@example.com', 'password123')">
          Auto-Fill Valid
        </button>
      </div>
      <p style="margin: 0; color: #475569; font-size: 0.8rem;">
        <strong>Valid Demo:</strong> Name: <code class="demo-code">Alex Morgan</code> | Email: <code class="demo-code">alex@example.com</code> | Pass: <code class="demo-code">password123</code>
      </p>
      <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-outline-navy btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" onclick="fillDemoCredentials('Wrong Name', 'alex@example.com', 'wrongpass')">
          Fill Invalid Demo
        </button>
      </div>
    </div>

    <!-- Server-Side Feedback Alerts (VALID & INVALID) -->
    <?php if ($login_status === 'valid'): ?>
      <div class="alert alert-success" id="validAlert" role="alert">
        <span style="font-size: 1.3rem;">&#9989;</span>
        <div>
          <strong>VALID LOGIN!</strong><br>
          <?php echo $success_message; ?><br>
          <small><a href="dashboard.php" style="color: #065f46; text-decoration: underline; font-weight: 600;">Click here if not redirected automatically within 2 seconds.</a></small>
        </div>
      </div>
      <script>
        setTimeout(function() {
          window.location.href = 'dashboard.php';
        }, 1500);
      </script>
    <?php elseif ($login_status === 'invalid'): ?>
      <div class="alert alert-danger" id="invalidAlert" role="alert">
        <span style="font-size: 1.3rem;">&#10060;</span>
        <div>
          <strong>INVALID CREDENTIALS!</strong><br>
          <?php echo $error_message; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Session Flash Message (e.g. redirected from registration or logout) -->
    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="alert alert-info">
        <span>&#8505;</span>
        <div><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?></div>
      </div>
    <?php endif; ?>

    <!-- Login Form Asking Name, Email ID, and Password -->
    <form action="login.php" method="POST" id="loginForm" novalidate>
      
      <!-- 1. Full Name Field -->
      <div class="form-group">
        <label for="loginName" class="form-label">
          Full Name <span class="required-star">*</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="text" 
            name="name" 
            id="loginName" 
            class="form-input <?php echo isset($field_errors['name']) ? 'is-invalid' : ($login_status === 'valid' ? 'is-valid' : ''); ?>" 
            placeholder="e.g. Alex Morgan"
            value="<?php echo htmlspecialchars($entered_name); ?>"
            required
            autocomplete="name"
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['name']) ? 'error' : ''; ?>">
          <?php echo $field_errors['name'] ?? ''; ?>
        </div>
      </div>

      <!-- 2. Email ID Field -->
      <div class="form-group">
        <label for="loginEmail" class="form-label">
          Email ID <span class="required-star">*</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="email" 
            name="email" 
            id="loginEmail" 
            class="form-input <?php echo isset($field_errors['email']) ? 'is-invalid' : ($login_status === 'valid' ? 'is-valid' : ''); ?>" 
            placeholder="e.g. alex@example.com"
            value="<?php echo htmlspecialchars($entered_email); ?>"
            required
            autocomplete="email"
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['email']) ? 'error' : ''; ?>">
          <?php echo $field_errors['email'] ?? ''; ?>
        </div>
      </div>

      <!-- 3. Password Field -->
      <div class="form-group">
        <label for="loginPassword" class="form-label">
          Password <span class="required-star">*</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="password" 
            name="password" 
            id="loginPassword" 
            class="form-input <?php echo isset($field_errors['password']) ? 'is-invalid' : ($login_status === 'valid' ? 'is-valid' : ''); ?>" 
            placeholder="Enter your account password"
            required
            autocomplete="current-password"
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['password']) ? 'error' : ''; ?>">
          <?php echo $field_errors['password'] ?? ''; ?>
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.88rem;">
        <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer;">
          <input type="checkbox" name="remember" style="accent-color: var(--gold-primary);"> Remember me
        </label>
        <a href="#forgot" onclick="alert('For evaluation testing, please use the Demo Credentials box above or register a new test user.'); return false;" style="color: var(--navy-light);">
          Forgot credentials?
        </a>
      </div>

      <!-- Action Buttons (Gold & Navy) -->
      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <button type="submit" class="btn btn-gold btn-block" style="padding: 0.8rem; font-size: 1rem;">
          &#128273; Validate & Sign In
        </button>
        <a href="register.php" class="btn btn-navy btn-block" style="padding: 0.75rem; font-size: 0.95rem;">
          Create New Account
        </a>
      </div>
    </form>

    <div style="text-align: center; margin-top: 1.75rem; font-size: 0.85rem; color: #64748b;">
      Don't have an account yet? <a href="register.php" style="font-weight: 600; color: var(--navy-secondary); text-decoration: underline;">Sign up here</a>.
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

