<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost:3307";
$username = "root";  // No password needed
$password = "";      // Leave blank
$dbname = "jarvas_ai";  

// ✅ Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// ✅ Handle connection errors
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];

    if (empty($email) || empty($pass)) {
        die("❌ Error: All fields are required.");
    }

    // ✅ Use prepared statements
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_pass);
        $stmt->fetch();
        
        // ✅ Check password properly
        if (password_verify($pass, $hashed_pass)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;

            // ✅ Redirect to home page
            echo "<script>window.location.href = 'home.html';</script>";
            exit();
        } else {
            echo "❌ Error: Incorrect password.";
        }
    } else {
        echo "❌ Error: No account found with this email.";
    }

    $stmt->close();
}

$conn->close();
?>
