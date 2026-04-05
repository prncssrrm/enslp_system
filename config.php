<?php
$host = "localhost";
$user = "root";
$pass = "";   // leave blank if XAMPP default
$dbname = "enslp_db";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch(PDOException $e) {
    die("CRITICAL ERROR: Connection failed. " . $e->getMessage());
}
?>
