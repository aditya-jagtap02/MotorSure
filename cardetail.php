<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "motorsure";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize form data
$registerationno = trim($_POST['rc_number']);
$makemodel = trim($_POST['make_model']);
$year = trim($_POST['year']);
$chassisno = trim($_POST['chassis_number']);
$mileage = trim($_POST['mileage']);

// Validate all required fields
if (empty($registerationno) || empty($makemodel) || empty($year) || empty($chassisno) || empty($mileage)) {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit();
}

// Validate RC number format
if (!preg_match("/^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/", $registerationno)) {
    echo "<script>alert('Invalid RC number format!'); window.history.back();</script>";
    exit();
}

// Validate chassis number format
if (!preg_match("/^[A-Z0-9]{17}$/", $chassisno)) {
    echo "<script>alert('Invalid chassis number. It should be exactly 17 characters.'); window.history.back();</script>";
    exit();
}

// Check if chassis number already exists
$checkChassis = $conn->prepare("SELECT chassisno FROM cardetail WHERE chassisno = ?");
$checkChassis->bind_param("s", $chassisno);
$checkChassis->execute();
$checkChassis->store_result();
if ($checkChassis->num_rows > 0) {
    echo "<script>alert('Chassis number already exists!'); window.history.back();</script>";
    exit();
}
$checkChassis->close();

// Insert the data
$stmt = $conn->prepare("INSERT INTO cardetail (registerationno, makemodel, year, chassisno, mileage) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiss", $registerationno, $makemodel, $year, $chassisno, $mileage);

if ($stmt->execute()) {
    echo "<script>alert('Vehicle Registered Successfully!'); window.location.href = 'plans.html';</script>";
} else {
    echo "<script>alert('Error submitting form!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
