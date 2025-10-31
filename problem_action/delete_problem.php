<?php
include 'db.php'; // Connect to the database

// Check if an ID was provided in the URL
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}

$id = $_GET['id'];

// Delete the problem from the database
$stmt = $conn->prepare("DELETE FROM problems WHERE id = :id");
$stmt->execute(['id' => $id]);

// Redirect back to the problem list
header("Location: index.php");
exit;
?>
