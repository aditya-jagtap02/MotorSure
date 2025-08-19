<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motorsure";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password']; // No password hashing

$stmt = $conn->prepare("SELECT password FROM signup WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Directly compare passwords without hashing
    if ($password === $row['password']) {
        $_SESSION['email'] = $email;
        setcookie('email', $email, time() + 3600, "/", "", true, true);

       // $stmt = $conn->prepare("UPDATE visited SET count = count + 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        echo "<script> window.location.href='cardetail.html';</script>";
    } else {
        echo "<script>alert('Incorrect password. Please try again.'); window.location.href='signin.html';</script>";
    }
} else {
    echo "<script>alert('No account found with this email. Please sign up first.'); window.location.href='signup.html';</script>";
}

$stmt->close();
$conn->close();
?>
