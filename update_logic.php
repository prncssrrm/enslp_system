<?php
// 1. Kumonekta sa database
include 'config.php';

// 2. Siguraduhin na galing sa POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $current = $_POST['current'];

    // Sequence ng stages base sa flowchart mo
    $stages = ['Allocation', 'Cutting', 'Etching', 'Lamination', 'QC', 'Completed'];
    
    // Hanapin kung pang-ilan ang current stage sa listahan
    $currentIndex = array_search($current, $stages);

    if (isset($_POST['next'])) {
        // MOVE TO NEXT STAGE
        // Check kung may kasunod pa na stage sa array
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $nextStage = $stages[$currentIndex + 1];
            
            $stmt = $conn->prepare("UPDATE work_orders SET current_stage = ? WHERE id = ?");
            $stmt->execute([$nextStage, $id]);
        }
    } 
    
    elseif (isset($_POST['fail'])) {
        // FLAG DEFECT (Feedback Loop)
        // Imbes na fix sa Cutting, ibalik natin sa previous step para accurate
        if ($currentIndex > 0) {
            $prevStage = $stages[$currentIndex - 1];
            
            $stmt = $conn->prepare("UPDATE work_orders SET current_stage = ? WHERE id = ?");
            $stmt->execute([$prevStage, $id]);
        }
    }

    // 3. Ibalik sa dashboard para makita ang real-time update
    header("Location: index.php?status=success");
    exit();
}
?>