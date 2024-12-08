<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query = "SELECT c.item_id, c.quantity, m.price 
               FROM cart c 
               JOIN menu_items m ON c.item_id = m.id 
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// Redirect if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $insert_order_query = "INSERT INTO orders (user_id, total, status, created_at) 
                               VALUES (?, ?, 'Pending', NOW())";
        $stmt = $conn->prepare($insert_order_query);
        $stmt->bind_param("id", $user_id, $total_price);
        $stmt->execute();
        $order_id = $conn->insert_id; // Get the generated order ID

        // Insert into order_details table
        $insert_detail_query = "INSERT INTO order_details (order_id, item_id, quantity, subtotal) 
                                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_detail_query);

        foreach ($cart_items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $subtotal);
            $stmt->execute();
        }

        // Clear the cart
        $delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($delete_cart_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to success page or display a success message
        echo "<script>alert('Order placed successfully! Order ID: $order_id'); window.location.href = 'menu.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<script>alert('Failed to place the order. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Checkout</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-10">Checkout</h1>

        <table class="w-full bg-white shadow-md rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-4">Item</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Quantity</th>
                    <th class="p-4">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="border-b">
                        <td class="p-4"><?= htmlspecialchars($item['item_id']); ?></td>
                        <td class="p-4">$<?= number_format($item['price'], 2); ?></td>
                        <td class="p-4"><?= htmlspecialchars($item['quantity']); ?></td>
                        <td class="p-4">$<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right p-4 font-bold">Total:</td>
                    <td class="p-4 font-bold">$<?= number_format($total_price, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Place Order Button -->
        <form method="post" class="mt-6 text-right">
            <button name="place_order" class="bg-blue-500 text-white px-6 py-3 rounded-md shadow-md hover:bg-blue-600">Place Order</button>
        </form>
    </div>
</body>
</html>
