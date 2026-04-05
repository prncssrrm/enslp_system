<?php
require_once 'config.php';

if (isset($_POST['update_status'])) {
    $job_id = $_POST['job_id'];
    $new_stage = $_POST['new_stage'];

    try {
        // I-update ang stage sa database base sa pinili mo sa modal
        $stmt = $conn->prepare("UPDATE work_orders SET current_stage = ? WHERE id = ?");
        $stmt->execute([$new_stage, $job_id]);

        // PAGKATAPOS I-UPDATE, BABALIK SA INDEX PARA MAKITA ANG PAGBABAGO
        header("Location: index.php?update=success");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>