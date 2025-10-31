<?php
include 'db.php'; // Connect to database

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if an ID was provided in the URL
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}

if ($problem['user_id'] != $_SESSION['user_id']) {
    die("You are not authorized to edit this problem.");
}

$id = $_GET['id'];

// Fetch the problem data from the database
$stmt = $conn->prepare("SELECT * FROM problems WHERE id = :id");
$stmt->execute(['id' => $id]);
$problem = $stmt->fetch();

if (!$problem) {
    die("Problem not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $updateStmt = $conn->prepare("UPDATE problems SET title = :title, description = :description WHERE id = :id");
    $updateStmt->execute([
        'title' => $title,
        'description' => $description,
        'id' => $id
    ]);

    // Redirect back to the problem list
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Problem - StormBrainer</title>
</head>
<body>
    <h2>Edit Problem</h2>

    <form method="POST">
        <!-- Problem Title -->
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($problem['title']) ?>" required><br><br>

        <!-- Problem Description -->
        <label>Description:</label><br>
        <textarea name="description" rows="5" cols="50"><?= htmlspecialchars($problem['description']) ?></textarea><br><br>

        <!-- Submit Button -->
        <button type="submit">Save Changes</button>
    </form>

    <br>
    <a href="index.php">Back to Problem List</a>
</body>
</html>
