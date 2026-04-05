<?php
include 'config.php';

// 1. Siguraduhin nating tama ang columns ng table
$conn->query("ALTER TABLE users MODIFY COLUMN password VARCHAR(255)");

// 2. I-hash natin ang 'admin123'
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);

// 3. I-update o I-insert ang admin account
$check = $conn->prepare("SELECT id FROM users WHERE username = 'admin'");
$check->execute();

if ($check->rowCount() > 0) {
    $sql = "UPDATE users SET password = ?, status = 'Active', role = 'Admin' WHERE username = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$hashed_password]);
    echo "Admin account updated successfully with hashed password!";
} else {
    $sql = "INSERT INTO users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['System Admin', 'admin', $hashed_password, 'Admin', 'Active']);
    echo "New Admin account created successfully!";
}

echo "<br><a href='login.php'>Balik sa Login Page</a>";
?>