<?php
$page_title = "Create Account";
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

// Redirect if already logged in
if (!empty($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$field_errors = [];
$global_error = '';

$student_id = '';
$first_name = '';
$last_name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($submitted_token)) {
        $global_error = "Security token mismatch. Please try submitting again.";
    } else {
        $student_id = trim($_POST['student_id'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $pdo = get_pdo();

        // 1. Rule: Student ID: numbers only (0-9), no letters or symbols, 6 to 12 digits. Must be unique.
        if (empty($student_id)) {
            $field_errors['student_id'] = 'Student ID is required.';
        } elseif (!preg_match('/^[0-9]+$/', $student_id)) {
            $field_errors['student_id'] = 'Student ID must contain numbers only (0-9). No letters or symbols.';
        } elseif (strlen($student_id) < 6 || strlen($student_id) > 12) {
            $field_errors['student_id'] = 'Student ID must be between 6 and 12 digits.';
        } else {
            // Check uniqueness
            $chkId = $pdo->prepare("SELECT id FROM students WHERE student_id = ? LIMIT 1");
            $chkId->execute([$student_id]);
            if ($chkId->fetch()) {
                $field_errors['student_id'] = 'This Student ID is already registered. Please login.';
            }
        }

        // 2. Rule: First name: required, letters only.
        if (empty($first_name)) {
            $field_errors['first_name'] = 'First name is required.';
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
            $field_errors['first_name'] = 'First name must contain letters only.';
        }

        // 3. Rule: Last name: optional, but if filled it must be letters only.
        if (!empty($last_name) && !preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
            $field_errors['last_name'] = 'Last name must contain letters only.';
        }

        // 4. Rule: Email: must end with @gmail.com. Part before @gmail.com must be lowercase only (no capital letters). Must be unique.
        if (empty($email)) {
            $field_errors['email'] = 'Email is required.';
        } elseif (!str_ends_with(strtolower($email), '@gmail.com')) {
            $field_errors['email'] = 'Email must end with @gmail.com';
        } else {
            $prefix = substr($email, 0, strrpos($email, '@'));
            if (empty($prefix)) {
                $field_errors['email'] = 'Email prefix cannot be empty.';
            } elseif (preg_match('/[A-Z]/', $prefix)) {
                $field_errors['email'] = 'No capital letters allowed before @gmail.com. Please use lowercase only.';
            } elseif (!preg_match('/^[a-z0-9._%+-]+@gmail\.com$/', $email)) {
                $field_errors['email'] = 'Please enter a valid lowercase Gmail address.';
            } else {
                // Check uniqueness
                $chkMail = $pdo->prepare("SELECT id FROM students WHERE email = ? LIMIT 1");
                $chkMail->execute([$email]);
                if ($chkMail->fetch()) {
                    $field_errors['email'] = 'This Gmail address is already registered. Please login.';
                }
            }
        }

        // 5. Rule: Password: at least 8 characters, must contain at least one capital letter, one small letter and one number (0-9).
        if (empty($password)) {
            $field_errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $field_errors['password'] = 'Password must be at least 8 characters long.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $field_errors['password'] = 'Password must contain at least one capital letter (A-Z).';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $field_errors['password'] = 'Password must contain at least one small letter (a-z).';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $field_errors['password'] = 'Password must contain at least one number (0-9).';
        }

        // 6. Rule: Confirm password: must match.
        if (empty($confirm_password)) {
            $field_errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($password !== $confirm_password) {
            $field_errors['confirm_password'] = 'Passwords do not match.';
        }

        // If no errors, insert into database
        if (empty($field_errors)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $first_name, $last_name, $email, $password_hash]);

            $new_id = $pdo->lastInsertId();

            // Store registration details in session for Page 3 (success.php)
            $_SESSION['registration_success'] = [
                'student_id' => $student_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                'created_at' => date('F d, Y - h:i A')
            ];

            // Set login session for Page 4
            $_SESSION['student_id'] = $student_id;
            $_SESSION['user_id'] = $new_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['email'] = $email;

            header("Location: success.php");
            exit();
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="auth-page-wrapper">
  <div class="portal-card portal-card-lg">
    
    <div class="portal-card-header">
      <div class="header-icon-circle">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor">
          <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18C5 19.5 8.13 21.36 12 21.36C15.87 21.36 19 19.5 19 17.18V13.18L12 17L5 13.18Z"/>
        </svg>
      </div>
      <h1 class="portal-card-title">Create Account</h1>
      <p class="portal-card-subtitle">Fill in your information to register for the student portal</p>
    </div>

    <?php if (!empty($global_error)): ?>
      <div class="alert alert-danger" role="alert">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <div><?php echo htmlspecialchars($global_error); ?></div>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST" id="registerForm" novalidate>
      <?php echo csrf_field(); ?>

      <!-- 1. Student ID -->
      <div class="form-group">
        <label for="reg_student_id" class="form-label">
          Student ID <span class="req">*</span>
          <span class="hint">(6 to 12 digits, numbers only)</span>
        </label>
        <input 
          type="text" 
          name="student_id" 
          id="reg_student_id" 
          class="form-input <?php echo isset($field_errors['student_id']) ? 'is-invalid' : ''; ?>" 
          placeholder="e.g. 20240915" 
          value="<?php echo htmlspecialchars($student_id); ?>"
          required
        >
        <?php if (isset($field_errors['student_id'])): ?>
          <span class="field-error"><?php echo htmlspecialchars($field_errors['student_id']); ?></span>
        <?php endif; ?>
      </div>

      <!-- 2 & 3. First Name & Last Name (Side by Side) -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label for="reg_first_name" class="form-label">
            First Name <span class="req">*</span>
          </label>
          <input 
            type="text" 
            name="first_name" 
            id="reg_first_name" 
            class="form-input <?php echo isset($field_errors['first_name']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. Aswani" 
            value="<?php echo htmlspecialchars($first_name); ?>"
            required
          >
          <?php if (isset($field_errors['first_name'])): ?>
            <span class="field-error"><?php echo htmlspecialchars($field_errors['first_name']); ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="reg_last_name" class="form-label">
            Last Name <span class="hint" style="color: var(--text-muted);">(Optional)</span>
          </label>
          <input 
            type="text" 
            name="last_name" 
            id="reg_last_name" 
            class="form-input <?php echo isset($field_errors['last_name']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. Ravi" 
            value="<?php echo htmlspecialchars($last_name); ?>"
          >
          <?php if (isset($field_errors['last_name'])): ?>
            <span class="field-error"><?php echo htmlspecialchars($field_errors['last_name']); ?></span>
          <?php endif; ?>
        </div>
      </div>

      <!-- 4. Email (@gmail.com, lowercase only before @) -->
      <div class="form-group">
        <label for="reg_email" class="form-label">
          Gmail Address <span class="req">*</span>
          <span class="hint">(@gmail.com, lowercase only, no caps lock)</span>
        </label>
        <input 
          type="email" 
          name="email" 
          id="reg_email" 
          class="form-input <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>" 
          placeholder="e.g. aswani123@gmail.com" 
          value="<?php echo htmlspecialchars($email); ?>"
          required
        >
        <?php if (isset($field_errors['email'])): ?>
          <span class="field-error"><?php echo htmlspecialchars($field_errors['email']); ?></span>
        <?php endif; ?>
      </div>

      <!-- 5. Password -->
      <div class="form-group">
        <label for="reg_password" class="form-label">
          Password <span class="req">*</span>
          <span class="hint">(Min 8 chars, 1 capital, 1 small, 1 number)</span>
        </label>
        <input 
          type="password" 
          name="password" 
          id="reg_password" 
          class="form-input <?php echo isset($field_errors['password']) ? 'is-invalid' : ''; ?>" 
          placeholder="Create a strong password" 
          required
        >
        <?php if (isset($field_errors['password'])): ?>
          <span class="field-error"><?php echo htmlspecialchars($field_errors['password']); ?></span>
        <?php endif; ?>
      </div>

      <!-- 6. Confirm Password -->
      <div class="form-group">
        <label for="reg_confirm_password" class="form-label">
          Confirm Password <span class="req">*</span>
        </label>
        <input 
          type="password" 
          name="confirm_password" 
          id="reg_confirm_password" 
          class="form-input <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
          placeholder="Re-enter your password" 
          required
        >
        <?php if (isset($field_errors['confirm_password'])): ?>
          <span class="field-error"><?php echo htmlspecialchars($field_errors['confirm_password']); ?></span>
        <?php endif; ?>
      </div>

      <!-- Submit Button -->
      <div style="margin-top: 1.75rem;">
        <button type="submit" class="btn btn-gold btn-block">
          Create Account
        </button>
      </div>

      <!-- Link back to Page 1 -->
      <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-light); font-size: 0.92rem; color: var(--text-muted);">
        Already registered? <a href="index.php" style="font-weight: 700; color: var(--navy); text-decoration: underline;">Login</a>
      </div>

    </form>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
