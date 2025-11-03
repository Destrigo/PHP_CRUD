<?php 
include 'db.php'; 
include 'auth.php';
requireLogin();
renderHeader();

// Load theme from session
$theme = $_SESSION['theme'] ?? 'light';

// Fetch all problems
$stmt = $conn->query("SELECT * FROM problems ORDER BY created_at DESC");
$problems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>StormBrainer</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .planet-container {
      position: relative;
      display: grid;
      place-items: center;
      min-height: 90vh;
      overflow: hidden;
    }

    .problem-planet {
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

    .problem-planet:hover {
      transform: scale(1.2) translate(-50%, -50%);
      z-index: 5;
    }

    @keyframes orbit {
      to { transform: rotate(360deg); }
    }

    /* Tooltip for problem details */
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
      max-width: 400px;
    }

    body[data-theme="light"] .problem-tooltip {
      background: rgba(255,255,255,0.9);
      color: #111;
    }

    /* Hide default list */
    ul, li { list-style: none; padding: 0; margin: 0; }
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

  <main class="planet-container">
    <a href="create_problem.php" class="btn" style="position:absolute;top:10px;left:10px;">+ Add New Problem</a>

    <?php 
    // Planets classes
    $planets = ['mercury','venus','earth','mars','jupiter','saturn','uranus','neptune'];

    foreach ($problems as $index => $problem):
      $planet = $planets[array_rand($planets)];
      $rating = max(1, (int)$problem['rating']);
      $size = 40 + ($rating * 5); // 1->45px, 10->90px
      $orbit = 100 + ($index * 40); // spacing orbits
      $speed = 10 + $index * 5; // seconds per rotation
    ?>
      <div class="problem-planet"
        style="
          width: <?= $size ?>px;
          height: <?= $size ?>px;
          background: radial-gradient(circle, var(--bubble-gradient-start), var(--bubble-gradient-end));
          animation-duration: <?= $speed ?>s;
          transform: rotate(0deg) translateX(<?= $orbit ?>px);
        "
        data-title="<?= htmlspecialchars($problem['title']) ?>"
        data-rating="<?= htmlspecialchars($problem['rating']) ?>"
        data-id="<?= $problem['id'] ?>"
      >
        <?= htmlspecialchars(substr($problem['title'], 0, 10)) ?>...
      </div>
    <?php endforeach; ?>
  </main>

  <div id="tooltip" class="problem-tooltip"></div>

  <script>
    const tooltip = document.getElementById('tooltip');
    document.querySelectorAll('.problem-planet').forEach(p => {
      p.addEventListener('mouseenter', () => {
        tooltip.innerHTML = `<strong>${p.dataset.title}</strong><br>Rating: ${p.dataset.rating}<br><a href="view_problem.php?id=${p.dataset.id}" style="color:#58a6ff;">View Problem</a>`;
        tooltip.style.display = 'block';
      });
      p.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
      });
    });
  </script>
</body>
</html>
