<?php
$page_title = "Registration Success";
require_once __DIR__ . '/includes/db.php';

// Session guard: This page must only open right after registering; otherwise redirect to Page 1.
if (empty($_SESSION['registration_success'])) {
    header("Location: index.php");
    exit();
}

$registered_student = $_SESSION['registration_success'];

// Construct full name (first and last name)
$first_name = $registered_student['first_name'] ?? '';
$last_name = $registered_student['last_name'] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
$student_id = $registered_student['student_id'] ?? '';
$email = $registered_student['email'] ?? '';

include __DIR__ . '/includes/header.php';
?>

<main class="auth-page-wrapper">
  <div class="success-card">
    
    <!-- Green Checkmark Icon Badge -->
    <div class="success-icon-badge">
      <svg viewBox="0 0 24 24" width="38" height="38" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
      </svg>
    </div>

    <!-- Green Success Heading -->
    <div style="text-align: center; margin-bottom: 1.5rem;">
      <h1 style="color: var(--success); font-size: 1.85rem; font-weight: 800; margin-bottom: 0.35rem;">
        You have registered successfully.
      </h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Your student profile has been verified and registered in the portal.
      </p>
    </div>

    <!-- Clean Card Showing Student Details (Never showing password) -->
    <table class="profile-summary-table">
      <tr>
        <td class="col-label">Student ID:</td>
        <td class="col-value"><code style="font-family: monospace; font-size: 1.05rem;"><?php echo htmlspecialchars($student_id); ?></code></td>
      </tr>
      <tr>
        <td class="col-label">Full Name:</td>
        <td class="col-value"><?php echo htmlspecialchars($full_name); ?></td>
      </tr>
      <tr>
        <td class="col-label">Registered Email:</td>
        <td class="col-value"><?php echo htmlspecialchars($email); ?></td>
      </tr>
      <tr>
        <td class="col-label">Account Status:</td>
        <td class="col-value" style="color: var(--success); font-weight: 700;">Active Student Account</td>
      </tr>
    </table>

    <!-- Gold Button "Continue to Dashboard" going to Page 4 -->
    <div>
      <a href="dashboard.php" class="btn btn-gold btn-block" style="padding: 0.85rem; font-size: 1.05rem;">
        Continue to Dashboard &rarr;
      </a>
    </div>

  </div>
</main>

<?php 
// Once displayed, we can keep or clean the registration session so it doesn't linger indefinitely
include __DIR__ . '/includes/footer.php'; 
?>
