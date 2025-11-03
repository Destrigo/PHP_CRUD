<?php
include 'db.php';
include 'auth.php';
requireLogin();
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) die("Problem ID not specified.");
$problem_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM problems WHERE id = :id");
$stmt->execute(['id' => $problem_id]);
$problem = $stmt->fetch();
if (!$problem) die("Problem not found.");

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

<!-- ANIMATED STAR BACKGROUND -->
<div class="stars"></div>
<div class="stars2"></div>
<div class="stars3"></div>

<main class="problem-view">
  <section class="problem-header">
    <h2><?= htmlspecialchars($problem['title']) ?></h2>
    <p class="description"><?= nl2br(htmlspecialchars($problem['description'])) ?></p>
    <p class="rating-display">⭐ <?= $problem['rating'] ?> points</p>
  </section>

  <hr>

  <section class="solutions">
    <h3>Solutions</h3>
    <?php if (empty($solutions)): ?>
      <p>No solutions yet. Be the first!</p>
    <?php else: ?>
      <div class="solution-space">
        <?php foreach ($solutions as $s): 
          $rating = (int)$s['rating'];
          $minSize = 70;
          $maxSize = 220;
          $size = $minSize + min($rating * 12, $maxSize - $minSize);
          $preview = htmlspecialchars(substr($s['content'], 0, 10));
          $full = htmlspecialchars($s['content']);
        ?>
        <div class="planet" style="--size: <?= $size ?>px" data-full="<?= $full ?>">
          <?= $preview ?>...
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <div id="solution-detail"></div>

</main>

<script>
document.querySelectorAll('.planet').forEach(planet => {
    planet.addEventListener('mouseenter', () => {
        const detail = document.getElementById('solution-detail');
        detail.textContent = planet.dataset.full;
        detail.style.display = 'block';
    });
    planet.addEventListener('mouseleave', () => {
        document.getElementById('solution-detail').style.display = 'none';
    });
});
</script>

</body>
</html>
