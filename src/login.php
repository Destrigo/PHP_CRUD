<?php
include 'db.php'; // Connessione al database
session_start();

// Se l’utente è già loggato, reindirizza alla home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

// Controlla se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cerca l’utente nel database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verifica se utente esiste e password è corretta
    if ($user && password_verify($password, $user['password'])) {
        // Login riuscito: salva dati nella sessione
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Reindirizza alla home
        header("Location: index.php");
        exit;
    } else {
        // Login fallito
        $error = "Username o password non corretti.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - StormBrainer</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login to StormBrainer</h2>

    <!-- Mostra messaggio di errore -->
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Form di login -->
    <form method="POST" action="login.php">
        <label>Username:</label><br>
        <input type="text" name="username" required placeholder="Username"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required placeholder="Password"><br><br>

        <button type="submit">Login</button>
    </form>

    <p>You don't have an account? <a href="register.php">Register here!</a></p>
</body>
</html>
