<?php
function initSecureSession() {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_samesite' => 'Strict',
        'gc_maxlifetime' => 3600
    ]);
    
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    
    if (!isset($_SESSION['secure_vault'])) {
        $_SESSION['secure_vault'] = [
            'user_profiles' => [],
            'activity_log' => [],
            'failed_attempts' => 0
        ];
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function validatePassword($password) {
    return strlen($password) >= 6;
}

function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}
?>
