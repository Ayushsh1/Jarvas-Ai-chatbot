<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost:3307";
$username = "root";  // Change if needed
$password = "";      // Change if your MySQL has a password
$dbname = "jarvas_ai";

// Create MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (!isset($_POST["username"], $_POST["email"], $_POST["password"], $_POST["confirm-password"])) {
        die("Error: Missing required fields.");
    }

    $user = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];
    $confirm_pass = $_POST["confirm-password"];

    // Ensure password and confirm password match
    if ($pass !== $confirm_pass) {
        die("Error: Passwords do not match.");
    }

    // Hash the password before storing it
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Insert user data into MySQL database
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $user, $email, $hashed_pass);

    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to login...";
        header("refresh:3;url=login.html"); // Redirect to login page after 3 seconds
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
