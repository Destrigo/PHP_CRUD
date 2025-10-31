<?php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username'=>$_POST['username']]);
$user = $stmt->fetch();

if ($user && password_verify($_POST['password'], $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header("Location: index.php");
    exit;
}
?>