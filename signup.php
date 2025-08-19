<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motorsure";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$mobile = $_POST['mobileno'];
$password = $_POST['password']; // No password hashing

// Check if email already exists
$check_email = "SELECT * FROM signup WHERE email = ?";
$stmt = $conn->prepare($check_email);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('Email already exists! Please use a different email.');
        window.location.href='signup.html';
    </script>";
    exit();
}

// Insert user data into the database
$sql = "INSERT INTO signup (name, email, mobileno, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $mobile, $password);

if ($stmt->execute()) {
    echo "<script>
        alert('Registration successful! Please login.');
        window.location.href='signin.html';
    </script>";
} else {
    echo "<script>
        alert('Error: " . $stmt->error . "');
        window.location.href='signup.html';
    </script>";
}

// Close connections
$stmt->close();
$conn->close();
?>
