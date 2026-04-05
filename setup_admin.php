<?php
include 'config.php';

try {
    // 1. I-hash ang password na 'admin123'
    $hashed_pass = password_hash('admin123', PASSWORD_DEFAULT);

    // 2. Linisin at i-insert ang admin account
    $conn->query("DELETE FROM users WHERE username = 'admin'"); // Siguradong fresh start
    
    $sql = "INSERT INTO users (full_name, username, password, role, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['System Administrator', 'admin', $hashed_pass, 'Admin', 'Active']);

    echo "<h3>Success! Admin account has been reset.</h3>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>admin123</b><br><br>";
    echo "<a href='login.php'>Proceed to Login Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>