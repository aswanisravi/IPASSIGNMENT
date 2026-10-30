<?php
$page_title = "Register New Account";
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$register_status = '';
$error_message = '';
$success_message = '';
$field_errors = [];

$name = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation 1: Name
    if (empty($name)) {
        $field_errors['name'] = 'Full Name is required.';
    } elseif (strlen($name) < 3) {
        $field_errors['name'] = 'Name must be at least 3 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $field_errors['name'] = 'Name can only contain letters and spaces.';
    }

    // Validation 2: Email
    if (empty($email)) {
        $field_errors['email'] = 'Email ID is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = 'Please enter a valid email address.';
    } else {
        // Check uniqueness in database
        $existing = db_fetch_one("SELECT id FROM users WHERE LOWER(email) = LOWER(?)", [$email]);
        if ($existing) {
            $field_errors['email'] = 'An account with this Email ID already exists. Please log in.';
        }
    }

    // Validation 3: Phone
    $cleaned_phone = preg_replace('/[\s-]/', '', $phone);
    if (empty($cleaned_phone)) {
        $field_errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^\d{10}$/', $cleaned_phone)) {
        $field_errors['phone'] = 'Please enter a valid 10-digit mobile number.';
    }

    // Validation 4: Password
    if (empty($password)) {
        $field_errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $field_errors['password'] = 'Password must be at least 6 characters.';
    }

    // Validation 5: Confirm Password
    if (empty($confirm_password)) {
        $field_errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $field_errors['confirm_password'] = 'Passwords do not match.';
    }

    // If all validations pass, save to database
    if (empty($field_errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'Student';

        $result = db_execute(
            "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)",
            [$name, $email, $cleaned_phone, $hashed_password, $role]
        );

        if ($result['success']) {
            $_SESSION['flash_message'] = "Registration successful! You can now log in using your registered Name, Email, and Password.";
            header("Location: login.php");
            exit();
        } else {
            $register_status = 'error';
            $error_message = "Database error: Unable to create account. " . ($result['error'] ?? '');
        }
    } else {
        $register_status = 'error';
        $error_message = "Please correct the highlighted validation errors below.";
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="auth-wrapper">
  <div class="form-card <?php echo ($register_status === 'error') ? 'shake' : ''; ?>">
    <div class="form-header">
      <div class="logo-icon" style="margin: 0 auto 1rem; width: 48px; height: 48px; font-size: 1.5rem;">&#128101;</div>
      <h2>Create an Account</h2>
      <p>Register your student profile with full backend and form validation.</p>
    </div>

    <?php if ($register_status === 'error'): ?>
      <div class="alert alert-danger" role="alert">
        <span style="font-size: 1.25rem;">&#10060;</span>
        <div><strong>Validation Error:</strong> <?php echo $error_message; ?></div>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST" id="registerForm" novalidate>
      
      <!-- Full Name -->
      <div class="form-group">
        <label for="regName" class="form-label">Full Name <span class="required-star">*</span></label>
        <div class="input-wrapper">
          <input 
            type="text" 
            name="name" 
            id="regName" 
            class="form-input <?php echo isset($field_errors['name']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. Alex Morgan"
            value="<?php echo htmlspecialchars($name); ?>"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['name']) ? 'error' : ''; ?>">
          <?php echo $field_errors['name'] ?? ''; ?>
        </div>
      </div>

      <!-- Email ID -->
      <div class="form-group">
        <label for="regEmail" class="form-label">Email ID <span class="required-star">*</span></label>
        <div class="input-wrapper">
          <input 
            type="email" 
            name="email" 
            id="regEmail" 
            class="form-input <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. alex@example.com"
            value="<?php echo htmlspecialchars($email); ?>"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['email']) ? 'error' : ''; ?>">
          <?php echo $field_errors['email'] ?? ''; ?>
        </div>
      </div>

      <!-- Phone Number -->
      <div class="form-group">
        <label for="regPhone" class="form-label">Phone Number (10 Digits) <span class="required-star">*</span></label>
        <div class="input-wrapper">
          <input 
            type="tel" 
            name="phone" 
            id="regPhone" 
            class="form-input <?php echo isset($field_errors['phone']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. 9876543210"
            value="<?php echo htmlspecialchars($phone); ?>"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['phone']) ? 'error' : ''; ?>">
          <?php echo $field_errors['phone'] ?? ''; ?>
        </div>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="regPassword" class="form-label">Password (Min 6 chars) <span class="required-star">*</span></label>
        <div class="input-wrapper">
          <input 
            type="password" 
            name="password" 
            id="regPassword" 
            class="form-input <?php echo isset($field_errors['password']) ? 'is-invalid' : ''; ?>" 
            placeholder="Create a strong password"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['password']) ? 'error' : ''; ?>">
          <?php echo $field_errors['password'] ?? ''; ?>
        </div>
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <label for="regConfirmPassword" class="form-label">Confirm Password <span class="required-star">*</span></label>
        <div class="input-wrapper">
          <input 
            type="password" 
            name="confirm_password" 
            id="regConfirmPassword" 
            class="form-input <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
            placeholder="Re-type password"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['confirm_password']) ? 'error' : ''; ?>">
          <?php echo $field_errors['confirm_password'] ?? ''; ?>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="submit" class="btn btn-gold btn-block" style="padding: 0.8rem; font-size: 1rem;">
          &#10004; Submit Registration
        </button>
        <a href="login.php" class="btn btn-navy btn-block" style="padding: 0.75rem;">
          Already Registered? Login
        </a>
      </div>
    </form>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

