<?php
include 'config.php';

if (isset($_GET['id']) && isset($_GET['current_stage'])) {
    $id = $_GET['id'];
    $current = $_GET['current_stage'];

    // Sinunod natin ang Flowchart mo dito:
    // Raw Material (Allocation) -> Cutting -> Etching -> Lamination -> QC -> Completed
    $stages = ['Allocation', 'Cutting', 'Etching', 'Lamination', 'QC', 'Completed'];
    
    $currentIndex = array_search($current, $stages);

    // Siguraduhin na nahanap ang stage at may kasunod pa
    if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
        $nextStage = $stages[$currentIndex + 1];

        try {
            $conn->beginTransaction();

            // 1. I-update ang stage ng trabaho
            $stmt = $conn->prepare("UPDATE work_orders SET current_stage = ? WHERE id = ?");
            $stmt->execute([$nextStage, $id]);

            // 2. Kung 'Completed' na (Packing/Delivery sa flowchart), dagdag sa Finished Goods
            if ($nextStage == 'Completed') {
                $stmt2 = $conn->prepare("UPDATE inventory SET quantity = quantity + 1 WHERE item_name = 'Finished Product'");
                $stmt2->execute();
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            die("Error: " . $e->getMessage());
        }
    }
}

// Balik sa Dashboard
header("Location: index.php");
exit();