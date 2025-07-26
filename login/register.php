<?php
// Show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL config
$host = 'localhost';
$db   = 'login_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set up PDO connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if username exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already exists.';
        }
    }

    // Insert user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed]);
        $success = "✅ Registered successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <style>
    body { font-family: Arial; background:#eee; display:flex; justify-content:center; align-items:center; height:100vh; }
    .box { background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px #aaa; width:300px; }
    .box input { width:100%; padding:8px; margin:8px 0; }
    .error { color:red; margin-bottom:10px; }
    .success { color:green; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
      <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <input type="submit" value="Register">
    </form>
  </div>
</body>
</html>
