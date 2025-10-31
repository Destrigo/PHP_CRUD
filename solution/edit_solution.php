<?php
include 'db.php'; // Connect to database

// Check if solution ID is provided
if (!isset($_GET['id'])) {
    die("Solution ID not specified.");
}

$id = $_GET['id'];

// Fetch solution from database
$stmt = $conn->prepare("SELECT * FROM solutions WHERE id = :id");
$stmt->execute(['id' => $id]);
$solution = $stmt->fetch();

if (!$solution) {
    die("Solution not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    $updateStmt = $conn->prepare("UPDATE solutions SET content = :content WHERE id = :id");
    $updateStmt->execute([
        'content' => $content,
        'id' => $id
    ]);

    // Redirect back to the problem page
    header("Location: view_problem.php?id=" . $solution['problem_id']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Solution - StormBrainer</title>
</head>
<body>
    <h2>Edit Solution</h2>

    <form method="POST">
        <textarea name="content" rows="5" cols="50" required><?= htmlspecialchars($solution['content']) ?></textarea><br><br>
        <button type="submit">Save Changes</button>
    </form>

    <br>
    <a href="view_problem.php?id=<?= $solution['problem_id'] ?>">Back to Problem</a>
</body>
</html>
