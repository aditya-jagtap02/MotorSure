<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login to continue']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$planType = $data['planType'];
$amount = $data['amount'] * 100; // Convert to paise

// Create order in database
$userId = $_SESSION['user_id'];
$orderDate = date('Y-m-d H:i:s');
$status = 'pending';

$sql = "INSERT INTO orders (user_id, plan_type, amount, order_date, status) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isiss", $userId, $planType, $amount, $orderDate, $status);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Failed to create order']);
    exit;
}

$orderId = $conn->insert_id;

// Create Razorpay order
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => $amount,
    'currency' => 'INR',
    'receipt' => 'order_' . $orderId
]));
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'Failed to create Razorpay order']);
    exit;
}

$razorpayOrder = json_decode($response, true);

// Update order with Razorpay order ID
$sql = "UPDATE orders SET razorpay_order_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $razorpayOrder['id'], $orderId);
$stmt->execute();

echo json_encode([
    'key' => RAZORPAY_KEY_ID,
    'amount' => $amount,
    'order_id' => $razorpayOrder['id']
]);
?> 