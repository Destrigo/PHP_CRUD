<?php
$servername = "localhost";
$username = "root";        // your MySQL username
$password = "";            // your MySQL password
$dbname = "StormBrainer";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>