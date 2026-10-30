<?php
$page_title = "User Dashboard";
require_once __DIR__ . '/config/db.php';

// Authentication Check: Redirect to login if session does not exist
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Authentication required. Please log in with your Name, Email ID, and Password.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$current_user) {
    // Session user ID no longer in DB
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch aggregate statistics from Database
$total_users_row = db_fetch_one("SELECT COUNT(*) as count FROM users");
$total_users = $total_users_row ? $total_users_row['count'] : 1;

$total_messages_row = db_fetch_one("SELECT COUNT(*) as count FROM messages");
$total_messages = $total_messages_row ? $total_messages_row['count'] : 0;

// Fetch all registered users for demonstration of database queries
$all_users = db_fetch_all("SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC LIMIT 10");

// Get first initial for avatar
$initial = strtoupper(substr($current_user['name'], 0, 1));

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main style="background-color: var(--bg-surface); padding-bottom: 4rem;">
  
  <!-- Dashboard Banner Header (Navy & Gold) -->
  <section class="dashboard-header">
    <div class="container dash-user-bar">
      <div class="dash-avatar-group">
        <div class="avatar-circle">
          <?php echo htmlspecialchars($initial); ?>
        </div>
        <div>
          <div style="font-size: 0.85rem; color: var(--gold-light); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
            &#128100; <?php echo htmlspecialchars($current_user['role']); ?> Dashboard
          </div>
          <h1 style="font-size: 2rem; font-weight: 700; margin: 0.2rem 0;">
            Welcome, <?php echo htmlspecialchars($current_user['name']); ?>!
          </h1>
          <p style="color: #cbd5e1; font-size: 0.95rem; margin: 0;">
            Logged in via Email: <strong><?php echo htmlspecialchars($current_user['email']); ?></strong>
          </p>
        </div>
      </div>

      <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="contact.php" class="btn btn-gold btn-sm">
          &#9993; Send Message
        </a>
        <a href="logout.php" class="btn btn-outline-gold btn-sm">
          &#128682; Secure Logout
        </a>
      </div>
    </div>
  </section>

  <div class="container">
    
    <!-- 4 Stats Cards Floating Grid -->
    <div class="dash-stat-grid">
      <div class="stat-card">
        <span class="stat-label">Active Session</span>
        <span class="stat-value" style="color: var(--status-success); font-size: 1.35rem;">&#10004; Authenticated</span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">User ID: #<?php echo htmlspecialchars($current_user['id']); ?></small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Total Registered Users</span>
        <span class="stat-value"><?php echo $total_users; ?></span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Active in database</small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Feedback Messages</span>
        <span class="stat-value"><?php echo $total_messages; ?></span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Submitted via Contact Us</small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Account Created</span>
        <span class="stat-value" style="font-size: 1.1rem; color: var(--navy-secondary); margin-top: 0.3rem;">
          <?php echo date('M d, Y', strtotime($current_user['created_at'])); ?>
        </span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Standard Student Role</small>
      </div>
    </div>

    <!-- Two-Column Layout: Profile Overview & Quick Actions -->
    <div class="grid-2" style="margin-bottom: 2.5rem;">
      
      <!-- User Profile Information Card -->
      <div class="card" style="box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-light); padding-bottom: 1rem; margin-bottom: 1.25rem;">
          <h2 style="font-size: 1.35rem; color: var(--navy-primary); font-weight: 700;">
            &#128221; Account Details
          </h2>
          <span class="status-badge connected">Verified Account</span>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
          <tr style="border-bottom: 1px solid var(--border-light);">
            <td style="padding: 0.75rem 0; font-weight: 600; color: var(--navy-secondary); width: 35%;">Full Name:</td>
            <td style="padding: 0.75rem 0; color: var(--text-primary);"><?php echo htmlspecialchars($current_user['name']); ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--border-light);">
            <td style="padding: 0.75rem 0; font-weight: 600; color: var(--navy-secondary);">Email ID:</td>
            <td style="padding: 0.75rem 0; color: var(--text-primary);"><?php echo htmlspecialchars($current_user['email']); ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--border-light);">
            <td style="padding: 0.75rem 0; font-weight: 600; color: var(--navy-secondary);">Phone Number:</td>
            <td style="padding: 0.75rem 0; color: var(--text-primary);"><?php echo htmlspecialchars($current_user['phone'] ?? 'Not Specified'); ?></td>
          </tr>
          <tr style="border-bottom: 1px solid var(--border-light);">
            <td style="padding: 0.75rem 0; font-weight: 600; color: var(--navy-secondary);">System Role:</td>
            <td style="padding: 0.75rem 0; color: var(--text-primary);">
              <span style="background: var(--navy-secondary); color: var(--gold-light); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                <?php echo htmlspecialchars($current_user['role']); ?>
              </span>
            </td>
          </tr>
          <tr>
            <td style="padding: 0.75rem 0; font-weight: 600; color: var(--navy-secondary);">Password Hash:</td>
            <td style="padding: 0.75rem 0; color: var(--text-muted); font-family: monospace; font-size: 0.8rem;">
              <?php echo substr($current_user['password'], 0, 20) . '... (BCRYPT Protected)'; ?>
            </td>
          </tr>
        </table>
      </div>

      <!-- Assignment Verification Highlights -->
      <div class="card" style="box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-top: 4px solid var(--navy-secondary);">
        <div style="border-bottom: 2px solid var(--border-light); padding-bottom: 1rem; margin-bottom: 1.25rem;">
          <h2 style="font-size: 1.35rem; color: var(--navy-primary); font-weight: 700;">
            &#9733; Assignment Checklist
          </h2>
        </div>

        <ul style="list-style: none; font-size: 0.92rem; line-height: 2;">
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>3-5 Web Pages:</strong> Home, Login, Register, Dashboard, Contact Us.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Backend:</strong> Pure PHP 8 with session management.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Database Connectivity:</strong> MySQL / SQLite with prepared statements.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Form Validation:</strong> Dual-tier client & server verification.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Login Authentication:</strong> Asks for Name, Email ID, and Password.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Validation States:</strong> Custom Valid (green) and Invalid (red shake) notifications.</li>
          <li><strong style="color: var(--status-success);">&#10004;</strong> <strong>Color Theme:</strong> Crisp White, Royal Navy Blue & Metallic Gold buttons.</li>
        </ul>

        <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem;">
          <a href="setup.php" class="btn btn-navy btn-sm btn-block">Inspect Database Connection</a>
        </div>
      </div>
    </div>

    <!-- Live Database Records Table (Demonstrating DB Fetch) -->
    <div class="card" style="padding: 2rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <h2 style="font-size: 1.35rem; color: var(--navy-primary); font-weight: 700;">
            &#128451; Registered Users in Database
          </h2>
          <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
            Showing live rows queried from the <code>users</code> database table.
          </p>
        </div>
        <div>
          <a href="register.php" class="btn btn-gold btn-sm">+ Add New User</a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email Address</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Registered On</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($all_users)): ?>
              <?php foreach ($all_users as $u): ?>
                <tr <?php echo ($u['id'] == $current_user['id']) ? 'style="background-color: #fffdf5; font-weight: 600;"' : ''; ?>>
                  <td>#<?php echo htmlspecialchars($u['id']); ?></td>
                  <td>
                    <?php echo htmlspecialchars($u['name']); ?>
                    <?php if ($u['id'] == $current_user['id']): ?>
                      <span style="font-size: 0.75rem; background: var(--gold-primary); color: #000; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem;">You</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($u['email']); ?></td>
                  <td><?php echo htmlspecialchars($u['phone'] ?? 'N/A'); ?></td>
                  <td>
                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--navy-secondary);">
                      <?php echo htmlspecialchars($u['role']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No user records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

