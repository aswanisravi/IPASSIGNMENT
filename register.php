<?php
$page_title = "Create Student Account - Registration";
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$register_status = '';
$error_message = '';
$field_errors = [];

$student_id = '';
$first_name = '';
$last_name = '';
$email = '';
$address = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Student ID: Full numbers only, all should numbers
    if (empty($student_id)) {
        $field_errors['student_id'] = 'Student ID is required.';
    } elseif (!preg_match('/^[0-9]+$/', $student_id)) {
        $field_errors['student_id'] = 'Student ID must contain numbers only (0-9).';
    } else {
        $existing_id = db_fetch_one("SELECT id FROM users WHERE student_id = ?", [$student_id]);
        if ($existing_id) {
            $field_errors['student_id'] = 'This Student ID is already registered. Please sign in instead.';
        }
    }

    // 2. Name: First Name is mandatory, Last Name is optional
    if (empty($first_name)) {
        $field_errors['first_name'] = 'First Name is mandatory.';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $field_errors['first_name'] = 'First Name should contain letters only.';
    }

    if (!empty($last_name) && !preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $field_errors['last_name'] = 'Last Name should contain letters only.';
    }

    // 3. Email: @gmail.com is necessary. Before that, name should be written with NO CAPS LOCK
    if (empty($email)) {
        $field_errors['email'] = 'Email address is required.';
    } elseif (!str_ends_with(strtolower($email), '@gmail.com')) {
        $field_errors['email'] = '@gmail.com is required. Email must end with @gmail.com.';
    } else {
        $email_parts = explode('@', $email);
        $username_part = $email_parts[0];
        if (preg_match('/[A-Z]/', $username_part)) {
            $field_errors['email'] = 'No caps lock allowed! Name before @gmail.com must be lowercase only.';
        } elseif (!preg_match('/^[a-z0-9._%+-]+@gmail\.com$/', $email)) {
            $field_errors['email'] = 'Please enter a valid lowercase Gmail address.';
        } else {
            $existing_email = db_fetch_one("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing_email) {
                $field_errors['email'] = 'This Gmail address is already registered. Please sign in.';
            }
        }
    }

    // 4. Address
    if (empty($address)) {
        $field_errors['address'] = 'Residential or Campus Address is required.';
    }

    // 5. Password: Alphanumerical, numbers up to 0 to 9, capital letter and small letter
    if (empty($password)) {
        $field_errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $field_errors['password'] = 'Password must be at least 6 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $field_errors['password'] = 'Password must contain at least one CAPITAL letter (A-Z).';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $field_errors['password'] = 'Password must contain at least one SMALL letter (a-z).';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $field_errors['password'] = 'Password must contain at least one NUMBER (0-9).';
    }

    // 6. Confirm Password
    if (empty($confirm_password)) {
        $field_errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $field_errors['confirm_password'] = 'Passwords do not match.';
    }

    // If all validations pass, save to database and go to registration success page
    if (empty($field_errors)) {
        $full_name = trim($first_name . ' ' . $last_name);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'Student';

        $result = db_execute(
            "INSERT INTO users (student_id, first_name, last_name, name, email, address, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$student_id, $first_name, $last_name, $full_name, $email, $address, $phone, $hashed_password, $role]
        );

        if ($result['success']) {
            $new_user_id = $result['insert_id'];
            
            // Set session so the student is immediately authenticated
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['student_id'] = $student_id;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_address'] = $address;
            $_SESSION['user_role'] = $role;

            // Store summary in session for the Registration Success display
            $_SESSION['just_registered'] = [
                'student_id' => $student_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'full_name' => $full_name,
                'email' => $email,
                'address' => $address,
                'registered_at' => date('F d, Y - h:i A')
            ];

            // Redirect to next page showing registration success details
            header("Location: registration_success.php");
            exit();
        } else {
            $register_status = 'error';
            $error_message = "Database error: Could not complete registration. " . ($result['error'] ?? '');
        }
    } else {
        $register_status = 'error';
        $error_message = "Please correct the highlighted validation requirements below.";
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="auth-wrapper" style="padding: 3rem 1.5rem; background-color: #f8fafc;">
  <div class="form-card <?php echo ($register_status === 'error') ? 'shake' : ''; ?>" style="max-width: 580px;">
    
    <div class="form-header">
      <div class="logo-icon" style="margin: 0 auto 1rem; width: 50px; height: 50px; font-size: 1.6rem;">&#127891;</div>
      <h2>Create Student Account</h2>
      <p>Register with your official Student ID and verified Gmail address.</p>
    </div>

    <!-- Rule Summary Info Badge -->
    <div style="background: #fffdf5; border: 1px solid rgba(201, 162, 39, 0.4); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.5rem; font-size: 0.84rem; color: #475569; line-height: 1.5;">
      <strong style="color: #0B1F4B;">&#128221; Account Registration Criteria:</strong>
      <ul style="margin: 0.4rem 0 0 1.25rem;">
        <li><strong>Student ID:</strong> Numbers only (e.g. 202409).</li>
        <li><strong>Name:</strong> First Name is mandatory; Last Name is optional.</li>
        <li><strong>Email:</strong> Must end with <code>@gmail.com</code> (no caps lock in username).</li>
        <li><strong>Password:</strong> Numbers (0-9), at least one capital letter &amp; one small letter.</li>
      </ul>
    </div>

    <?php if ($register_status === 'error'): ?>
      <div class="alert alert-danger" role="alert">
        <span style="font-size: 1.25rem;">&#10060;</span>
        <div><strong>Validation Error:</strong> <?php echo $error_message; ?></div>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST" id="studentRegisterForm" novalidate>
      
      <!-- 1. Student ID (Full numbers only) -->
      <div class="form-group">
        <label for="regStudentId" class="form-label">
          Student ID Number <span class="required-star">*</span>
          <span class="label-hint">(All digits 0-9)</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="text" 
            name="student_id" 
            id="regStudentId" 
            class="form-input <?php echo isset($field_errors['student_id']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. 1005 or 2024058"
            value="<?php echo htmlspecialchars($student_id); ?>"
            pattern="[0-9]+"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['student_id']) ? 'error' : ''; ?>" id="regStudentIdFeedback">
          <?php echo $field_errors['student_id'] ?? ''; ?>
        </div>
      </div>

      <!-- 2. First Name & Last Name in 2 Columns -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <!-- First Name (Mandatory) -->
        <div class="form-group">
          <label for="regFirstName" class="form-label">
            First Name <span class="required-star">*</span>
            <span class="label-hint">(Mandatory)</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="text" 
              name="first_name" 
              id="regFirstName" 
              class="form-input <?php echo isset($field_errors['first_name']) ? 'is-invalid' : ''; ?>" 
              placeholder="e.g. Alex"
              value="<?php echo htmlspecialchars($first_name); ?>"
              required
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['first_name']) ? 'error' : ''; ?>">
            <?php echo $field_errors['first_name'] ?? ''; ?>
          </div>
        </div>

        <!-- Last Name (Optional) -->
        <div class="form-group">
          <label for="regLastName" class="form-label">
            Last Name 
            <span class="label-hint" style="color: #64748b;">(Optional)</span>
          </label>
          <div class="input-wrapper">
            <input 
              type="text" 
              name="last_name" 
              id="regLastName" 
              class="form-input <?php echo isset($field_errors['last_name']) ? 'is-invalid' : ''; ?>" 
              placeholder="e.g. Morgan"
              value="<?php echo htmlspecialchars($last_name); ?>"
            >
          </div>
          <div class="validation-feedback <?php echo isset($field_errors['last_name']) ? 'error' : ''; ?>">
            <?php echo $field_errors['last_name'] ?? ''; ?>
          </div>
        </div>
      </div>

      <!-- 3. Email Address (@gmail.com necessary, NO CAPS LOCK before @) -->
      <div class="form-group">
        <label for="regEmail" class="form-label">
          Gmail Address <span class="required-star">*</span>
          <span class="label-hint">(@gmail.com required, no caps lock)</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="email" 
            name="email" 
            id="regEmail" 
            class="form-input <?php echo isset($field_errors['email']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. alex.morgan@gmail.com"
            value="<?php echo htmlspecialchars($email); ?>"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['email']) ? 'error' : ''; ?>" id="regEmailFeedback">
          <?php echo $field_errors['email'] ?? ''; ?>
        </div>
      </div>

      <!-- 4. Residential / Campus Address -->
      <div class="form-group">
        <label for="regAddress" class="form-label">
          Residential / Campus Address <span class="required-star">*</span>
        </label>
        <div class="input-wrapper">
          <textarea 
            name="address" 
            id="regAddress" 
            rows="2" 
            class="form-textarea <?php echo isset($field_errors['address']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. Block C, Student Hostel / Green Park Campus"
            required
          ><?php echo htmlspecialchars($address); ?></textarea>
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['address']) ? 'error' : ''; ?>">
          <?php echo $field_errors['address'] ?? ''; ?>
        </div>
      </div>

      <!-- 5. Set Password (Alphanumeric, numbers 0-9, capital letter and small letter) -->
      <div class="form-group">
        <label for="regPassword" class="form-label">
          Set Password <span class="required-star">*</span>
          <span class="label-hint">(0-9, capital letter &amp; small letter)</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="password" 
            name="password" 
            id="regPassword" 
            class="form-input <?php echo isset($field_errors['password']) ? 'is-invalid' : ''; ?>" 
            placeholder="e.g. Password123"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['password']) ? 'error' : ''; ?>" id="regPasswordFeedback">
          <?php echo $field_errors['password'] ?? ''; ?>
        </div>
      </div>

      <!-- 6. Confirm Password -->
      <div class="form-group">
        <label for="regConfirmPassword" class="form-label">
          Confirm Password <span class="required-star">*</span>
        </label>
        <div class="input-wrapper">
          <input 
            type="password" 
            name="confirm_password" 
            id="regConfirmPassword" 
            class="form-input <?php echo isset($field_errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
            placeholder="Re-enter password"
            required
          >
        </div>
        <div class="validation-feedback <?php echo isset($field_errors['confirm_password']) ? 'error' : ''; ?>" id="regConfirmFeedback">
          <?php echo $field_errors['confirm_password'] ?? ''; ?>
        </div>
      </div>

      <!-- Submit & Navigation Buttons -->
      <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="submit" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1.05rem;">
          &#10004; Complete Registration &amp; View Profile
        </button>
        <a href="index.php" class="btn btn-outline-navy btn-block" style="padding: 0.75rem;">
          Already have an account? Sign In
        </a>
      </div>

    </form>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
