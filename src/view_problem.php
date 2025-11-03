<?php
include 'db.php';
include 'auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get problem ID
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}
$problem_id = $_GET['id'];

// Fetch problem
$stmt = $conn->prepare("SELECT * FROM problems WHERE id = :id");
$stmt->execute(['id' => $problem_id]);
$problem = $stmt->fetch();

if (!$problem) {
    die("Problem not found.");
}

// Fetch solutions
$stmt = $conn->prepare("SELECT * FROM solutions WHERE problem_id = :problem_id ORDER BY rating DESC, created_at DESC");
$stmt->execute(['problem_id' => $problem_id]);
$solutions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($problem['title']) ?> - StormBrainer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body data-theme="<?= $theme ?>">
  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <main class="problem-view">
    <section class="problem-header">
      <h2><?= htmlspecialchars($problem['title']) ?></h2>
      <p class="description"><?= nl2br(htmlspecialchars($problem['description'])) ?></p>
      <p class="rating-display">⭐ <?= $problem['rating'] ?> points</p>

      <?php if ($problem['user_id'] != $user_id): ?>
        <form method="POST" action="rate.php">
          <input type="hidden" name="entity_type" value="problem">
          <input type="hidden" name="entity_id" value="<?= $problem['id'] ?>">
          <button class="btn small">+1 Problem</button>
        </form>
      <?php else: ?>
        <p class="note">You cannot +1 your own problem.</p>
      <?php endif; ?>

      <?php if ($problem['user_id'] == $user_id): ?>
        <p class="actions">
          <a href="edit_problem.php?id=<?= $problem['id'] ?>" class="btn">Edit</a>
          <a href="delete_problem.php?id=<?= $problem['id'] ?>" class="btn danger">Delete</a>
        </p>
      <?php endif; ?>
    </section>

    <hr>

    <section class="solutions">
      <h3>Solutions</h3>

      <?php if (count($solutions) === 0): ?>
        <p>No solutions yet. Be the first to suggest one!</p>
      <?php else: ?>
        <div class="solution-space">
          <?php foreach ($solutions as $s): ?>
            <?php
              $rating = (int)$s['rating'];
              $minSize = 70;
              $maxSize = 220;
              $size = $minSize + min($rating * 12, $maxSize - $minSize);
              $preview = htmlspecialchars(substr($s['content'], 0, 10));
            ?>
            <div 
              class="planet" 
              style="--size: <?= $size ?>px;" 
              data-full="<?= htmlspecialchars($s['content']) ?>"
            >
              <?= $preview ?>...
            </div>
          <?php endforeach; ?>
        </div>
        <div id="solution-detail"></div>
      <?php endif; ?>
    </section>

    <section class="add-solution">
      <h4>Add a Solution</h4>
      <form method="POST" action="add_solution.php">
        <input type="hidden" name="problem_id" value="<?= $problem['id'] ?>">
        <textarea name="content" required placeholder="Write your solution here..."></textarea><br>
        <button type="submit" class="btn primary">Add Solution</button>
      </form>
    </section>

    <p class="back-link"><a href="index.php">&larr; Back to Problems</a></p>
  </main>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const planets = document.querySelectorAll('.planet');
    const detail = document.getElementById('solution-detail');

    planets.forEach(planet => {
      planet.addEventListener('mouseenter', () => {
        detail.textContent = planet.dataset.full;
        detail.style.display = 'block';
      });
      planet.addEventListener('mouseleave', () => {
        detail.style.display = 'none';
      });
    });
  });
  </script>
</body>
</html>
