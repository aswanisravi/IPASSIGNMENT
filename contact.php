<?php
$page_title = "Contact Us & Feedback";
require_once __DIR__ . '/config/db.php';

$contact_status = '';
$error_message = '';
$success_message = '';
$field_errors = [];

// Pre-fill fields if user is already logged in
$default_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$default_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

$name = $default_name;
$email = $default_email;
$subject = '';
$category = 'General Inquiry';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $category = trim($_POST['category'] ?? 'General Inquiry');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Form Validations
    if (empty($name) || strlen($name) < 2) {
        $field_errors['name'] = 'Full Name is required (minimum 2 characters).';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = 'A valid Email ID is required.';
    }

    if (empty($subject) || strlen($subject) < 3) {
        $field_errors['subject'] = 'Subject is required (minimum 3 characters).';
    }

    if (empty($message) || strlen($message) < 10) {
        $field_errors['message'] = 'Message content is required (minimum 10 characters).';
    }

    if (empty($field_errors)) {
        $res = db_execute(
            "INSERT INTO messages (name, email, category, subject, message) VALUES (?, ?, ?, ?, ?)",
            [$name, $email, $category, $subject, $message]
        );

        if ($res['success']) {
            $contact_status = 'success';
            $success_message = "Your message has been successfully saved to the database (Ticket Reference #" . $res['insert_id'] . "). Thank you for reaching out!";
            // Reset message & subject
            $subject = '';
            $message = '';
        } else {
            $contact_status = 'error';
            $error_message = "Failed to save message to database. " . ($res['error'] ?? '');
        }
    } else {
        $contact_status = 'error';
        $error_message = "Please review the form and correct the validation errors.";
    }
}

// Fetch recent messages to demonstrate DB query
$recent_messages = db_fetch_all("SELECT * FROM messages ORDER BY id DESC LIMIT 5");

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="section-padding" style="background-color: var(--bg-surface);">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Get in Touch</span>
      <h1 class="section-title">Contact & Assignment Feedback</h1>
      <p class="section-subtitle">
        Fill out this form to test full database persistence and client + server validation.
      </p>
    </div>

    <div class="grid-2" style="align-items: flex-start; max-width: 1050px; margin: 0 auto;">
      
      <!-- Contact Form Card -->
      <div class="card" style="box-shadow: 0 8px 24px rgba(10, 25, 47, 0.08); border-top: 4px solid var(--gold-primary);">
        <h2 style="font-size: 1.4rem; color: var(--navy-primary); margin-bottom: 1.25rem; font-weight: 700;">
          &#9993; Send a Feedback Message
        </h2>

        <?php if ($contact_status === 'success'): ?>
          <div class="alert alert-success" role="alert">
            <span style="font-size: 1.25rem;">&#9989;</span>
            <div><strong>Success:</strong> <?php echo $success_message; ?></div>
          </div>
        <?php elseif ($contact_status === 'error'): ?>
          <div class="alert alert-danger" role="alert">
            <span style="font-size: 1.25rem;">&#10060;</span>
            <div><strong>Validation Error:</strong> <?php echo $error_message; ?></div>
          </div>
        <?php endif; ?>

        <form action="contact.php" method="POST" id="contactForm" novalidate>
          
          <div class="form-group">
            <label for="contactName" class="form-label">Full Name <span class="required-star">*</span></label>
            <div class="input-wrapper">
              <input 
                type="text" 
                name="name" 
                id="contactName" 
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

          <div class="form-group">
            <label for="contactEmail" class="form-label">Email ID <span class="required-star">*</span></label>
            <div class="input-wrapper">
              <input 
                type="email" 
                name="email" 
                id="contactEmail" 
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

          <div class="form-group">
            <label for="contactCategory" class="form-label">Inquiry Category</label>
            <select name="category" id="contactCategory" class="form-select">
              <option value="Assignment Feedback" <?php echo ($category === 'Assignment Feedback') ? 'selected' : ''; ?>>Assignment Feedback</option>
              <option value="Project Evaluation" <?php echo ($category === 'Project Evaluation') ? 'selected' : ''; ?>>Project Evaluation</option>
              <option value="General Inquiry" <?php echo ($category === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
              <option value="Technical Support" <?php echo ($category === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
            </select>
          </div>

          <div class="form-group">
            <label for="contactSubject" class="form-label">Subject <span class="required-star">*</span></label>
            <div class="input-wrapper">
              <input 
                type="text" 
                name="subject" 
                id="contactSubject" 
                class="form-input <?php echo isset($field_errors['subject']) ? 'is-invalid' : ''; ?>" 
                placeholder="Brief summary of your message"
                value="<?php echo htmlspecialchars($subject); ?>"
                required
              >
            </div>
            <div class="validation-feedback <?php echo isset($field_errors['subject']) ? 'error' : ''; ?>">
              <?php echo $field_errors['subject'] ?? ''; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="contactMessage" class="form-label">Message Details <span class="required-star">*</span></label>
            <div class="input-wrapper">
              <textarea 
                name="message" 
                id="contactMessage" 
                rows="4" 
                class="form-textarea <?php echo isset($field_errors['message']) ? 'is-invalid' : ''; ?>" 
                placeholder="Enter at least 10 characters here..."
                required
              ><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <div class="validation-feedback <?php echo isset($field_errors['message']) ? 'error' : ''; ?>">
              <?php echo $field_errors['message'] ?? ''; ?>
            </div>
          </div>

          <!-- Buttons -->
          <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-gold btn-block">
              &#128392; Submit Feedback
            </button>
            <button type="reset" class="btn btn-navy">
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Information & Recent Messages Card -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="card" style="box-shadow: 0 4px 16px rgba(0,0,0,0.05); background: #ffffff;">
          <h3 style="font-size: 1.2rem; color: var(--navy-primary); margin-bottom: 0.75rem; font-weight: 700;">
            &#128205; Assignment Contact Details
          </h3>
          <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1rem; line-height: 1.6;">
            This form is connected directly to the backend database table <code>messages</code>. When a submission passes validation, an <code>INSERT</code> query executes.
          </p>

          <div style="font-size: 0.9rem; line-height: 1.8; color: var(--navy-secondary);">
            <div><strong>Department:</strong> Department of Computer Science & Engineering</div>
            <div><strong>Course:</strong> Internet Programming (IP)</div>
            <div><strong>Design Theme:</strong> White, Navy Blue & Gold</div>
            <div><strong>Security:</strong> Input sanitization & Prepared statements</div>
          </div>
        </div>

        <div class="card" style="box-shadow: 0 4px 16px rgba(0,0,0,0.05); background: #ffffff;">
          <h3 style="font-size: 1.15rem; color: var(--navy-primary); margin-bottom: 0.75rem; font-weight: 700;">
            &#128451; Recent Messages in DB
          </h3>
          
          <?php if (!empty($recent_messages)): ?>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
              <?php foreach ($recent_messages as $msg): ?>
                <div style="border-left: 3px solid var(--gold-primary); background: var(--bg-subtle); padding: 0.75rem 1rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                  <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 700; color: var(--navy-primary); margin-bottom: 0.25rem;">
                    <span><?php echo htmlspecialchars($msg['name']); ?> (<?php echo htmlspecialchars($msg['category']); ?>)</span>
                    <span style="color: var(--text-muted);"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></span>
                  </div>
                  <div style="font-size: 0.88rem; font-weight: 600; color: var(--navy-secondary);"><?php echo htmlspecialchars($msg['subject']); ?></div>
                  <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem;"><?php echo htmlspecialchars($msg['message']); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p style="color: var(--text-muted); font-size: 0.9rem;">No feedback messages stored in database yet.</p>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

