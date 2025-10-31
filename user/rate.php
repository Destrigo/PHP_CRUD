<?php
include 'db.php';
include 'auth.php';

// Redirect to login if not logged in
requireLogin();

$user_id = $_SESSION['user_id'];
$entity_type = $_POST['entity_type'];
$entity_id = $_POST['entity_id'];

// Check if user is the author
if ($entity_type === 'problem') {
    $stmt = $conn->prepare("SELECT user_id FROM problems WHERE id = :id");
} else {
    $stmt = $conn->prepare("SELECT user_id, problem_id FROM solutions WHERE id = :id");
}
$stmt->execute(['id' => $entity_id]);
$entity = $stmt->fetch();

if (!$entity) {
    die("Item not found.");
}

// Prevent voting on own item
if ($entity['user_id'] == $user_id) {
    die("You cannot +1 your own " . $entity_type . ".");
}

// Check if user already voted
$stmt = $conn->prepare("SELECT * FROM votes WHERE user_id = :user_id AND entity_type = :entity_type AND entity_id = :entity_id");
$stmt->execute([
    'user_id' => $user_id,
    'entity_type' => $entity_type,
    'entity_id' => $entity_id
]);

if ($stmt->fetch()) {
    die("You have already voted.");
}

// Insert vote
$insert = $conn->prepare("INSERT INTO votes (user_id, entity_type, entity_id) VALUES (:user_id, :entity_type, :entity_id)");
$insert->execute([
    'user_id' => $user_id,
    'entity_type' => $entity_type,
    'entity_id' => $entity_id
]);

// Update rating
if ($entity_type === 'problem') {
    $update = $conn->prepare("UPDATE problems SET rating = rating + 1 WHERE id = :id");
    $update->execute(['id' => $entity_id]);
    header("Location: view_problem.php?id=$entity_id");
} else {
    $update = $conn->prepare("UPDATE solutions SET rating = rating + 1 WHERE id = :id");
    $update->execute(['id' => $entity_id]);

    // Redirect to parent problem
    $problem_id = $entity['problem_id'];
    header("Location: view_problem.php?id=$problem_id");
}
exit;
?>
