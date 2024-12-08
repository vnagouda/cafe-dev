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
    <title>Manage Orders</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <h1 class="text-4xl font-bold text-center mb-10">Manage Orders</h1>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">Order ID</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders_query = "SELECT o.id, o.user_id, o.total, o.status, u.name 
                                     FROM orders o 
                                     JOIN users u ON o.user_id = u.id";
                    $orders_result = $conn->query($orders_query);
                    while ($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td class="p-4"><?= $order['id']; ?></td>
                            <td class="p-4"><?= htmlspecialchars($order['name']); ?></td>
                            <td class="p-4">₹<?= number_format($order['total'], 2); ?></td>
                            <td class="p-4"><?= htmlspecialchars($order['status']); ?></td>
                            <td class="p-4">
                                <a href="update_status.php?id=<?= $order['id']; ?>&status=Completed" class="text-green-500 hover:underline">Mark Completed</a>
                                <a href="update_status.php?id=<?= $order['id']; ?>&status=Cancelled" class="text-red-500 hover:underline">Cancel</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
