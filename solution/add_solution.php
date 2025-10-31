<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $problem_id = $_POST['problem_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO solutions (problem_id, content) VALUES (:pid, :content)");
    $stmt->execute(['pid'=>$problem_id,'content'=>$content]);

    header("Location: view_problem.php?id=$problem_id");
    exit;
}
?>
