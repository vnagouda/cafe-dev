<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT c.id AS cart_id, c.quantity, m.name, m.price, m.image 
        FROM cart c
        JOIN menu_items m ON c.item_id = m.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Cart</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <h1 class="text-5xl font-bold text-center mb-10">Your Cart</h1>

        <div id="toast-container" class="fixed top-5 right-5 z-50"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo $item['name']; ?></h2>
                        <p class="text-gray-500 my-2">Quantity: <span class="item-quantity"><?php echo $item['quantity']; ?></span></p>
                        <p class="text-gray-500 my-2">Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <p class="text-yellow-600 font-bold mt-2">Total: $<span class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></p>
                        <div class="mt-4 flex justify-between">
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-600 transition remove-btn" data-id="<?php echo $item['cart_id']; ?>">Remove</button>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 transition decrease-btn" data-id="<?php echo $item['cart_id']; ?>">Decrease</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastContainer = document.getElementById('toast-container');

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `px-4 py-2 rounded-lg shadow-md text-white mb-2 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                toast.textContent = message;
                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }

            // Handle Remove Button
            document.querySelectorAll('.remove-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const cartId = button.dataset.id;

                    fetch('../user/cart_api.php?action=remove', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cart_id: cartId }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showToast('Item removed successfully!');
                                button.closest('.bg-white').remove();
                            } else {
                                showToast('Failed to remove item.', 'error');
                                console.error(data.message);
                            }
                        })
                        .catch(err => console.error('Fetch error:', err));
                });
            });

            // Handle Decrease Button
            document.querySelectorAll('.decrease-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const cartId = button.dataset.id;

                    fetch('../user/cart_api.php?action=decrease', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cart_id: cartId }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showToast('Quantity decreased successfully!');
                                location.reload(); // Reload to reflect updated quantity
                            } else {
                                showToast('Failed to decrease quantity.', 'error');
                                console.error(data.message);
                            }
                        })
                        .catch(err => console.error('Fetch error:', err));
                });
            });
        });
    </script>
</body>
</html>
