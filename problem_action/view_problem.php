<?php include 'db.php';

$id = $_GET['id'];
// Fetch the problem
$stmt = $conn->prepare("SELECT * FROM problems WHERE id=:id");
$stmt->execute(['id'=>$id]);
$problem = $stmt->fetch();

// Fetch solutions
$stmt = $conn->prepare("SELECT * FROM solutions WHERE problem_id=:id ORDER BY created_at DESC");
$stmt->execute(['id'=>$id]);
$solutions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($problem['title']) ?></title></head>
<body>
<a href="index.php">Back</a>
<h2><?= htmlspecialchars($problem['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($problem['description'])) ?></p>

<h3>Solutions</h3>
<ul>
<?php foreach($solutions as $s): ?>
    <li>
        <?= nl2br(htmlspecialchars($s['content'])) ?>
        [<a href="delete_solution.php?id=<?= $s['id'] ?>&problem_id=<?= $problem['id'] ?>">Delete</a>]
    </li>
<?php endforeach; ?>
</ul>

<h4>Add a Solution</h4>
<form method="POST" action="add_solution.php">
    <input type="hidden" name="problem_id" value="<?= $problem['id'] ?>">
    <textarea name="content" required></textarea><br>
    <button>Add Solution</button>
</form>
</body>
</html>
