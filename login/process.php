<?php
session_start();

// Database configuration
$host = "localhost"; // usually localhost
$dbname = "login_db";
$username = "root"; // XAMPP default username
$password = "";     // XAMPP default password is empty

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if login form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Sanitize input
    $user = mysqli_real_escape_string($conn, $user);

    // Query the database
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify encrypted password
        if (password_verify($pass, $row['password'])) {
            $_SESSION['username'] = $user;
            echo "Login successful! Welcome, " . htmlspecialchars($user) . ".";
            // Redirect to a protected page if needed
            // header("Location: dashboard.php");
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>
