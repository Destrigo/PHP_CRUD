<?php include 'db.php'; 
include 'auth.php';
// Redirect to login if not logged in
requireLogin();
// Render the main header/navigation
renderHeader();
?>

<!DOCTYPE html>
<html>
<head>
    <title>StormBrainer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body data-theme="<?= $theme ?>">
<div class="stars"></div>
<div class="stars2"></div>
<div class="stars3"></div>
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
            <p>Rating: <?= $problem['rating'] ?></p>

            <?php if ($problem['user_id'] != $_SESSION['user_id']): ?>
            <form method="POST" action="rate.php">
                <input type="hidden" name="entity_type" value="problem">
                <input type="hidden" name="entity_id" value="<?= $problem['id'] ?>">
                <button type="submit">+1</button>
            </form>
            <?php endif; ?>
            [<a href="edit_problem.php?id=<?= $problem['id'] ?>">Edit</a>]
            [<a href="delete_problem.php?id=<?= $problem['id'] ?>">Delete</a>]
        </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
