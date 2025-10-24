<?php
header('Content-Type: application/json');
include('../db.php');

// Simple helper to respond with JSON
function respond($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request method.');
}

// Collect POST data
$book_id = $_POST['book_id'] ?? null;
$type = $_POST['type'] ?? null;
$currency = $_POST['currency'] ?? 'NGN';
$price = $_POST['price'] ?? 0;

// Validate required fields
if (!$book_id || !$type || !$price) {
    respond('error', 'Missing required fields.');
}

// If physical, collect shipping info
$shipping = [];
if ($type === 'physical') {
    $shipping_fields = ['fullName', 'phone', 'address', 'country', 'state', 'city'];
    foreach ($shipping_fields as $field) {
        if (empty($_POST[$field])) {
            respond('error', "Missing shipping field: $field");
        }
        $shipping[$field] = trim($_POST[$field]);
    }
    // Save shipping info (optional)
    $stmt = $conn->prepare("INSERT INTO orders (book_id, full_name, phone, address, country, state, city, price, currency, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "issssssdss",
        $book_id,
        $shipping['fullName'],
        $shipping['phone'],
        $shipping['address'],
        $shipping['country'],
        $shipping['state'],
        $shipping['city'],
        $price,
        $currency,
        $type
    );
    $stmt->execute();
} else {
    // For digital books, save minimal order info
    $stmt = $conn->prepare("INSERT INTO orders (book_id, price, currency, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $book_id, $price, $currency, $type);
    $stmt->execute();
}

// Fetch the inserted order ID
$order_id = $stmt->insert_id;

// Generate payment link (mock)
$payment_url = "https://your-payment-gateway.com/pay?order_id={$order_id}&amount={$price}&currency={$currency}";

// Respond with success
respond('success', 'Order created successfully', [
    'order_id' => $order_id,
    'payment_url' => $payment_url
]);
?>
