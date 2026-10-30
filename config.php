<?php
/**
 * Configuration Settings: Database, SMTP, and College Details
 */

// -------------------------------------------------------------
// 1. MySQL Database Configuration (Standard XAMPP Settings)
// -------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'student_portal');
define('DB_USER', 'root');
define('DB_PASS', '');

// -------------------------------------------------------------
// 2. SMTP Email Configuration (PHPMailer / Gmail SMTP)
// -------------------------------------------------------------
// If you want live email delivery, set SMTP_ENABLED to true
// and provide your Gmail address and 16-character App Password.
// (Generated at: https://myaccount.google.com/apppasswords)
// If left false, the 6-digit verification code will be displayed
// directly on the screen so you can test it on localhost instantly!
define('SMTP_ENABLED', false);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
define('SMTP_USER', 'your-college-email@gmail.com');
define('SMTP_PASS', 'your-16-character-app-password');
define('SMTP_FROM_NAME', 'Student Portal Administration');

// -------------------------------------------------------------
// 3. College Branding Details
// -------------------------------------------------------------
define('COLLEGE_NAME', 'Apex Institute of Technology');
define('PORTAL_NAME', 'Student Portal');

