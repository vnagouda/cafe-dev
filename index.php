<?php
include 'includes/header.php';
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}
?>

<div class="flex items-center justify-center h-screen bg-gradient-to-br from-yellow-700 to-orange-300">
    <div class="text-center text-white animate-fade-in">
        <h1 class="text-6xl font-bold mb-4">Welcome to the Cafe!</h1>
        <p class="text-xl mb-6">Login or Register to explore our menu.</p>
        <a href="login.php" class="inline-block px-8 py-3 border border-white text-white hover:bg-white hover:text-black transition duration-300">Login</a>
        <a href="register.php" class="inline-block px-8 py-3 ml-4 border border-white text-white hover:bg-white hover:text-black transition duration-300">Register</a>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fade-in 1s ease-in;
    }
    .smooth-transition {
        transition: opacity 0.3s ease-in-out;
    }
</style>
