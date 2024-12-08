<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Cafe Website</title>
</head>
<body>
<header class="bg-gray-800 p-4 text-white">
    <nav class="container mx-auto flex justify-between items-center">
        <a href="/index.php" class="text-lg font-bold">Cafe Website</a>
        <div class="flex items-center gap-4">
            <?php if ($current_page != 'login.php' && $current_page != 'register.php' && $current_page != 'index.php'): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
          
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                    <a href="/user/cart.php" id="cart-header-btn" class="px-4 py-2 bg-yellow-500 rounded-lg shadow-md hover:bg-yellow-600 transition">
                        Cart (<span id="cart-count-header">0</span>)
                    </a>
                <?php endif; ?>
                <a href="../logout.php" class="px-4">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<script>
    // Fetch cart count and update in the header
    document.addEventListener('DOMContentLoaded', () => {
        fetch('../user/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('cart-count-header').textContent = data.count;
                }
            });
    });
</script>
