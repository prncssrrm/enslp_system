<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // I-update ang status sa 'Failed'
    // Hindi tayo magbabalik ng stock sa inventory dahil sira na ang board (Wastage)
    $stmt = $conn->prepare("UPDATE work_orders SET current_stage = 'Failed' WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit();