<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'insurance_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create orders table if not exists
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_type VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    order_date DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    payment_id VARCHAR(100),
    payment_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

$conn->query($sql);

// Add active_plan column to users table if not exists
$sql = "ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS active_plan VARCHAR(50),
        ADD COLUMN IF NOT EXISTS plan_start_date DATETIME,
        ADD COLUMN IF NOT EXISTS plan_end_date DATETIME";

$conn->query($sql);
?> 