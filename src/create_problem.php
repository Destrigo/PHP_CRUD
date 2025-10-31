<?php
include 'db.php';
include 'auth.php';
// Redirect to login if not logged in
requireLogin();
$user_id = $_SESSION['user_id']; // now $user_id is defined

$title = $_POST['title'] ?? null;
$description = $_POST['description'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($title && $description) {
        $stmt = $conn->prepare("INSERT INTO problems (user_id, title, description) VALUES (:user_id, :title, :description)");
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description
        ]);
        header("Location: my_problems.php");
        exit;
    } else {
        $error = "Title and description cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Problem - StormBrainer</title>
</head>
<body data-theme="<?= $theme ?>">
    <h2>Add New Problem</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <button type="submit">Add Problem</button>
    </form>
    <p><a href="index.php">Back to Problems</a></p>
</body>
</html>
