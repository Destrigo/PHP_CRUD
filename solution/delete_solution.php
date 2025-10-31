<?php
include 'db.php'; // Connect to database
include 'auth.php';

// Redirect to login if not logged in
requireLogin();

$user_id = $_SESSION['user_id'];

// Check if solution ID is provided
if (!isset($_GET['id'])) {
    die("Solution ID not specified.");
}

if ($problem['user_id'] != $_SESSION['user_id']) {
    die("You are not authorized to edit this problem.");
}

$id = $_GET['id'];

// Get the problem_id first (so we can redirect back to the problem page)
$stmt = $conn->prepare("SELECT problem_id FROM solutions WHERE id = :id");
$stmt->execute(['id' => $id]);
$solution = $stmt->fetch();

if (!$solution) {
    die("Solution not found.");
}

$problem_id = $solution['problem_id'];

// Delete the solution
$delStmt = $conn->prepare("DELETE FROM solutions WHERE id = :id");
$delStmt->execute(['id' => $id]);

// Redirect back to the problem view page
header("Location: view_problem.php?id=$problem_id");
exit;
?>
