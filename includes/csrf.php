<?php
/**
 * CSRF Protection Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or retrieve existing CSRF token
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate hidden input field with CSRF token
 */
function csrf_field() {
    $token = htmlspecialchars(get_csrf_token());
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify submitted CSRF token
 */
function verify_csrf_token($submitted_token) {
    if (empty($_SESSION['csrf_token']) || empty($submitted_token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submitted_token);
}
