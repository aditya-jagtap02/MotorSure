<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login to continue']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// For dummy payments, we'll always consider it successful
if ($data['status'] === 'success') {
    // Create order in database
    $userId = $_SESSION['user_id'];
    $planType = $data['planType'];
    $amount = $data['amount'] * 100; // Convert to paise
    $orderDate = date('Y-m-d H:i:s');
    $status = 'completed';
    $paymentId = $data['payment_id'];
    
    // Insert order
    $sql = "INSERT INTO orders (user_id, plan_type, amount, order_date, status, payment_id, payment_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $userId, $planType, $amount, $orderDate, $status, $paymentId);
    
    if ($stmt->execute()) {
        // Update user's active plan
        $sql = "UPDATE users SET 
                active_plan = ?,
                plan_start_date = NOW(),
                plan_end_date = DATE_ADD(NOW(), INTERVAL 1 YEAR)
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $planType, $userId);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update order status']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Payment failed']);
}
?> 