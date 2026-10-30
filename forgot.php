<?php
$page_title = "Forgot Password";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

// Redirect to dashboard if already logged in
if (!empty($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';
$demo_code_notice = '';

// Step control: 1 = Email, 2 = Code, 3 = New Password
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    unset($_SESSION['forgot_step'], $_SESSION['forgot_email'], $_SESSION['code_verified'], $_SESSION['demo_code']);
    header("Location: forgot.php");
    exit();
}

$step = $_SESSION['forgot_step'] ?? 1;
$email = $_SESSION['forgot_email'] ?? '';
$demo_code_notice = $_SESSION['demo_code'] ?? '';

$pdo = get_pdo();

// Clean up expired codes from database
try {
    $pdo->exec("DELETE FROM reset_codes WHERE expires_at < " . (str_contains($pdo->getAttribute(PDO::ATTR_DRIVER_NAME), 'sqlite') ? "datetime('now')" : "NOW()"));
} catch (Exception $e) {
    // Graceful skip
}

// -------------------------------------------------------------
// STEP 1 PROCESSING: Ask for Registered Email
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_email') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token expired. Please try again.";
    } else {
        $input_email = trim($_POST['email'] ?? '');

        if (empty($input_email)) {
            $error_message = "Please enter your registered email address.";
        } elseif (!str_ends_with(strtolower($input_email), '@gmail.com')) {
            $error_message = "Email must end with @gmail.com.";
        } else {
            // Check if student exists in database
            $stmt = $pdo->prepare("SELECT id, first_name FROM students WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$input_email]);
            $student = $stmt->fetch();

            if ($student) {
                // Generate 6-digit verification code
                $code = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                
                // Expiry time: 10 minutes from now
                $expires_at = date('Y-m-d H:i:s', time() + 600);

                // Remove previous codes for this email
                $del = $pdo->prepare("DELETE FROM reset_codes WHERE email = ?");
                $del->execute([$input_email]);

                // Insert into reset_codes table
                $ins = $pdo->prepare("INSERT INTO reset_codes (email, code, expires_at) VALUES (?, ?, ?)");
                $ins->execute([$input_email, $code, $expires_at]);

                // SMTP / Email Sending Attempt
                $email_sent = false;
                if (defined('SMTP_ENABLED') && SMTP_ENABLED === true) {
                    $email_sent = send_smtp_email($input_email, $code, $student['first_name']);
                }

                $_SESSION['forgot_email'] = $input_email;
                $_SESSION['forgot_step'] = 2;
                $step = 2;
                $email = $input_email;

                if ($email_sent) {
                    $success_message = "A 6-digit verification code has been sent to your email (" . htmlspecialchars($input_email) . "). Valid for 10 minutes.";
                } else {
                    // Localhost demo notice so testing is instant
                    $_SESSION['demo_code'] = $code;
                    $demo_code_notice = $code;
                    $success_message = "Verification code generated! (Valid for 10 minutes).";
                }
            } else {
                $error_message = "No student account found registered with that email address.";
            }
        }
    }
}

// -------------------------------------------------------------
// STEP 2 PROCESSING: Ask for the 6-Digit Code
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_code') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token expired. Please try again.";
    } else {
        $entered_code = trim($_POST['code'] ?? '');

        if (empty($entered_code)) {
            $error_message = "Please enter the 6-digit verification code.";
        } else {
            // Check code and expiry in reset_codes table
            $nowStr = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("SELECT * FROM reset_codes WHERE email = ? AND code = ? AND expires_at >= ? LIMIT 1");
            $stmt->execute([$email, $entered_code, $nowStr]);
            $validCode = $stmt->fetch();

            if ($validCode) {
                $_SESSION['forgot_step'] = 3;
                $_SESSION['code_verified'] = true;
                $step = 3;
                $success_message = "Code verified successfully. Please enter your new password.";
            } else {
                $error_message = "Invalid or expired verification code. Please check your code or request a new one.";
            }
        }
    }
}

// -------------------------------------------------------------
// STEP 3 PROCESSING: Ask for New Password & Confirm Password
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_password') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token expired. Please try again.";
    } elseif (empty($_SESSION['code_verified'])) {
        $error_message = "Verification required. Please start the password reset again.";
        $step = 1;
    } else {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Same password rules as registration:
        // at least 8 chars, 1 capital, 1 small, 1 number
        if (empty($new_password)) {
            $error_message = "New password is required.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]/', $new_password)) {
            $error_message = "Password must contain at least one capital letter (A-Z).";
        } elseif (!preg_match('/[a-z]/', $new_password)) {
            $error_message = "Password must contain at least one small letter (a-z).";
        } elseif (!preg_match('/[0-9]/', $new_password)) {
            $error_message = "Password must contain at least one number (0-9).";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            // Save hashed password in students table
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE students SET password_hash = ? WHERE email = ?");
            $upd->execute([$new_hash, $email]);

            // Delete code from reset_codes
            $del = $pdo->prepare("DELETE FROM reset_codes WHERE email = ?");
            $del->execute([$email]);

            // Clear session reset variables
            unset($_SESSION['forgot_step'], $_SESSION['forgot_email'], $_SESSION['code_verified'], $_SESSION['demo_code']);

            // Redirect to Page 1 with green message "Password changed. Please login."
            $_SESSION['flash_success'] = "Password changed. Please login.";
            header("Location: index.php");
            exit();
        }
    }
}

/**
 * Optional Gmail SMTP sender using PHPMailer or socket mail
 */
function send_smtp_email($recipient, $code, $name) {
    // If PHPMailer is loaded in project, invoke it:
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = (SMTP_SECURE === 'ssl') ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
            $mail->addAddress($recipient, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Your Student Portal Password Reset Code';
            $mail->Body    = "<h3>Hello " . htmlspecialchars($name) . ",</h3><p>Your 6-digit verification code is: <strong>" . htmlspecialchars($code) . "</strong></p><p>This code will expire in 10 minutes.</p>";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    return false;
}

include __DIR__ . '/includes/header.php';
?>

<main class="auth-page-wrapper">
  <div class="portal-card">
    
    <div class="portal-card-header">
      <div class="header-icon-circle">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor">
          <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
        </svg>
      </div>
      <h1 class="portal-card-title">Reset Password</h1>
      <p class="portal-card-subtitle">Self-service student verification in 3 simple steps</p>
    </div>

    <!-- Step Progress Indicator -->
    <div class="step-indicator">
      <div class="step-bubble <?php echo ($step >= 1) ? ($step > 1 ? 'completed' : 'active') : ''; ?>">1</div>
      <div class="step-line <?php echo ($step >= 2) ? 'active' : ''; ?>"></div>
      <div class="step-bubble <?php echo ($step >= 2) ? ($step > 2 ? 'completed' : 'active') : ''; ?>">2</div>
      <div class="step-line <?php echo ($step >= 3) ? 'active' : ''; ?>"></div>
      <div class="step-bubble <?php echo ($step >= 3) ? 'active' : ''; ?>">3</div>
    </div>

    <!-- Alerts -->
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger" role="alert">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <div><?php echo htmlspecialchars($error_message); ?></div>
      </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
      <div class="alert alert-success" role="alert">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <div><?php echo htmlspecialchars($success_message); ?></div>
      </div>
    <?php endif; ?>

    <!-- On-screen code notice if email is not configured on localhost demo -->
    <?php if (!empty($demo_code_notice) && $step === 2): ?>
      <div class="alert-demo">
        <div style="font-size: 0.82rem; font-weight: 700; text-transform: uppercase;">Localhost Demo &bull; 6-Digit Code</div>
        <div class="demo-code-display"><?php echo htmlspecialchars($demo_code_notice); ?></div>
        <button type="button" class="quick-btn" onclick="document.getElementById('reset_code').value='<?php echo htmlspecialchars($demo_code_notice); ?>';">
          Auto-Fill Code Below
        </button>
      </div>
    <?php endif; ?>

    <!-- ============================================================= -->
    <!-- STEP 1: Ask for registered email                              -->
    <!-- ============================================================= -->
    <?php if ($step === 1): ?>
      <form action="forgot.php" method="POST" id="forgotForm" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="submit_email">

        <div class="form-group">
          <label for="forgot_email" class="form-label">
            Registered Gmail Address <span class="req">*</span>
          </label>
          <input 
            type="email" 
            name="email" 
            id="forgot_email" 
            class="form-input" 
            placeholder="e.g. aswani123@gmail.com" 
            value="<?php echo htmlspecialchars($email); ?>"
            required
            autocomplete="email"
          >
          <span class="hint" style="margin-top: 0.35rem; display: block;">
            We will verify your student account and generate a 10-minute code.
          </span>
        </div>

        <div style="margin-top: 1.75rem;">
          <button type="submit" class="btn btn-gold btn-block">
            Send Verification Code
          </button>
        </div>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-light);">
          <a href="index.php" style="font-weight: 700; color: var(--navy); font-size: 0.92rem;">
            &larr; Back to Student Login
          </a>
        </div>
      </form>

    <!-- ============================================================= -->
    <!-- STEP 2: Ask for the 6-Digit Code                              -->
    <!-- ============================================================= -->
    <?php elseif ($step === 2): ?>
      <form action="forgot.php" method="POST" id="forgotForm" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="submit_code">

        <div style="margin-bottom: 1.25rem; font-size: 0.92rem; color: var(--text-muted);">
          Code sent for student: <strong style="color: var(--navy);"><?php echo htmlspecialchars($email); ?></strong>
        </div>

        <div class="form-group">
          <label for="reset_code" class="form-label">
            6-Digit Verification Code <span class="req">*</span>
          </label>
          <input 
            type="text" 
            name="code" 
            id="reset_code" 
            maxlength="6"
            class="form-input" 
            placeholder="e.g. 6-digit code" 
            style="letter-spacing: 3px; font-weight: 800; font-size: 1.2rem; text-align: center;"
            required
            autocomplete="one-time-code"
          >
          <span class="hint" style="margin-top: 0.35rem; display: block;">
            Code expires in 10 minutes.
          </span>
        </div>

        <div style="margin-top: 1.75rem; display: flex; flex-direction: column; gap: 0.75rem;">
          <button type="submit" class="btn btn-gold btn-block">
            Verify Code
          </button>
          <a href="forgot.php?reset=1" class="btn btn-outline-navy btn-block">
            Use Different Email
          </a>
        </div>
      </form>

    <!-- ============================================================= -->
    <!-- STEP 3: Ask for New Password and Confirm It                   -->
    <!-- ============================================================= -->
    <?php elseif ($step === 3): ?>
      <form action="forgot.php" method="POST" id="forgotForm" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="submit_password">

        <div class="form-group">
          <label for="reset_password" class="form-label">
            New Password <span class="req">*</span>
            <span class="hint">(Min 8 chars, 1 capital, 1 small, 1 number)</span>
          </label>
          <input 
            type="password" 
            name="new_password" 
            id="reset_password" 
            class="form-input" 
            placeholder="Enter new secure password" 
            required
            autocomplete="new-password"
          >
        </div>

        <div class="form-group">
          <label for="reset_confirm" class="form-label">
            Confirm New Password <span class="req">*</span>
          </label>
          <input 
            type="password" 
            name="confirm_password" 
            id="reset_confirm" 
            class="form-input" 
            placeholder="Re-enter new password" 
            required
            autocomplete="new-password"
          >
        </div>

        <div style="margin-top: 1.75rem;">
          <button type="submit" class="btn btn-gold btn-block">
            Save New Password &amp; Login
          </button>
        </div>
      </form>
    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

