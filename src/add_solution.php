<?php
include 'db.php';
include 'auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $problem_id = $_POST['problem_id'] ?? null;
    $content = trim($_POST['content'] ?? '');

    if (!$problem_id || !$content) {
        die("Missing required fields.");
    }

    // Inserisci la soluzione nel database
    $stmt = $conn->prepare("
        INSERT INTO solutions (problem_id, user_id, content, created_at)
        VALUES (:problem_id, :user_id, :content, NOW())
    ");
    $stmt->execute([
        'problem_id' => $problem_id,
        'user_id' => $user_id,
        'content' => $content
    ]);

    // Reindirizza alla pagina del problema
    header("Location: view_problem.php?id=" . urlencode($problem_id));
    exit;
} else {
    die("Invalid request method.");
}
?>

