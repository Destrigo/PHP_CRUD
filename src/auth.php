<?php
session_start();
// Call this function on pages that require authentication
// requireLogin();
// Determine current theme
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
    header("Location: " . $_SERVER['PHP_SELF']); // refresh page
    exit;
}
$theme = $_SESSION['theme'];

// Redirect to login if user is not logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}


// Helper function to display header/navigation
function renderHeader() {
    global $theme;
    ?>
    <header style="display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#e74c3c; color:white;">
        <div>
            <a href="index.php" style="color:white; text-decoration:none; font-weight:bold; font-size:1.2em;">StormBrainer</a>
        </div>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php" style="color:white; margin-right:15px;">All Problems</a>
                <a href="my_problems.php" style="color:white; margin-right:15px;">My Problems</a>
                <a href="create_problem.php" style="color:white; margin-right:15px;">+ Add Problem</a>
                <a href="search.php">Search</a>
                <a href="logout.php" style="color:white;">Logout</a>
            <?php else: ?>
                <a href="login.php" style="color:white; margin-right:15px;">Login</a>
                <a href="register.php" style="color:white;">Register</a>
            <?php endif; ?>
            <form method="POST" style="display:inline">
                <button type="submit" name="toggle_theme" class="btn tiny">
                    <?= $theme === 'light' ? '🌙 Dark Mode' : '☀️ Light Mode' ?>
                </button>
            </form>

        </nav>
    </header>
    <hr>
    <?php
}
?>
