<?php
include 'config.php';

if (isset($_GET['id']) && isset($_GET['reason'])) {
    $id = $_GET['id'];
    $reason = $_GET['reason'];
    $status = $_GET['status'];
    $date_now = date('Y-m-d H:i:s');

    // I-update ang status, completion date, at ang rason ng failure
    $stmt = $conn->prepare("UPDATE production_jobs SET status = ?, date_completed = ?, fail_reason = ? WHERE id = ?");
    
    if ($stmt->execute([$status, $date_now, $reason, $id])) {
        header("Location: index.php?msg=updated");
    } else {
        echo "Error updating record.";
    }
} else {
    header("Location: index.php");
}
exit();