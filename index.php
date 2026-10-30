<?php
$page_title = "Student Login Portal - Home";
require_once __DIR__ . '/config/db.php';

$login_status = ''; // 'valid', 'wrong_password', 'not_found', 'error'
$error_message = '';
$success_message = '';
$entered_student_id = '';
$entered_name = '';
$field_errors = [];

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_student_id = trim($_POST['student_id'] ?? '');
    $entered_name = trim($_POST['name'] ?? '');
    $entered_password = $_POST['password'] ?? '';

    // 1. Validate Student ID (Numbers only)
    if (empty($entered_student_id)) {
        $field_errors['student_id'] = 'Student ID is required.';
    } elseif (!preg_match('/^[0-9]+$/', $entered_student_id)) {
        $field_errors['student_id'] = 'Student ID must contain numbers only.';
    }

    // 2. Validate Password
    if (empty($entered_password)) {
        $field_errors['password'] = 'Password is required.';
    }

    // If initial format is valid, query database
    if (empty($field_errors)) {
        // Query user by student_id or email
        $user = db_fetch_one("SELECT * FROM users WHERE student_id = ? OR email = ?", [$entered_student_id, $entered_student_id]);

        if ($user) {
            // Verify Password
            $password_valid = password_verify($entered_password, $user['password']) || ($entered_password === $user['password']);

            if ($password_valid) {
                // If Name was provided, check if it matches optionally
                if (!empty($entered_name)) {
                    $user_first = strtolower(trim($user['first_name'] ?? ''));
                    $user_full = strtolower(trim($user['name'] ?? ''));
                    $entered_clean = strtolower($entered_name);

                    // Allow match if first name or full name matches
                    if ($user_first !== $entered_clean && !str_contains($user_full, $entered_clean)) {
                        // Soft warning or allow: since user said "if the person is already registered, then just ask him about student ID and password"
                    }
                }

                $login_status = 'valid';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['first_name'] = $user['first_name'] ?? $user['name'];
                $_SESSION['last_name'] = $user['last_name'] ?? '';
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_address'] = $user['address'] ?? 'Not Specified';
                $_SESSION['user_role'] = $user['role'] ?? 'Student';
                $_SESSION['login_time'] = time();

                $success_message = "Login successful! Welcome back, <strong>" . htmlspecialchars($user['name']) . "</strong>. Redirecting to your student dashboard...";
            } else {
                // Password is wrong condition
                $login_status = 'wrong_password';
                $error_message = "Wrong password. Please verify your password and try again, or use Forgot Password.";
                $field_errors['password'] = "Wrong password.";
            }
        } else {
            // Student ID not found in database
            $login_status = 'not_found';
            $error_message = "Student ID not found in the database. If you do not have an account, please click 'Create an account' below.";
            $field_errors['student_id'] = "Unregistered Student ID.";
        }
    } else {
        $login_status = 'error';
        $error_message = "Please correct the highlighted errors below.";
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main style="background-color: #f8fafc; padding-bottom: 4rem;">
  
  <!-- Homepage Hero Banner with Centered Login & University Logo -->
  <section class="portal-hero">
    <div class="container">
      
      <!-- Brand Crest & Portal Header -->
      <div class="portal-header-area">
        <div class="portal-crest-circle">
          &#127891;
        </div>
        <h1 class="portal-main-heading">
          Student <span style="color: #C9A227;">Login Portal</span>
        </h1>
        <p class="portal-sub-heading">
          Official University Academic & Student Information Portal
        </p>
      </div>

      <!-- Main Login Box (On Homepage) -->
      <div class="login-box-container">
        <div class="student-login-card <?php echo ($login_status === 'wrong_password' || $login_status === 'not_found' || $login_status === 'error') ? 'shake' : ''; ?>">
          
          <div class="login-card-header">
            <div class="login-icon-badge">&#128272;</div>
            <h2 class="login-title">Sign In to Your Account</h2>
            <p class="login-subtitle">
              Enter your <strong>Student ID</strong> and <strong>Password</strong> to access your profile.
            </p>
          </div>

          <!-- Quick Test / Demo Credentials Box for Testing -->
          <div class="demo-login-box">
            <div class="demo-login-header">
              <span>&#9889; Demo Credentials for Evaluation</span>
              <button type="button" class="btn btn-navy btn-sm" onclick="fillStudentDemo('1001', 'Alex', 'Password123')">
                Auto-Fill Demo
              </button>
            </div>
            <div class="demo-login-body">
              <div><strong>Student ID:</strong> <code>1001</code> | <strong>Name:</strong> <code>Alex</code> | <strong>Pass:</strong> <code>Password123</code></div>
              <div style="margin-top: 0.35rem;">
                <button type="button" class="demo-link-btn" onclick="fillStudentDemo('1001', 'Alex', 'WrongPass99')">
                  Test Wrong Password
                </button>
                &bull;
                <button type="button" class="demo-link-btn" onclick="fillStudentDemo('99999', 'Unknown', 'Password123')">
                  Test Unregistered ID
                </button>
              </div>
            </div>
          </div>

          <!-- Alert Banners: Valid & Invalid States -->
          <?php if ($login_status === 'valid'): ?>
            <div class="alert alert-success" role="alert">
              <span style="font-size: 1.3rem;">&#9989;</span>
              <div>
                <strong>LOGIN SUCCESSFUL!</strong><br>
                <?php echo $success_message; ?>
              </div>
            </div>
            <script>
              setTimeout(function() {
                window.location.href = 'dashboard.php';
              }, 1200);
            </script>
          <?php elseif ($login_status === 'wrong_password'): ?>
            <div class="alert alert-danger" role="alert">
              <span style="font-size: 1.3rem;">&#10060;</span>
              <div>
                <strong>Wrong password!</strong><br>
                <?php echo $error_message; ?>
              </div>
            </div>
          <?php elseif ($login_status === 'not_found'): ?>
            <div class="alert alert-danger" role="alert">
              <span style="font-size: 1.3rem;">&#10060;</span>
              <div>
                <strong>Account Not Found!</strong><br>
                <?php echo $error_message; ?>
              </div>
            </div>
          <?php elseif ($login_status === 'error'): ?>
            <div class="alert alert-danger" role="alert">
              <span style="font-size: 1.3rem;">&#10060;</span>
              <div>
                <strong>Validation Error:</strong> <?php echo $error_message; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-info">
              <span>&#8505;</span>
              <div><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?></div>
            </div>
          <?php endif; ?>

          <!-- Login Form asking Student ID, Name, and Password -->
          <form action="index.php" method="POST" id="homeLoginForm" novalidate>
            
            <!-- 1. Student ID (Numbers only) -->
            <div class="form-group">
              <label for="studentId" class="form-label">
                Student ID <span class="required-star">*</span>
                <span class="label-hint">(Numbers only)</span>
              </label>
              <div class="input-wrapper">
                <input 
                  type="text" 
                  name="student_id" 
                  id="studentId" 
                  class="form-input <?php echo isset($field_errors['student_id']) ? 'is-invalid' : ($login_status === 'valid' ? 'is-valid' : ''); ?>" 
                  placeholder="e.g. 1001 or 202409"
                  value="<?php echo htmlspecialchars($entered_student_id); ?>"
                  pattern="[0-9]+"
                  required
                  autocomplete="username"
                >
              </div>
              <div class="validation-feedback <?php echo isset($field_errors['student_id']) ? 'error' : ''; ?>" id="studentIdFeedback">
                <?php echo $field_errors['student_id'] ?? ''; ?>
              </div>
            </div>

            <!-- 2. Student Name (Optional for existing registered students) -->
            <div class="form-group">
              <label for="studentName" class="form-label">
                Student Name
                <span class="label-hint">(First Name or Full Name)</span>
              </label>
              <div class="input-wrapper">
                <input 
                  type="text" 
                  name="name" 
                  id="studentName" 
                  class="form-input" 
                  placeholder="e.g. Alex (Optional for existing users)"
                  value="<?php echo htmlspecialchars($entered_name); ?>"
                  autocomplete="name"
                >
              </div>
            </div>

            <!-- 3. Password -->
            <div class="form-group">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                <label for="studentPassword" class="form-label" style="margin-bottom: 0;">
                  Password <span class="required-star">*</span>
                </label>
                <!-- Forgot Password Option Link -->
                <a href="forgot_password.php" class="forgot-link">
                  Forgot password?
                </a>
              </div>
              <div class="input-wrapper">
                <input 
                  type="password" 
                  name="password" 
                  id="studentPassword" 
                  class="form-input <?php echo isset($field_errors['password']) ? 'is-invalid' : ($login_status === 'valid' ? 'is-valid' : ''); ?>" 
                  placeholder="Enter your password"
                  required
                  autocomplete="current-password"
                >
              </div>
              <div class="validation-feedback <?php echo isset($field_errors['password']) ? 'error' : ''; ?>" id="passwordFeedback">
                <?php echo $field_errors['password'] ?? ''; ?>
              </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.85rem;">
              <button type="submit" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1.05rem;">
                &#128273; Sign In / Student Login
              </button>
            </div>

            <!-- If Not Signed, Create Account Section -->
            <div class="create-account-prompt">
              <span>Not registered yet?</span>
              <a href="register.php" class="create-account-link">
                Create an account
              </a>
            </div>

          </form>

        </div>
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
          <p class="student-step-text">Create your account with your student ID, name, email and a strong password.</p>
        </div>
        <div class="student-step-card">
          <div class="student-step-number">2</div>
          <p class="student-step-text">Log in using your registered Student ID and password.</p>
        </div>
        <div class="student-step-card">
          <div class="student-step-number">3</div>
          <p class="student-step-text">Open your student dashboard and view your digital ID card.</p>
        </div>
      </div>
      <div class="student-steps-action">
        <a href="#studentId" class="btn btn-gold" onclick="document.getElementById('studentId').focus();">
          Student Login Now
        </a>
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
            Use at least 6 characters with alphanumeric digits (0-9), at least one capital letter (A-Z), and at least one small letter (a-z). Never share your password.
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

<script>
function fillStudentDemo(id, name, pass) {
  document.getElementById('studentId').value = id;
  document.getElementById('studentName').value = name;
  document.getElementById('studentPassword').value = pass;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
