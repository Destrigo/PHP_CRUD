<?php
include 'db.php';
include 'auth.php';
requireLogin();
renderHeader();

$theme = $_SESSION['theme'] ?? 'light';

// Fetch problems
$stmt = $conn->query("SELECT * FROM problems ORDER BY created_at DESC");
$problems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StormBrainer</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      overflow: hidden;
    }

    .planet-container {
      position: relative;
      display: grid;
      place-items: center;
      min-height: 85vh;
      overflow: hidden;
    }

    .problem-planet {
      position: absolute;
      top: 50%;
      left: 50%;
      border-radius: 50%;
      transform-origin: -50% center;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.9em;
      text-shadow: 0 0 5px rgba(0,0,0,0.8);
      cursor: pointer;
      transition: transform 0.3s ease;
      animation: orbit 40s linear infinite;
      text-align: center;
    }

    .problem-planet:hover {
      transform: scale(1.15) translate(-50%, -50%);
      z-index: 5;
    }

    @keyframes orbit {
      to { transform: rotate(360deg); }
    }

    .problem-tooltip {
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
    }

    body[data-theme="light"] .problem-tooltip {
      background: rgba(255,255,255,0.9);
      color: #111;
    }

    .add-btn {
      display: inline-block;
      margin: 20px auto;
      padding: 10px 18px;
      border-radius: 10px;
      background: #2e86de;
      color: white;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }

    .add-btn:hover {
      background: #1b4f9c;
    }

    h2 {
      text-align: center;
      margin-top: 1.5rem;
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

  <main>
    <h2>Problems</h2>
    <div style="text-align:center;">
      <a href="create_problem.php" class="add-btn">+ Add New Problem</a>
    </div>

    <?php if (count($problems) === 0): ?>
      <p style="text-align:center;">No problems yet. Add one to start the galaxy!</p>
    <?php else: ?>
      <div class="planet-container">
        <?php 
        $planets = ['mercury','venus','earth','mars','jupiter','saturn','uranus','neptune'];
        $count = count($problems);
        foreach ($problems as $i => $p):
          $planet = $planets[array_rand($planets)];
          $rating = max(1, (int)$p['rating']);
          $size = 45 + ($rating * 7);
          $orbit = 180 + ($i * 70);
          $angle = ($i / $count) * 360;
        ?>
          <div 
            class="problem-planet <?= $planet ?>" 
            style="width: <?= $size ?>px; height: <?= $size ?>px; transform: rotate(<?= $angle ?>deg) translateX(<?= $orbit ?>px);"
            data-id="<?= $p['id'] ?>"
            data-title="<?= htmlspecialchars($p['title']) ?>"
            data-rating="<?= $rating ?>"
            data-user="<?= $p['user_id'] ?>"
          >
            <?= htmlspecialchars(substr($p['title'], 0, 10)) ?>...
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <div id="problem-tooltip" class="problem-tooltip"></div>

  <script>
    const tooltip = document.getElementById('problem-tooltip');
    const userId = <?= json_encode($_SESSION['user_id']) ?>;

    document.querySelectorAll('.problem-planet').forEach(p => {
      p.addEventListener('mouseenter', () => {
        const id = p.dataset.id;
        const title = p.dataset.title;
        const rating = p.dataset.rating;
        const author = parseInt(p.dataset.user);

        let html = `<strong>${title}</strong><br>⭐ ${rating} points<br><br>
        <a href="view_problem.php?id=${id}" class="btn tiny">View</a> `;

        if (author === userId) {
          html += `<a href="edit_problem.php?id=${id}" class="btn tiny">Edit</a>
          <a href="delete_problem.php?id=${id}" class="btn tiny danger">Delete</a>`;
        } else {
          html += `<form method='POST' action='rate.php' style='display:inline;'>
            <input type='hidden' name='entity_type' value='problem'>
            <input type='hidden' name='entity_id' value='${id}'>
            <button class='btn tiny'>+1</button>
          </form>`;
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
