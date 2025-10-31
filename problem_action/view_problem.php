<?php
include 'db.php';
include 'auth.php';

// Redirect to login if not logged in
requireLogin();

// Render the main header/navigation
renderHeader();

$user_id = $_SESSION['user_id'];

// Get problem ID from URL
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}
$problem_id = $_GET['id'];

// Fetch the problem
$stmt = $conn->prepare("SELECT * FROM problems WHERE id = :id");
$stmt->execute(['id' => $problem_id]);
$problem = $stmt->fetch();

if (!$problem) {
    die("Problem not found.");
}

// Fetch solutions for this problem
$stmt = $conn->prepare("SELECT * FROM solutions WHERE problem_id = :problem_id ORDER BY created_at DESC");
$stmt->execute(['problem_id' => $problem_id]);
$solutions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($problem['title']) ?> - StormBrainer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2><?= htmlspecialchars($problem['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($problem['description'])) ?></p>
    <p>Rating: <?= $problem['rating'] ?></p>

    <?php if ($problem['user_id'] != $user_id): ?>
    <form method="POST" action="rate.php">
        <input type="hidden" name="entity_type" value="problem">
        <input type="hidden" name="entity_id" value="<?= $problem['id'] ?>">
        <button type="submit">+1</button>
    </form>
    <?php else: ?>
    <p>You cannot +1 your own problem</p>
    <?php endif; ?>

    <!-- Edit/Delete links for the author -->
    <?php if ($problem['user_id'] == $user_id): ?>
    <p>
        <a href="edit_problem.php?id=<?= $problem['id'] ?>">Edit Problem</a> |
        <a href="delete_problem.php?id=<?= $problem['id'] ?>">Delete Problem</a>
    </p>
    <?php endif; ?>

    <hr>

    <h3>Solutions</h3>
    <ul>
        <?php foreach($solutions as $solution): ?>
        <li>
            <?= nl2br(htmlspecialchars($solution['content'])) ?>
            <p>Rating: <?= $solution['rating'] ?></p>

            <?php if ($solution['user_id'] != $user_id): ?>
            <form method="POST" action="rate.php">
                <input type="hidden" name="entity_type" value="solution">
                <input type="hidden" name="entity_id" value="<?= $solution['id'] ?>">
                <button type="submit">+1</button>
            </form>
            <?php else: ?>
            <p>You cannot +1 your own solution</p>
            <?php endif; ?>

            <!-- Edit/Delete links for the author -->
            <?php if ($solution['user_id'] == $user_id): ?>
            <p>
                <a href="edit_solution.php?id=<?= $solution['id'] ?>">Edit</a> |
                <a href="delete_solution.php?id=<?= $solution['id'] ?>">Delete</a>
            </p>
            <?php endif; ?>
        </li>
        <hr>
        <?php endforeach; ?>
    </ul>

    <!-- Add new solution -->
    <h4>Add a Solution</h4>
    <form method="POST" action="add_solution.php">
        <input type="hidden" name="problem_id" value="<?= $problem['id'] ?>">
        <textarea name="content" required placeholder="Your solution here..."></textarea><br>
        <button type="submit">Add Solution</button>
    </form>

    <p><a href="index.php">Back to Problems</a></p>
</body>
</html>
