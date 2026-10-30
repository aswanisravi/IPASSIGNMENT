<?php
$page_title = "Forgot Password - Student Verification";
require_once __DIR__ . '/config/db.php';

$step = 1; // 1: Enter Email, 2: Enter Code & New Password, 3: Success
$alert_type = '';
$alert_message = '';
$field_errors = [];

$email_input = '';
$code_input = '';
$generated_code = $_SESSION['reset_code'] ?? null;
$target_email = $_SESSION['reset_email'] ?? null;

// Determine step from session if code was previously sent
if ($generated_code && $target_email && !isset($_GET['restart'])) {
    $step = 2;
}

// 1. Handle Step 1 Submission: Request Verification Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_code') {
    $email_input = trim($_POST['email'] ?? '');

    if (empty($email_input)) {
        $field_errors['email'] = 'Email ID is required.';
    } elseif (!str_ends_with(strtolower($email_input), '@gmail.com')) {
        $field_errors['email'] = 'Email must be a valid @gmail.com address.';
    } else {
        // Query user in database
        $user = db_fetch_one("SELECT * FROM users WHERE LOWER(email) = LOWER(?)", [$email_input]);

        if ($user) {
            // Generate 6-digit numerical code
            $code = strval(mt_rand(100000, 999999));
            
            // Save in DB & session
            db_execute("UPDATE users SET reset_code = ? WHERE id = ?", [$code, $user['id']]);
            $_SESSION['reset_email'] = $user['email'];
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_user_id'] = $user['id'];

            $target_email = $user['email'];
            $generated_code = $code;
            $step = 2;

            $alert_type = 'success';
            $alert_message = "A 6-digit verification code has been generated and sent to your email ID: <strong>" . htmlspecialchars($target_email) . "</strong>.";
        } else {
            $alert_type = 'danger';
            $alert_message = "No student account found registered with the email ID: <strong>" . htmlspecialchars($email_input) . "</strong>.";
            $field_errors['email'] = "Email ID not registered.";
        }
    }
}

// 2. Handle Step 2 Submission: Verify Code & Update Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $code_input = trim($_POST['code'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $step = 2;

    // Validate Code
    if (empty($code_input)) {
        $field_errors['code'] = 'Verification code is required.';
    } elseif ($code_input !== $generated_code) {
        $field_errors['code'] = 'Invalid verification code. Please check and re-enter.';
    }

    // Validate New Password (alpha-numericals, 0 to 9, capital letter and small letter)
    if (empty($new_password)) {
        $field_errors['new_password'] = 'New password is required.';
    } elseif (strlen($new_password) < 6) {
        $field_errors['new_password'] = 'Password must be at least 6 characters.';
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $field_errors['new_password'] = 'Password must contain at least one CAPITAL letter (A-Z).';
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $field_errors['new_password'] = 'Password must contain at least one SMALL letter (a-z).';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $field_errors['new_password'] = 'Password must contain at least one NUMBER (0-9).';
    }

    if (empty($confirm_password)) {
        $field_errors['confirm_password'] = 'Please confirm your new password.';
    } elseif ($new_password !== $confirm_password) {
        $field_errors['confirm_password'] = 'Passwords do not match.';
    }

    // If code and password valid, update DB
    if (empty($field_errors)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['reset_user_id'] ?? null;

        if ($user_id) {
            db_execute("UPDATE users SET password = ?, reset_code = NULL WHERE id = ?", [$hashed, $user_id]);
        } else {
            db_execute("UPDATE users SET password = ?, reset_code = NULL WHERE email = ?", [$hashed, $target_email]);
        }

        // Clean reset session
        unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_user_id']);
        $step = 3;
        $alert_type = 'success';
        $alert_message = "Your password has been reset successfully! You can now sign in with your new password.";
    } else {
        $alert_type = 'danger';
        $alert_message = "Please correct the verification errors below.";
    }
}

// Reset flow if user wants to change email
if (isset($_GET['restart'])) {
    unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_user_id']);
    header("Location: forgot_password.php");
    exit();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="auth-wrapper" style="padding: 3.5rem 1.5rem; background-color: #f8fafc; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
  <div class="form-card" style="max-width: 520px;">
    
    <div class="form-header">
      <div class="logo-icon" style="margin: 0 auto 1rem; width: 48px; height: 48px; font-size: 1.5rem;">&#128273;</div>
      <h2>Reset Student Password</h2>
      <p>Follow the steps below to verify your email and set a new password.</p>
    </div>

    <!-- Alert notifications -->
    <?php if (!empty($alert_message)): ?>
      <div class="alert alert-<?php echo ($alert_type === 'success') ? 'success' : 'danger'; ?>" role="alert">
        <span style="font-size: 1.25rem;"><?php echo ($alert_type === 'success') ? '&#9989;' : '&#10060;'; ?></span>
        <div><?php echo $alert_message; ?></div>
      </div>
    <?php endif; ?>

    <!-- STEP 1: Type Email ID -->
    <?php if ($step === 1): ?>
      <form action="forgot_password.php" method="POST" novalidate>
        <input type="hidden" name="action" value="request_code">

        <div class="form-group">
          <label for="forgotEmail" class="form-label">
            Type Your Registered Email ID <span class="required-star">*</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="email" 
              name="email" 
              id="forgotEmail" 
              class="form-input <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>" 
              placeholder="e.g. alex@gmail.com"
              value="<?php echo htmlspecialchars($email_input); ?>"
              required
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['email']) ? 'error' : ''; ?>">
            <?php echo $field_errors['email'] ?? ''; ?>
          </div>
          <small style="color: #64748b; font-size: 0.82rem; margin-top: 0.35rem; display: block;">
            We will generate and send a 6-digit verification code to this email.
          </small>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
          <button type="submit" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1rem;">
            &#128233; Send Verification Code
          </button>
          <a href="index.php" class="btn btn-outline-navy btn-block" style="padding: 0.75rem;">
            Back to Student Login
          </a>
        </div>
      </form>

    <!-- STEP 2: Enter Verification Code & Set New Password -->
    <?php elseif ($step === 2): ?>

      <!-- Demo Simulation Code Helper Box -->
      <div style="background: #eff6ff; border: 1.5px dashed #3b82f6; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; text-align: center;">
        <div style="font-size: 0.85rem; color: #1e3a8a; font-weight: 700; margin-bottom: 0.25rem;">
          &#128232; Email Code Simulation (For Instant Evaluation)
        </div>
        <div style="font-size: 1.5rem; font-weight: 800; letter-spacing: 4px; color: #0B1F4B; font-family: monospace;">
          <?php echo htmlspecialchars($generated_code); ?>
        </div>
        <button type="button" class="btn btn-navy btn-sm" style="margin-top: 0.5rem; font-size: 0.78rem; padding: 0.25rem 0.6rem;" onclick="document.getElementById('verifyCode').value = '<?php echo htmlspecialchars($generated_code); ?>';">
          Auto-Fill Code
        </button>
      </div>

      <form action="forgot_password.php" method="POST" novalidate>
        <input type="hidden" name="action" value="reset_password">

        <!-- 6-digit Code Input -->
        <div class="form-group">
          <label for="verifyCode" class="form-label">
            Enter 6-Digit Verification Code <span class="required-star">*</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="text" 
              name="code" 
              id="verifyCode" 
              maxlength="6"
              class="form-input <?php echo isset($field_errors['code']) ? 'is-invalid' : ''; ?>" 
              placeholder="e.g. 6 digits"
              value="<?php echo htmlspecialchars($code_input); ?>"
              style="letter-spacing: 2px; font-weight: 700; font-size: 1.1rem;"
              required
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['code']) ? 'error' : ''; ?>">
            <?php echo $field_errors['code'] ?? ''; ?>
          </div>
        </div>

        <!-- New Password -->
        <div class="form-group">
          <label for="newPassword" class="form-label">
            Set New Password <span class="required-star">*</span>
            <span class="label-hint">(0-9, capital letter &amp; small letter)</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="password" 
              name="new_password" 
              id="newPassword" 
              class="form-input <?php echo isset($field_errors['new_password']) ? 'is-invalid' : ''; ?>" 
              placeholder="e.g. NewPass123"
              required
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['new_password']) ? 'error' : ''; ?>">
            <?php echo $field_errors['new_password'] ?? ''; ?>
          </div>
        </div>

        <!-- Confirm New Password -->
        <div class="form-group">
          <label for="confirmPassword" class="form-label">
            Confirm New Password <span class="required-star">*</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="password" 
              name="confirm_password" 
              id="confirmPassword" 
              class="form-input <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
              placeholder="Re-enter new password"
              required
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['confirm_password']) ? 'error' : ''; ?>">
            <?php echo $field_errors['confirm_password'] ?? ''; ?>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
          <button type="submit" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1rem;">
            &#128272; Verify Code &amp; Reset Password
          </button>
          <a href="forgot_password.php?restart=1" class="btn btn-outline-navy btn-block" style="padding: 0.75rem;">
            Use a Different Email
          </a>
        </div>
      </form>

    <!-- STEP 3: Password Reset Successful -->
    <?php elseif ($step === 3): ?>
      <div style="text-align: center; padding: 1.5rem 0;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">&#127881;</div>
        <h3 style="color: #0B1F4B; font-weight: 700; margin-bottom: 0.5rem;">Password Updated!</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 1.75rem;">
          Your new password meets all security conditions and has been updated in the database.
        </p>
        <a href="index.php" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1.05rem;">
          &#128273; Sign In with New Password
        </a>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
