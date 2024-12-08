<?php
include '../includes/db.php';
session_start();

// Ensure JSON response
header('Content-Type: application/json');

// Log PHP errors for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Validate session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? null;

// Handle adding items to the cart
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $item_id = $data['item_id'] ?? null;

    if (!$item_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item ID']);
        exit();
    }

    // Check if the item already exists in the cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if item exists
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $item_id);
        $stmt->execute();
    } else {
        // Insert new item into cart
        $sql = "INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $item_id);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
    exit();
}

// Handle decreasing item quantity
if ($action === 'decrease' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cart_id = $data['cart_id'] ?? null;

    if (!$cart_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid cart ID']);
        exit();
    }

    $sql = "UPDATE cart SET quantity = quantity - 1 WHERE id = ? AND user_id = ? AND quantity > 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity decreased']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cannot decrease quantity below 1']);
    }
    exit();
}

// Handle removing items from the cart
if ($action === 'remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cart_id = $data['cart_id'] ?? null;

    if (!$cart_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid cart ID']);
        exit();
    }

    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
    }
    exit();
}

// Default response for invalid actions
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit();
