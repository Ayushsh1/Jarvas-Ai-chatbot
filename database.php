<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "jarvas_ai";

// Connect to the existing database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$errors = array();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];
    $confirm_pass = $_POST["confirm-password"];

    if (empty($user) || empty($email) || empty($pass) || empty($confirm_pass)) {
        array_push($errors, "All fields are required.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Invalid email format.");
    }
    if ($pass !== $confirm_pass) {
        array_push($errors, "Passwords do not match.");
    }
    if (strlen($pass) < 6) {
        array_push($errors, "Password must be at least 6 characters.");
    }

    // Check if username or email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        array_push($errors, "Username or email already exists.");
    }
    $stmt->close();

    // Insert user into database
    if (count($errors) == 0) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $email, $hashed_pass);
        if ($stmt->execute()) {
            $_SESSION['username'] = $user;
            header("Location: login.html"); // Redirect to login page
            exit();
        } else {
            array_push($errors, "Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
?>
