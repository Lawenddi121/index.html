<?php
// Dummy credentials for example
$valid_user = "admin";
$valid_pass = "1234";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    if ($user === $valid_user && $pass === $valid_pass) {
        echo "Login successful! Welcome, " . htmlspecialchars($user);
    } else {
        echo "Invalid username or password.";
    }
}
?>
