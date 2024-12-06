<?php
include 'includes/header.php';
include 'includes/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user details in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time(); // Track session activity

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<div class="flex items-center justify-center h-screen bg-gradient-to-br from-yellow-700 to-orange-300 animate-fade-in">
    <div class="w-full max-w-sm p-8 bg-white shadow-lg rounded-lg smooth-transition">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"> <?php echo $error; ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full p-3 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full p-3 border rounded" required>
            </div>
            <button type="submit" class="w-full p-3 mt-4 bg-brown-600 text-white rounded hover:bg-brown-800 transition duration-300">Login</button>
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
