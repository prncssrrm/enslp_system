<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $stage = 'Pending'; // Default stage

    try {
        $sql = "INSERT INTO work_orders (product_name, current_stage, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$product_name, $stage]);

        // Pagkatapos mag-save, babalik sa Dashboard
        header("Location: index.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Pag may mali, balik sa Dashboard
    header("Location: index.php?error=missing_data");
    exit();
}
?>