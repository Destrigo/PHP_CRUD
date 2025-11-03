<?php
include 'db.php';
session_start();

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $error = "Username or email already taken.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            // Optionally, log in the user automatically
            $user_id = $conn->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - StormBrainer</title>
	<link rel="stylesheet" href="style.css">
</head>
<body data-theme="<?= $theme ?>">
<div class="stars"></div>
<div class="stars2"></div>
<div class="stars3"></div>
    <h2>Register for StormBrainer</h2>

    <!-- Show error if any -->
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label>Username:</label><br>
        <input type="text" name="username" required placeholder="Username"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required placeholder="Email"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required placeholder="Password"><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required placeholder="Confirm Password"><br><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
