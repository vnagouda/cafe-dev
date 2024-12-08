<?php include '../includes/session_validation.php'; ?>

<?php
include '../includes/header.php';
include '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check user session and role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// Retrieve user's name from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'];
} else {
    $user_name = "Guest";
}

// Fetch 3 random highlights from the menu_items table
$highlights_query = "SELECT name, description, price, image FROM menu_items ORDER BY RAND() LIMIT 3";
$highlights_result = $conn->query($highlights_query);

$highlights = [];
if ($highlights_result && $highlights_result->num_rows > 0) {
    while ($row = $highlights_result->fetch_assoc()) {
        $highlights[] = $row;
    }
}
?>

<!-- Include External Styles -->
<link rel="stylesheet" href="../assets/css/styles.css">

<div class="bg-gradient-to-br from-yellow-700 to-orange-300 min-h-screen p-10">
    <div class="container mx-auto text-center text-white space-y-10">
        <!-- Welcome Section -->
        <div>
            <h1 class="text-6xl font-bold mb-4 animate-fade-in">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p class="text-2xl">Explore our menu, check your orders, and enjoy exclusive deals!</p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <a href="menu.php" class="bg-white text-yellow-700 py-6 px-12 rounded-lg shadow-lg text-xl font-semibold transition hover:bg-yellow-700 hover:text-white">
                Explore Menu
            </a>
            <a href="order_history.php" class="bg-white text-yellow-700 py-6 px-12 rounded-lg shadow-lg text-xl font-semibold transition hover:bg-yellow-700 hover:text-white">
                View Orders
            </a>
            <a href="offers.php" class="bg-white text-yellow-700 py-6 px-12 rounded-lg shadow-lg text-xl font-semibold transition hover:bg-yellow-700 hover:text-white">
                Check Offers
            </a>
        </div>

        <!-- Highlights Section -->
        <div>
            <h2 class="text-4xl font-bold mb-6">Today’s Highlights</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <?php if (!empty($highlights)): ?>
                    <?php foreach ($highlights as $item): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition duration-300">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-64 object-cover rounded-t-lg">
                            <h3 class="text-2xl font-semibold text-yellow-700 mt-4"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="text-green-600 font-bold mt-4">Price: $<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-300">No highlights available for today. Check back later!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Ripple Effect JavaScript
    document.querySelectorAll('.ripple-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            // Get button's position
            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');

            // Set ripple's position
            ripple.style.left = `${e.clientX - rect.left}px`;
            ripple.style.top = `${e.clientY - rect.top}px`;

            // Add ripple to button
            ripple.classList.add('ripple');
            this.appendChild(ripple);

            // Remove ripple after animation
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
</script>
