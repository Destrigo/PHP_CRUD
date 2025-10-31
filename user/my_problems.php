<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch problems created by this user
$stmt = $conn->prepare("SELECT * FROM problems WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$problems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Problems - StormBrainer</title>
</head>
<body>
    <h2>My Problems</h2>
    <a href="create_problem.php">+ Add New Problem</a>

    <?php if (empty($problems)): ?>
        <p>You have not added any problems yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach($problems as $problem): ?>
            <li>
                <!-- Link to view_problem.php to see solutions -->
                <a href="view_problem.php?id=<?= $problem['id'] ?>">
                    <?= htmlspecialchars($problem['title']) ?>
                </a>

                <!-- Edit/Delete links for this user’s own problem -->
                [<a href="edit_problem.php?id=<?= $problem['id'] ?>">Edit</a>]
                [<a href="delete_problem.php?id=<?= $problem['id'] ?>">Delete</a>]

                <!-- Display rating -->
                <p>Rating: <?= $problem['rating'] ?></p>

                <!-- Count and link to solutions -->
                <?php
                $stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM solutions WHERE problem_id = :id");
                $stmt2->execute(['id' => $problem['id']]);
                $solution_count = $stmt2->fetch()['total'];
                ?>
                <p>
                    <a href="view_problem.php?id=<?= $problem['id'] ?>">
                        View <?= $solution_count ?> Solution<?= $solution_count != 1 ? 's' : '' ?>
                    </a>
                </p>
            </li>
            <hr>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="index.php">Back to All Problems</a></p>
</body>
</html>
