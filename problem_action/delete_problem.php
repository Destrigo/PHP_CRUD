<?php
include 'db.php'; // Connect to the database

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if an ID was provided in the URL
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}

if ($problem['user_id'] != $_SESSION['user_id']) {
    die("You are not authorized to edit this problem.");
}

$id = $_GET['id'];

// Delete the problem from the database
$stmt = $conn->prepare("DELETE FROM problems WHERE id = :id");
$stmt->execute(['id' => $id]);

// Redirect back to the problem list
header("Location: index.php");
exit;
?>
