<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <h1 class="text-4xl font-bold text-center mb-10">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Inventory Management -->
            <a href="inventory.php" class="bg-blue-500 text-white py-6 px-6 rounded-lg shadow-lg text-xl text-center hover:bg-blue-600">
                Manage Inventory
            </a>

            <!-- Order Management -->
            <a href="orders.php" class="bg-green-500 text-white py-6 px-6 rounded-lg shadow-lg text-xl text-center hover:bg-green-600">
                Manage Orders
            </a>

            <!-- User Management -->
            <a href="users.php" class="bg-yellow-500 text-white py-6 px-6 rounded-lg shadow-lg text-xl text-center hover:bg-yellow-600">
                Manage Users
            </a>

            <!-- Payment History -->
            <a href="payments.php" class="bg-purple-500 text-white py-6 px-6 rounded-lg shadow-lg text-xl text-center hover:bg-purple-600">
                View Payment History
            </a>
        </div>
    </div>
</body>
</html>
