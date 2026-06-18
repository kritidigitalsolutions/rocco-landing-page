<?php
/**
 * Rocco Play Admin — Authentication Guard
 * Include at the top of every protected admin page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated. Redirect to login if not.
 */
function requireAuth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generate a CSRF token and store in session.
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from request.
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden CSRF input field.
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Get current admin user info.
 */
function getAdminName() {
    return $_SESSION['admin_name'] ?? 'Admin';
}

function getAdminEmail() {
    return $_SESSION['admin_email'] ?? '';
}
