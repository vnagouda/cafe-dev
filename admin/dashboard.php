<?php
include '../includes/header.php';
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    <p>Manage users, inventory, and view orders here.</p>
    <ul class="mt-4">
        <li><a href="manage_users.php" class="text-blue-500 hover:underline">Manage Users</a></li>
        <li><a href="manage_inventory.php" class="text-blue-500 hover:underline">Manage Inventory</a></li>
        <li><a href="view_orders.php" class="text-blue-500 hover:underline">View Orders</a></li>
    </ul>
</div>