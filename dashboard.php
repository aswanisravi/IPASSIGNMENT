<?php
$page_title = "Student Dashboard & ID Card";
require_once __DIR__ . '/config/db.php';

// Authentication Check: Redirect to homepage login if session does not exist
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Authentication required. Please sign in with your Student ID and Password.";
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$current_user) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Student Details
$student_id = $current_user['student_id'] ?? 'N/A';
$first_name = $current_user['first_name'] ?? $current_user['name'];
$last_name = $current_user['last_name'] ?? '';
$full_name = $current_user['name'];
$email = $current_user['email'];
$address = $current_user['address'] ?? 'Not Specified';
$role = $current_user['role'] ?? 'Student';
$initial = strtoupper(substr($first_name, 0, 1));

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main style="background-color: #f8fafc; padding-bottom: 5rem;">
  
  <!-- Dashboard Top Banner -->
  <section class="dashboard-header" style="background: linear-gradient(135deg, #0B1F4B 0%, #173b67 100%); color: #ffffff; padding: 2.75rem 0; border-bottom: 3px solid #C9A227;">
    <div class="container dash-user-bar">
      <div class="dash-avatar-group">
        <div class="avatar-circle">
          <?php echo htmlspecialchars($initial); ?>
        </div>
        <div>
          <div style="font-size: 0.85rem; color: #C9A227; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
            &#127891; Active Student Profile
          </div>
          <h1 style="font-size: 2rem; font-weight: 800; margin: 0.2rem 0; color: #ffffff;">
            Welcome, <?php echo htmlspecialchars($full_name); ?>!
          </h1>
          <p style="color: #cbd5e1; font-size: 0.95rem; margin: 0;">
            Student ID: <strong style="color: #ffffff;"><?php echo htmlspecialchars($student_id); ?></strong> &bull; Gmail: <strong style="color: #ffffff;"><?php echo htmlspecialchars($email); ?></strong>
          </p>
        </div>
      </div>

      <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-gold btn-sm" onclick="window.print();">
          &#128438; Print Student ID Card
        </button>
        <a href="logout.php" class="btn btn-outline-gold btn-sm">
          &#128682; Logout
        </a>
      </div>
    </div>
  </section>

  <div class="container" style="margin-top: 2.5rem;">
    
    <!-- 4 Stats Cards Grid -->
    <div class="dash-stat-grid" style="margin-top: 0; margin-bottom: 2.5rem;">
      <div class="stat-card">
        <span class="stat-label">Enrollment Status</span>
        <span class="stat-value" style="color: #10b981; font-size: 1.35rem;">&#10004; Verified</span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Student ID: <?php echo htmlspecialchars($student_id); ?></small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Attendance Rate</span>
        <span class="stat-value" style="color: #0B1F4B;">94.5%</span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Current Semester Average</small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Enrolled Subjects</span>
        <span class="stat-value">5 Courses</span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Computer Science &amp; Eng.</small>
      </div>

      <div class="stat-card">
        <span class="stat-label">Library Books</span>
        <span class="stat-value">2 Issued</span>
        <small style="color: var(--text-muted); margin-top: 0.5rem;">Due date: Nov 28</small>
      </div>
    </div>

    <!-- Main Row: Digital Student ID Card & Academic Overview -->
    <div class="grid-2" style="align-items: flex-start; margin-bottom: 2.5rem;">
      
      <!-- DIGITAL STUDENT IDENTITY CARD -->
      <div class="digital-id-card">
        <!-- Card Header -->
        <div class="id-card-header">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="font-size: 1.8rem; background: #C9A227; color: #0B1F4B; border-radius: 6px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; font-weight: 800;">
              &#127891;
            </div>
            <div>
              <div style="font-size: 1rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">University Student Portal</div>
              <div style="font-size: 0.72rem; color: #C9A227; letter-spacing: 1px; font-weight: 700;">OFFICIAL IDENTITY CARD</div>
            </div>
          </div>
          <span style="background: rgba(201, 162, 39, 0.2); border: 1px solid #C9A227; color: #C9A227; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
            ACTIVE
          </span>
        </div>

        <!-- Card Body -->
        <div class="id-card-body">
          <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <!-- Avatar -->
            <div class="id-photo-box">
              <span style="font-size: 2.2rem; font-weight: 800; color: #0B1F4B;">
                <?php echo htmlspecialchars($initial); ?>
              </span>
              <span class="id-photo-caption">PHOTO</span>
            </div>

            <!-- Basic Info -->
            <div style="flex: 1; min-width: 180px;">
              <div style="font-size: 1.35rem; font-weight: 800; color: #0B1F4B; line-height: 1.2;">
                <?php echo htmlspecialchars($full_name); ?>
              </div>
              <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                Undergraduate Student &bull; CSE Department
              </div>
              <div style="margin-top: 0.65rem;">
                <span style="background: #0B1F4B; color: #C9A227; font-family: monospace; font-size: 0.95rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 4px; display: inline-block;">
                  ID: <?php echo htmlspecialchars($student_id); ?>
                </span>
              </div>
            </div>
          </div>

          <!-- Detailed Info Table -->
          <table class="id-table">
            <tr>
              <td class="id-label">First Name:</td>
              <td class="id-value"><?php echo htmlspecialchars($first_name); ?></td>
            </tr>
            <tr>
              <td class="id-label">Last Name:</td>
              <td class="id-value"><?php echo !empty($last_name) ? htmlspecialchars($last_name) : '<em>Not specified</em>'; ?></td>
            </tr>
            <tr>
              <td class="id-label">Gmail Address:</td>
              <td class="id-value"><code><?php echo htmlspecialchars($email); ?></code></td>
            </tr>
            <tr>
              <td class="id-label">Student Address:</td>
              <td class="id-value"><?php echo htmlspecialchars($address); ?></td>
            </tr>
            <tr>
              <td class="id-label">Valid Through:</td>
              <td class="id-value">Academic Year 2026 - 2027</td>
            </tr>
          </table>

          <!-- Card Barcode Graphic -->
          <div class="id-barcode-section">
            <div class="barcode-graphic">||||| | |||| ||| ||||||| | |||| ||||| |||| | ||| |||||</div>
            <div style="font-size: 0.72rem; color: #64748b; font-family: monospace; margin-top: 0.2rem;">
              *STU-<?php echo htmlspecialchars($student_id); ?>-UNIV*
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Academic Courses & Notice Quick Access -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Enrolled Courses Card -->
        <div class="card" style="box-shadow: 0 4px 16px rgba(11, 31, 75, 0.06); border-top: 4px solid #0B1F4B;">
          <h2 style="font-size: 1.25rem; color: #0B1F4B; font-weight: 700; margin-bottom: 1rem;">
            &#128218; Enrolled Courses (Semester 5)
          </h2>

          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
              <div>
                <strong style="color: #0B1F4B; font-size: 0.95rem;">CS301: Internet Programming</strong>
                <div style="font-size: 0.8rem; color: #64748b;">Prof. Robert Sterling &bull; Mon/Wed 10:00 AM</div>
              </div>
              <span style="background: #ecfdf5; color: #065f46; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px;">
                98% Attendance
              </span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
              <div>
                <strong style="color: #0B1F4B; font-size: 0.95rem;">CS302: Database Management Systems</strong>
                <div style="font-size: 0.8rem; color: #64748b;">Dr. Emily Chen &bull; Tue/Thu 11:30 AM</div>
              </div>
              <span style="background: #ecfdf5; color: #065f46; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px;">
                92% Attendance
              </span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
              <div>
                <strong style="color: #0B1F4B; font-size: 0.95rem;">CS303: Software Engineering &amp; Design</strong>
                <div style="font-size: 0.8rem; color: #64748b;">Prof. Alan Walker &bull; Fri 09:00 AM</div>
              </div>
              <span style="background: #ecfdf5; color: #065f46; font-size: 0.8rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px;">
                94% Attendance
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Actions & Help Desk -->
        <div class="card" style="box-shadow: 0 4px 16px rgba(11, 31, 75, 0.06); border-top: 4px solid #C9A227;">
          <h2 style="font-size: 1.25rem; color: #0B1F4B; font-weight: 700; margin-bottom: 0.75rem;">
            &#128172; Need Assistance?
          </h2>
          <p style="color: #64748b; font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.25rem;">
            If you need to update your registered email address or need support with course enrollments, submit a ticket to the student help desk.
          </p>
          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="contact.php" class="btn btn-gold btn-sm">
              &#9993; Contact Help Desk
            </a>
            <a href="index.php" class="btn btn-outline-navy btn-sm">
              View Homepage &amp; Notices
            </a>
          </div>
        </div>

      </div>

    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
