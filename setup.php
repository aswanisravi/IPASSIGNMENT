<?php
$page_title = "Database Setup & Health Diagnostic";
require_once __DIR__ . '/config/db.php';

$action_message = '';
$action_status = '';

// Handle Re-seed or Reset action
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'reseed') {
        $db = get_db_connection();
        if ($db['type'] === 'pdo') {
            init_sqlite_tables($db['conn']);
        } elseif ($db['type'] === 'mysqli') {
            init_mysql_tables($db['conn']);
        }
        $action_status = 'success';
        $action_message = "Database tables and demo records verified and re-seeded successfully!";
    }
}

$conn_info = get_db_connection();
$users_count = db_fetch_one("SELECT COUNT(*) as count FROM users");
$messages_count = db_fetch_one("SELECT COUNT(*) as count FROM messages");
$users_list = db_fetch_all("SELECT id, student_id, first_name, last_name, name, email, address, phone, role, created_at FROM users");

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="section-padding" style="background-color: var(--bg-surface);">
  <div class="container" style="max-width: 950px;">
    
    <div class="section-header">
      <span class="section-tag">Diagnostic Tool</span>
      <h1 class="section-title">Database Connectivity & Setup</h1>
      <p class="section-subtitle">
        Inspect live database engine status, connection parameters, and table integrity for this assignment.
      </p>
    </div>

    <?php if (!empty($action_message)): ?>
      <div class="alert alert-success" style="margin-bottom: 2rem;">
        <span style="font-size: 1.3rem;">&#9989;</span>
        <div><strong>Status Update:</strong> <?php echo $action_message; ?></div>
      </div>
    <?php endif; ?>

    <!-- Status Overview Card -->
    <div class="card" style="margin-bottom: 2rem; border-top: 5px solid var(--gold-primary);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h2 style="font-size: 1.5rem; color: var(--navy-primary); font-weight: 700;">
            Active Connection Status
          </h2>
          <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">
            Real-time backend driver report
          </p>
        </div>
        <div>
          <?php echo db_status_badge(); ?>
        </div>
      </div>

      <div class="grid-3" style="margin-bottom: 1.5rem;">
        <div style="background: var(--bg-subtle); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
          <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Active Driver</div>
          <div style="font-size: 1.2rem; font-weight: 700; color: var(--navy-secondary);"><?php echo htmlspecialchars($conn_info['driver']); ?></div>
        </div>

        <div style="background: var(--bg-subtle); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
          <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Users In DB</div>
          <div style="font-size: 1.2rem; font-weight: 700; color: var(--navy-secondary);"><?php echo $users_count ? $users_count['count'] : 0; ?> Records</div>
        </div>

        <div style="background: var(--bg-subtle); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
          <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Messages In DB</div>
          <div style="font-size: 1.2rem; font-weight: 700; color: var(--navy-secondary);"><?php echo $messages_count ? $messages_count['count'] : 0; ?> Records</div>
        </div>
      </div>

      <div style="background: #fffdf5; border: 1px solid var(--gold-border); padding: 1.25rem; border-radius: var(--radius-sm); font-size: 0.9rem; color: #78350f;">
        <strong>&#128161; How Connection Handling Works:</strong>
        <p style="margin: 0.5rem 0 0; line-height: 1.5;">
          The system first attempts to connect to <strong>MySQL</strong> at <code><?php echo DB_HOST; ?>:<?php echo DB_PORT; ?></code> with database <code><?php echo DB_NAME; ?></code>. If MySQL service is active (such as in XAMPP / WAMP), it uses MySQL automatically. If MySQL is not running on localhost, it automatically activates the built-in <strong>SQLite</strong> driver (<code>database.sqlite</code>) so testing never fails!
        </p>
      </div>

      <form action="setup.php" method="POST" style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <input type="hidden" name="action" value="reseed">
        <button type="submit" class="btn btn-gold btn-sm">
          &#8635; Verify / Re-Seed Tables & Demo Data
        </button>
        <a href="login.php" class="btn btn-navy btn-sm">
          &#128273; Go to Login Page
        </a>
      </form>
    </div>

    <!-- Pre-Configured Users Table -->
    <div class="card" style="margin-bottom: 2rem;">
      <h3 style="font-size: 1.25rem; color: var(--navy-primary); margin-bottom: 1rem; font-weight: 700;">
        &#128101; Registered Student Accounts in Database
      </h3>
      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Student ID (Login)</th>
              <th>Full Name</th>
              <th>Gmail Address</th>
              <th>Address</th>
              <th>Default Password</th>
              <th>Role</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($users_list)): ?>
              <?php foreach ($users_list as $u): ?>
                <tr>
                  <td>#<?php echo htmlspecialchars($u['id']); ?></td>
                  <td><strong style="color: #0B1F4B; font-family: monospace;"><?php echo htmlspecialchars($u['student_id'] ?? '1001'); ?></strong></td>
                  <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                  <td><code><?php echo htmlspecialchars($u['email']); ?></code></td>
                  <td><?php echo htmlspecialchars($u['address'] ?? 'Campus Lane'); ?></td>
                  <td><code class="demo-code">Password123</code></td>
                  <td><span class="status-badge connected"><?php echo htmlspecialchars($u['role']); ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Manual MySQL Setup Instructions for College Submissions -->
    <div class="card" style="border-top: 4px solid var(--navy-secondary);">
      <h3 style="font-size: 1.25rem; color: var(--navy-primary); margin-bottom: 1rem; font-weight: 700;">
        &#128196; College / Lab Assignment Submission Instructions (XAMPP / phpMyAdmin)
      </h3>
      <ol style="padding-left: 1.5rem; line-height: 1.8; color: var(--text-secondary); font-size: 0.95rem;">
        <li>Place the project folder into your XAMPP web root: <code>C:\xampp\htdocs\webpage-php\</code></li>
        <li>Open XAMPP Control Panel and start both <strong>Apache</strong> and <strong>MySQL</strong>.</li>
        <li>Open your browser and navigate to <code>http://localhost/phpmyadmin/</code></li>
        <li>Click <strong>Import</strong> tab at the top and select the file <code>database.sql</code> from this project folder.</li>
        <li>Click <strong>Go</strong> to execute. The database <code>ip_assignment_db</code>, tables, and test records will be created.</li>
        <li>Access the web portal at <code>http://localhost/webpage-php/</code> or run directly using PHP built-in server: <code>php -S localhost:8000</code>.</li>
      </ol>
    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

