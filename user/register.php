<?php
$hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
$stmt->execute(['username'=>$_POST['username'], 'email'=>$_POST['email'], 'password'=>$hashedPassword]);
?>