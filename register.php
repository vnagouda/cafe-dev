<?php
include 'includes/header.php';
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = 'user';  // Default role is user

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        $error = "Registration failed!";
    }
}
?>
<div class="flex items-center justify-center h-screen bg-gradient-to-br from-yellow-700 to-orange-300 animate-fade-in">
    <div class="w-full max-w-sm p-8 bg-white shadow-lg rounded-lg smooth-transition">
        <h2 class="text-2xl font-bold text-center mb-6">Register</h2>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="name" class="block text-sm mb-2">Name</label>
                <input type="text" name="name" id="name" class="w-full p-3 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full p-3 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full p-3 border rounded" required>
            </div>
            <button type="submit" class="w-full p-3 mt-4 bg-brown-600 text-white rounded hover:bg-brown-800 transition duration-300">Register</button>
        </form>
    </div>
</div>
<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fade-in 1s ease-in;
    }
    .smooth-transition {
        transition: opacity 0.3s ease-in-out;
    }
</style>
