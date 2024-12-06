<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (e.g., 30 minutes)
$timeout_duration = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Destroy session if inactive for too long
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$_SESSION['last_activity'] = time(); // Update last activity timestamp

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
?>
