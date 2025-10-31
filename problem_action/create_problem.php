<?php
include 'db.php';
include 'auth.php';

// Redirect to login if not logged in
requireLogin();

// Render the main header/navigation
renderHeader();

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("INSERT INTO problems (title, description, user_id) VALUES (:title, :description, :user_id)");
$stmt->execute(['title'=>$title, 'description'=>$description, 'user_id'=>$user_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO problems (title, description) VALUES (:title, :description)");
    $stmt->execute(['title' => $title, 'description' => $description]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Problem</title></head>
<link rel="stylesheet" href="style.css">
<body>
<h2>Add Problem</h2>
<form method="POST">
    Title:<br>
    <input type="text" name="title" required><br><br>
    Description:<br>
    <textarea name="description"></textarea><br><br>
    <button type="submit">Add Problem</button>
</form>
<a href="index.php">Back</a>
</body>
</html>
