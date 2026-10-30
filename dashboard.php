<?php
$page_title = "Student Dashboard";
require_once __DIR__ . '/includes/db.php';

// Session Protection: If not logged in, redirect to Page 1
if (empty($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// Clear the registration success flag if it was set
unset($_SESSION['registration_success']);

$student_id = $_SESSION['student_id'] ?? '';
$first_name = $_SESSION['first_name'] ?? 'Student';
$last_name = $_SESSION['last_name'] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
$email = $_SESSION['email'] ?? '';
$initial = strtoupper(substr($first_name, 0, 1));

include __DIR__ . '/includes/header.php';
?>

<main class="dash-content-area">
  
  <!-- Welcome Banner with the student's name -->
  <section class="dashboard-banner">
    <div class="container dash-user-row">
      <div class="dash-avatar-group">
        <div class="dash-avatar">
          <?php echo htmlspecialchars($initial); ?>
        </div>
        <div>
          <h1 class="dash-title">Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
          <p class="dash-sub">Enrolled Student &bull; Academic Year 2026&ndash;2027 &bull; Student Portal</p>
        </div>
      </div>
      <div>
        <a href="logout.php" class="btn btn-gold btn-sm">
          Logout
        </a>
      </div>
    </div>
  </section>

  <div class="container" style="margin-top: 2.5rem;">
    
    <!-- 4 Quick-Link Cards (Course Materials, Notices and Deadlines, Attendance, Help Desk) -->
    <div class="quick-links-grid">
      
      <!-- Card 1: Course Materials -->
      <div class="portal-action-card">
        <div class="action-card-icon">&#128218;</div>
        <h3 class="action-card-title">Course Materials</h3>
        <p class="action-card-desc">
          Access lecture notes, syllabus, reference materials and recommended reading lists for your enrolled subjects.
        </p>
      </div>

      <!-- Card 2: Notices and Deadlines -->
      <div class="portal-action-card">
        <div class="action-card-icon">&#128197;</div>
        <h3 class="action-card-title">Notices and Deadlines</h3>
        <p class="action-card-desc">
          Check examination timetables, assignment submission deadlines, and official university announcements.
        </p>
      </div>

      <!-- Card 3: Attendance -->
      <div class="portal-action-card">
        <div class="action-card-icon">&#9989;</div>
        <h3 class="action-card-title">Attendance</h3>
        <p class="action-card-desc">
          Track your subject-wise presence, overall semester percentage (Current: 94%), and medical leave credits.
        </p>
      </div>

      <!-- Card 4: Help Desk -->
      <div class="portal-action-card">
        <div class="action-card-icon">&#128172;</div>
        <h3 class="action-card-title">Help Desk</h3>
        <p class="action-card-desc">
          Submit questions regarding course registration, hall tickets, fee payments, or certificate requests.
        </p>
      </div>

    </div>

    <!-- 2-Column Section: Profile Card & Notice Board -->
    <div class="dash-layout-grid">
      
      <!-- Profile Card (Student ID, name, email) -->
      <div class="content-box">
        <h2 class="content-box-title">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
          Student Profile
        </h2>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div style="padding: 0.75rem 1rem; background: var(--bg-surface); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
            <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Student ID</div>
            <div style="font-size: 1.15rem; font-weight: 800; color: var(--navy); font-family: monospace;">
              <?php echo htmlspecialchars($student_id); ?>
            </div>
          </div>

          <div style="padding: 0.75rem 1rem; background: var(--bg-surface); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
            <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Full Name</div>
            <div style="font-size: 1.05rem; font-weight: 700; color: var(--navy);">
              <?php echo htmlspecialchars($full_name); ?>
            </div>
          </div>

          <div style="padding: 0.75rem 1rem; background: var(--bg-surface); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
            <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Registered Email</div>
            <div style="font-size: 0.95rem; font-weight: 600; color: var(--navy); word-break: break-all;">
              <?php echo htmlspecialchars($email); ?>
            </div>
          </div>

          <div style="padding: 0.75rem 1rem; background: var(--bg-surface); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
            <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Account Status</div>
            <div style="font-size: 0.92rem; font-weight: 700; color: var(--success); display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
              <span style="width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; display: inline-block;"></span>
              Active &amp; Verified Student
            </div>
          </div>
        </div>

        <div style="margin-top: 1.5rem;">
          <a href="logout.php" class="btn btn-outline-navy btn-block">
            Logout of Portal
          </a>
        </div>
      </div>

      <!-- Notice Board (3 Sample Notices) -->
      <div class="content-box">
        <h2 class="content-box-title">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
          </svg>
          Campus Notice Board
        </h2>

        <div class="notice-list">
          
          <!-- Notice 1 -->
          <div class="notice-item">
            <div class="notice-meta">
              <span>EXAMINATION NOTICE</span>
              <span>November 05, 2026</span>
            </div>
            <h4 class="notice-headline">Mid-Semester Examination Schedule Published</h4>
            <p class="notice-desc">
              The internal examination timetable for all undergraduate departments is now available. Download your digital hall ticket through the portal before November 15.
            </p>
          </div>

          <!-- Notice 2 -->
          <div class="notice-item">
            <div class="notice-meta">
              <span>LIBRARY ADVISORY</span>
              <span>November 12, 2026</span>
            </div>
            <h4 class="notice-headline">Extended Library Timings for Exam Preparations</h4>
            <p class="notice-desc">
              The Central Library and digital reference laboratories will remain open until 10:00 PM on weekdays throughout the preparatory and exam weeks.
            </p>
          </div>

          <!-- Notice 3 -->
          <div class="notice-item">
            <div class="notice-meta">
              <span>ACADEMIC DEADLINE</span>
              <span>November 20, 2026</span>
            </div>
            <h4 class="notice-headline">Project Report &amp; Assignment Submission</h4>
            <p class="notice-desc">
              All students must submit their semester project interim documentation to their respective faculty advisors by November 20, 5:00 PM.
            </p>
          </div>

        </div>
      </div>

    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
