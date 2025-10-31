<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>StormBrainer</title>
</head>
<body>
    <h2>Problems</h2>
    <a href="create_problem.php">+ Add New Problem</a>

    <?php
    $stmt = $conn->query("SELECT * FROM problems ORDER BY created_at DESC");
    $problems = $stmt->fetchAll();
    ?>

    <ul>
        <?php foreach($problems as $problem): ?>
        <li>
            <a href="view_problem.php?id=<?= $problem['id'] ?>">
                <?= htmlspecialchars($problem['title']) ?>
            </a>
            [<a href="edit_problem.php?id=<?= $problem['id'] ?>">Edit</a>]
            [<a href="delete_problem.php?id=<?= $problem['id'] ?>">Delete</a>]
        </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
