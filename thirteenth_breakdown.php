<?php
include 'config.php';

$emp = $_GET['emp'];
$year = $_GET['year'];

$stmt = $conn->prepare("
SELECT period_start,period_end,basic_pay
FROM payroll
WHERE employee_id=? 
AND YEAR(period_start)=?
ORDER BY period_start ASC
");

$stmt->execute([$emp,$year]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
"rows"=>$rows
]);