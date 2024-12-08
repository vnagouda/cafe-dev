<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle Add, Update, and Remove operations here...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Manage Inventory</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <h1 class="text-4xl font-bold text-center mb-10">Manage Inventory</h1>

        <!-- Inventory Table -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Price</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $menu_query = "SELECT * FROM menu_items";
                    $menu_result = $conn->query($menu_query);
                    while ($item = $menu_result->fetch_assoc()): ?>
                        <tr>
                            <td class="p-4"><?= $item['id']; ?></td>
                            <td class="p-4"><?= htmlspecialchars($item['name']); ?></td>
                            <td class="p-4">$<?= number_format($item['price'], 2); ?></td>
                            <td class="p-4">
                                <a href="edit_item.php?id=<?= $item['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
                                <a href="delete_item.php?id=<?= $item['id']; ?>" class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
