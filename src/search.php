<?php
include 'db.php';
include 'auth.php';
requireLogin();
renderHeader();

$searchTerm = '';
$results = [];

if (isset($_GET['q'])) {
    $searchTerm = trim($_GET['q']);

    if (!empty($searchTerm)) {
        $stmt = $conn->prepare("
            SELECT * FROM problems
            WHERE title LIKE :search OR description LIKE :search
            ORDER BY created_at DESC
        ");
        $stmt->execute(['search' => "%$searchTerm%"]);
        $results = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Problems - StormBrainer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>🔍 Search Problems</h2>

    <form method="GET" action="search.php" class="search-form">
        <input
            type="text"
            name="q"
            placeholder="Enter keywords..."
            value="<?= htmlspecialchars($searchTerm) ?>"
            required
        >
        <button type="submit">Search</button>
    </form>

    <hr>

    <?php if (isset($_GET['q'])): ?>
        <h3>Results for: <em><?= htmlspecialchars($searchTerm) ?></em></h3>

        <?php if (count($results) > 0): ?>
            <ul class="search-results">
                <?php foreach ($results as $problem): ?>
                    <li class="problem-card">
                        <a href="view_problem.php?id=<?= $problem['id'] ?>">
                            <strong><?= htmlspecialchars($problem['title']) ?></strong>
                        </a>
                        <p><?= nl2br(htmlspecialchars(substr($problem['description'], 0, 120))) ?>...</p>
                        <p>Rating: <?= $problem['rating'] ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No problems found matching your search.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
