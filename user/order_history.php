<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for the logged-in user
$orders_query = "SELECT id, total, status, created_at 
                 FROM orders 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $orders[] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Order History</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <h1 class="text-4xl font-bold text-center mb-10">Order History</h1>

        <?php if (!empty($orders)): ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order): ?>
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-2xl font-bold text-gray-800">Order #<?= htmlspecialchars($order['id']); ?></h2>
                            <p class="text-gray-500">Placed on: <?= date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        <p class="text-gray-600">Status: <span class="font-semibold"><?= htmlspecialchars($order['status']); ?></span></p>
                        <p class="text-gray-800 font-bold mt-2">Total: $<?= number_format($order['total'], 2); ?></p>

                        <!-- Fetch Order Details -->
                        <?php
                        $order_details_query = "SELECT d.item_id, m.name, d.quantity, d.subtotal 
                                                FROM order_details d 
                                                JOIN menu_items m ON d.item_id = m.id 
                                                WHERE d.order_id = ?";
                        $stmt = $conn->prepare($order_details_query);
                        $stmt->bind_param("i", $order['id']);
                        $stmt->execute();
                        $details_result = $stmt->get_result();
                        $details = $details_result->fetch_all(MYSQLI_ASSOC);
                        ?>

                        <div class="mt-4">
                            <h3 class="text-lg font-semibold mb-2">Items:</h3>
                            <ul class="list-disc pl-6">
                                <?php foreach ($details as $detail): ?>
                                    <li>
                                        <?= htmlspecialchars($detail['name']); ?> 
                                        (x<?= htmlspecialchars($detail['quantity']); ?>) - 
                                        $<?= number_format($detail['subtotal'], 2); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">You have no orders yet. <a href="menu.php" class="text-blue-500">Start ordering now</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
