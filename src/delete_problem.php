<?php
include 'db.php';
include 'auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Controlla che sia stato passato un ID
if (!isset($_GET['id'])) {
    die("Problem ID not specified.");
}

$problem_id = (int) $_GET['id'];

// Recupera il problema
$stmt = $conn->prepare("SELECT * FROM problems WHERE id = :id");
$stmt->execute(['id' => $problem_id]);
$problem = $stmt->fetch(PDO::FETCH_ASSOC);

// Se non esiste
if (!$problem) {
    die("Problem not found.");
}

// Verifica che l’utente sia il proprietario
if ($problem['user_id'] != $user_id) {
    die("You are not authorized to edit this problem.");
}

// Elimina le soluzioni collegate (opzionale ma pulito)
$conn->prepare("DELETE FROM solutions WHERE problem_id = :problem_id")
     ->execute(['problem_id' => $problem_id]);

// Elimina il problema
$stmt = $conn->prepare("DELETE FROM problems WHERE id = :id");
$stmt->execute(['id' => $problem_id]);

// Reindirizza
header("Location: index.php");
exit;
?>
