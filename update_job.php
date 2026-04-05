<?php
require_once 'config.php';

if (isset($_POST['update_status'])) {
    $id = $_POST['job_id'];
    $new_stage = $_POST['new_stage'];

    try {
        // I-update ang stage sa database
        $stmt = $conn->prepare("UPDATE work_orders SET current_stage = ? WHERE id = ?");
        $stmt->execute([$new_stage, $id]);

        // REDIRECT agad pabalik sa index para hindi mag-hang o mag-blink
        header("Location: index.php?status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?status=error");
        exit();
    }
}
?>