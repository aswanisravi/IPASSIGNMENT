# Student Portal Web Application

A responsive 5-page student portal built with **HTML5, CSS3, JavaScript, PHP (PDO), and MySQL**. Designed with a crisp white background, royal navy blue (`#0B1F4B`), and gold (`#C9A227`) theme, featuring gold buttons with navy text.

---

## 📁 Project Folder Structure

```
webpage php/
├── assets/
│   ├── css/
│   │   └── style.css            # White, Navy (#0B1F4B) & Gold (#C9A227) responsive styles
│   └── js/
│       └── validation.js        # Real-time client-side live validation & mobile toggle
├── includes/
│   ├── csrf.php                 # CSRF token generator & validator
│   ├── db.php                   # PDO Database connector (MySQL default + SQLite fallback)
│   ├── footer.php               # Branded Navy & Gold footer
│   └── header.php               # Responsive header, SVG graduation cap logo & navigation
├── config.php                   # Database & SMTP / PHPMailer configuration
├── index.php                    # PAGE 1: Home Page = Login
├── register.php                 # PAGE 2: Create Account
├── success.php                  # PAGE 3: Registration Success
├── dashboard.php                # PAGE 4: Student Dashboard
├── forgot.php                   # PAGE 5: Forgot Password (3 steps on same page)
├── logout.php                   # Session destruction & redirect
├── schema.sql                   # MySQL database creation & table schema
└── README.md                    # Setup documentation
```

---

## 🗄️ Database Schema (`schema.sql`)

```sql
CREATE DATABASE IF NOT EXISTS `student_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `student_portal`;

-- Table 1: students
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(12) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) DEFAULT '',
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 2: reset_codes
CREATE TABLE IF NOT EXISTS `reset_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL,
  `code` VARCHAR(6) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pre-seeded Student Account for Quick Testing:
-- Student ID: 20240101 | Password: Password123
INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password_hash`)
VALUES (
  '20240101',
  'Aswani',
  'Ravi',
  'aswani123@gmail.com',
  '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.'
)
ON DUPLICATE KEY UPDATE `student_id` = `student_id`;
```

---

## 🛠️ Step-by-Step Instructions to Run on XAMPP

### Step 1: Copy Project Folder to XAMPP
Copy this project directory into your XAMPP `htdocs` folder:
```
C:\xampp\htdocs\student-portal
```

### Step 2: Start Apache and MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### Step 3: Import the Database in phpMyAdmin
1. Open your browser and go to:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click the **Import** tab in the top navigation bar.
3. Click **Choose File** and select `schema.sql` located inside the project folder (`C:\xampp\htdocs\student-portal\schema.sql`).
4. Click **Go** (or **Import**) at the bottom.
5. The `student_portal` database and both tables (`students`, `reset_codes`) will be created with the demo account.

### Step 4: Open the Website
In your browser, visit:
```
http://localhost/student-portal/
```
*(Or if placed directly in `htdocs`: `http://localhost/`)*

---

## 🔑 Demo Account Credentials

| Field | Value |
| :--- | :--- |
| **Student ID** | `2024-01-01` (or `20240101`) |
| **Password** | `Password123` |
| **Name** | Aswani Ravi |
| **Email** | `aswani123@gmail.com` |

---

## 📄 The 5 Pages Explained

### Page 1: Home Page = Login (`index.php`)
- **Logo & Name:** College graduation cap SVG logo and college name at the top.
- **Login Form:** Strictly **Student ID** and **Password** only.
- **Validation Messages:**
  - If password is wrong: displays exact red message **`Wrong password`**.
  - If Student ID does not exist: displays exact red message **`Student ID not found. Please create an account.`**
- **Links:**
  - "Forgot password?" &rarr; `forgot.php`
  - "New student? Create an account" &rarr; `register.php`
- On valid login: regenerates session ID and opens `dashboard.php`.

### Page 2: Create Account (`register.php`)
- **Fields & Rules:**
  1. **Student ID:** Numbers only (0-9), 6 to 12 digits, unique in database.
  2. **First name:** Required, letters only.
  3. **Last name:** Optional, letters only if filled.
  4. **Email:** Must end with `@gmail.com`. The part before `@gmail.com` must be lowercase only (no capital letters), e.g. `aswani123@gmail.com`. Unique in database.
  5. **Password:** At least 8 characters, must contain at least one capital letter (A-Z), one small letter (a-z), and one number (0-9).
  6. **Confirm password:** Must match password.
- Live client-side JavaScript error under each field + server-side PHP validation.
- "Already registered? Login" link goes back to Page 1 (`index.php`).
- On success: saves student and redirects to Page 3 (`success.php`).

### Page 3: Registration Success (`success.php`)
- Shows green message: **`You have registered successfully.`**
- Shows clean summary card with: Student ID, full name (first and last name), and email. Never shows password.
- Gold button **`Continue to Dashboard`** linking to Page 4 (`dashboard.php`).
- Session-guarded: Only accessible directly after registering; otherwise redirects to Page 1.

### Page 4: Student Dashboard (`dashboard.php`)
- Session-protected: redirects unauthenticated users to Page 1 (`index.php`).
- **Welcome banner** with the student's full name.
- **Profile card** with Student ID, full name, and email.
- **4 Quick-Link Cards:**
  1. *Course Materials*
  2. *Notices and Deadlines*
  3. *Attendance*
  4. *Help Desk*
- **Notice Board** with 3 sample notices.
- **Logout button** that destroys the session and returns to Page 1 (`index.php`).

### Page 5: Forgot Password (`forgot.php`) - 3 Steps on Same Page
- **Step 1:** Asks for registered email (`@gmail.com`). If found, creates 6-digit code with 10-minute expiry in `reset_codes`. If email sending is not configured on localhost demo, displays the code on screen for instant testing.
- **Step 2:** Asks for the 6-digit code. Rejects invalid or expired codes.
- **Step 3:** Asks for new password and confirmation (same password security rules: min 8 chars, 1 capital, 1 small, 1 number). Hashes and updates in `students`, deletes code from `reset_codes`, and redirects to Page 1 with green message **`Password changed. Please login.`**
