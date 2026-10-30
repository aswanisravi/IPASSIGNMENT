<?php
$page_title = "Registration Successful - Student Profile";
require_once __DIR__ . '/config/db.php';

// Authentication check: Must have registered or be logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['just_registered'])) {
    header("Location: index.php");
    exit();
}

// Retrieve details from session or database
$user_id = $_SESSION['user_id'] ?? null;
$user_data = [];

if (isset($_SESSION['just_registered'])) {
    $user_data = $_SESSION['just_registered'];
} elseif ($user_id) {
    $db_user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$user_id]);
    if ($db_user) {
        $user_data = [
            'student_id' => $db_user['student_id'] ?? 'N/A',
            'first_name' => $db_user['first_name'] ?? $db_user['name'],
            'last_name' => $db_user['last_name'] ?? '',
            'full_name' => $db_user['name'],
            'email' => $db_user['email'],
            'address' => $db_user['address'] ?? 'Not Specified',
            'registered_at' => date('F d, Y - h:i A', strtotime($db_user['created_at']))
        ];
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main style="background-color: #f8fafc; padding: 4rem 1.5rem; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
  <div class="container" style="max-width: 680px;">
    
    <div class="card" style="border-top: 5px solid #C9A227; box-shadow: 0 10px 30px rgba(11, 31, 75, 0.08); padding: 2.75rem 2.25rem;">
      
      <!-- Success Icon & Celebration Title -->
      <div style="text-align: center; margin-bottom: 2rem;">
        <div style="width: 72px; height: 72px; background: #ecfdf5; border: 2px solid #10b981; color: #10b981; border-radius: 50%; font-size: 2.2rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
          &#10004;
        </div>
        <h1 style="color: #0B1F4B; font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">
          You Have Registered Successfully!
        </h1>
        <p style="color: #64748b; font-size: 1rem; margin: 0;">
          Your student account is now active and stored in the university database.
        </p>
      </div>

      <!-- Registration Details Summary Box -->
      <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 2rem;">
        <div style="background: #0B1F4B; color: #ffffff; padding: 0.85rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-weight: 700; font-size: 0.95rem; letter-spacing: 0.5px;">
            &#128100; Official Student Registration Record
          </span>
          <span style="background: #C9A227; color: #0B1F4B; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
            VERIFIED
          </span>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B; width: 35%;">Student ID:</td>
            <td style="padding: 0.85rem 1.25rem; color: #1e293b; font-family: monospace; font-size: 1.05rem; font-weight: 700;">
              <?php echo htmlspecialchars($user_data['student_id'] ?? 'N/A'); ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">First Name:</td>
            <td style="padding: 0.85rem 1.25rem; color: #1e293b;">
              <?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>
              <small style="color: #64748b; font-size: 0.8rem;">(Mandatory)</small>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">Last Name:</td>
            <td style="padding: 0.85rem 1.25rem; color: #1e293b;">
              <?php echo !empty($user_data['last_name']) ? htmlspecialchars($user_data['last_name']) : '<em style="color:#94a3b8;">Not provided (Optional)</em>'; ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">Full Name:</td>
            <td style="padding: 0.85rem 1.25rem; color: #0B1F4B; font-weight: 700;">
              <?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">Gmail Address:</td>
            <td style="padding: 0.85rem 1.25rem; color: #1e293b;">
              <code><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></code>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">Address:</td>
            <td style="padding: 0.85rem 1.25rem; color: #1e293b;">
              <?php echo htmlspecialchars($user_data['address'] ?? 'Not Specified'); ?>
            </td>
          </tr>
          <tr>
            <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: #0B1F4B;">Registration Date:</td>
            <td style="padding: 0.85rem 1.25rem; color: #64748b; font-size: 0.88rem;">
              <?php echo htmlspecialchars($user_data['registered_at'] ?? date('F d, Y')); ?>
            </td>
          </tr>
        </table>
      </div>

      <!-- Action Button Moving to the Next Page -->
      <div style="display: flex; flex-direction: column; gap: 0.85rem;">
        <a href="dashboard.php" class="btn btn-gold btn-block" style="padding: 0.95rem; font-size: 1.1rem; font-weight: 700;">
          &#127891; Proceed to Student Portal &amp; Digital ID Card &rarr;
        </a>
        <a href="index.php" class="btn btn-outline-navy btn-block" style="padding: 0.75rem;">
          Return to Homepage
        </a>
      </div>

    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
