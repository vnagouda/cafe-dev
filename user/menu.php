<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Menu</title>
</head>
<body class="bg-gray-100">
    <div id="toast-container" class="fixed top-5 right-5 z-50"></div>

    <div class="container mx-auto py-10">
        <h1 class="text-5xl font-bold text-center mb-10">Our Menu</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo $item['name']; ?></h2>
                        <p class="text-gray-500 my-2"><?php echo $item['description']; ?></p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xl font-bold text-yellow-600">$<?php echo number_format($item['price'], 2); ?></span>
                            <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-yellow-600 transition add-to-cart-btn"
                                    data-id="<?php echo $item['id']; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Include JavaScript -->
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

            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
				button.addEventListener('click', () => {
					const itemId = button.dataset.id;

					fetch('../user/cart_api.php?action=add', { // Add the correct action parameter
						method: 'POST',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify({ item_id: itemId }),
					})
						.then(response => {
							if (!response.ok) {
								throw new Error(`HTTP error! Status: ${response.status}`);
							}

							const contentType = response.headers.get('content-type');
							if (!contentType || !contentType.includes('application/json')) {
								return response.text().then(text => {
									console.error('Unexpected response format:', text);
									throw new Error('Response is not JSON');
								});
							}

							return response.json();
						})
						.then(data => {
							if (data.status === 'success') {
								alert('Item added to cart successfully!');
							} else {
								console.error('API error:', data.message);
								alert('Failed to add item to cart.');
							}
						})
						.catch(err => console.error('Fetch error:', err));
				});
			});


        });
    </script>
</body>
</html>
