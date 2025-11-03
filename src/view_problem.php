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

$theme = $_SESSION['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($problem['title']) ?> - StormBrainer</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .planet-container {
      position: relative;
      display: grid;
      place-items: center;
      min-height: 80vh;
      overflow: hidden;
    }

    .solution-planet {
      position: absolute;
      top: 50%;
      left: 50%;
      border-radius: 50%;
      transform-origin: -50% center;
      animation: orbit linear infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      font-size: 0.8em;
      text-shadow: 0 0 5px rgba(0,0,0,0.8);
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .solution-planet:hover {
      transform: scale(1.2) translate(-50%, -50%);
      z-index: 5;
    }

    @keyframes orbit {
      to { transform: rotate(360deg); }
    }

    .solution-tooltip {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,0.85);
      color: white;
      padding: 12px 20px;
      border-radius: 12px;
      text-align: center;
      display: none;
      z-index: 999;
      max-width: 500px;
      white-space: pre-wrap;
    }

    body[data-theme="light"] .solution-tooltip {
      background: rgba(255,255,255,0.9);
      color: #111;
    }

    .btn.tiny {
      font-size: 0.7em;
      padding: 3px 8px;
      margin: 0 3px;
    }

    .note.small {
      font-size: 0.7em;
      opacity: 0.8;
    }

    .problem-header {
      text-align: center;
      margin-top: 2rem;
    }
  </style>
</head>

<body data-theme="<?= htmlspecialchars($theme) ?>">
  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>
  <div class="solar-system">
    <ol>
      <li class="sun"></li>
      <li class="mercury"></li>
      <li class="venus"></li>
      <li class="earth"></li>
      <li class="mars"></li>
      <li class="jupiter"></li>
      <li class="saturn"></li>
      <li class="uranus"></li>
      <li class="neptune"></li>
    </ol>
  </div>

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
      <h3 style="text-align:center;">Solutions</h3>

      <?php if (count($solutions) === 0): ?>
        <p style="text-align:center;">No solutions yet. Be the first to suggest one!</p>
      <?php else: ?>
        <div class="planet-container">
          <?php 
          $planets = ['mercury','venus','earth','mars','jupiter','saturn','uranus','neptune'];
          foreach ($solutions as $index => $s):
            $planet = $planets[array_rand($planets)];
            $rating = max(1, (int)$s['rating']);
            $size = 40 + ($rating * 6);
            $orbit = 120 + ($index * 50);
            $speed = 10 + $index * 5;
          ?>
            <div class="solution-planet <?= $planet ?>"
              style="
                width: <?= $size ?>px;
                height: <?= $size ?>px;
                animation-duration: <?= $speed ?>s;
                transform: rotate(0deg) translateX(<?= $orbit ?>px);
              "
              data-full="<?= htmlspecialchars($s['content']) ?>"
              data-rating="<?= $rating ?>"
              data-id="<?= $s['id'] ?>"
              data-user="<?= $s['user_id'] ?>"
            >
              <?= htmlspecialchars(substr($s['content'], 0, 10)) ?>...
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="add-solution" style="text-align:center;margin-top:2rem;">
      <h4>Add a Solution</h4>
      <form method="POST" action="add_solution.php">
        <input type="hidden" name="problem_id" value="<?= $problem['id'] ?>">
        <textarea name="content" required placeholder="Write your solution here..."></textarea><br>
        <button type="submit" class="btn primary">Add Solution</button>
      </form>
    </section>

    <p class="back-link" style="text-align:center;margin-top:2rem;">
      <a href="index.php">&larr; Back to Problems</a>
    </p>
  </main>

  <div id="solution-tooltip" class="solution-tooltip"></div>

  <script>
    const tooltip = document.getElementById('solution-tooltip');
    const userId = <?= json_encode($user_id) ?>;

    document.querySelectorAll('.solution-planet').forEach(p => {
      p.addEventListener('mouseenter', () => {
        const id = p.dataset.id;
        const full = p.dataset.full;
        const rating = p.dataset.rating;
        const author = parseInt(p.dataset.user);

        let html = `<strong>⭐ ${rating}</strong><br>${full}<br>`;
        if (author === userId) {
          html += `<br>
            <a href="edit_solution.php?id=${id}" class="btn tiny">Edit</a>
            <a href="delete_solution.php?id=${id}" class="btn tiny danger">Delete</a>
          `;
        } else {
          html += `
            <form method='POST' action='rate.php'>
              <input type='hidden' name='entity_type' value='solution'>
              <input type='hidden' name='entity_id' value='${id}'>
              <button class='btn tiny'>+1</button>
            </form>
          `;
        }
        tooltip.innerHTML = html;
        tooltip.style.display = 'block';
      });
      p.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
      });
    });
  </script>
</body>
</html>
