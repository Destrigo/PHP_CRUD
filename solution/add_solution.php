<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("INSERT INTO problems (title, description, user_id) VALUES (:title, :description, :user_id)");
$stmt->execute(['title'=>$title, 'description'=>$description, 'user_id'=>$user_id]);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $problem_id = $_POST['problem_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO solutions (problem_id, content) VALUES (:pid, :content)");
    $stmt->execute(['pid'=>$problem_id,'content'=>$content]);

    header("Location: view_problem.php?id=$problem_id");
    exit;
}
?>
