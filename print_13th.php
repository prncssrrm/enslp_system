<?php
include 'config.php';

$id=$_GET['id'];

$stmt=$conn->prepare("
SELECT t.*,e.full_name
FROM thirteenth_month t
JOIN employees e ON e.id=t.employee_id
WHERE t.id=?
");

$stmt->execute([$id]);

$r=$stmt->fetch();
?>

<h2>13th Month Payslip</h2>

<p>Employee: <?= $r['full_name'] ?></p>
<p>Year: <?= $r['year'] ?></p>

<hr>

<p>Total Basic Salary: ₱ <?= number_format($r['total_basic_salary'],2) ?></p>

<h3>13th Month Pay: ₱ <?= number_format($r['thirteenth_amount'],2) ?></h3>

<script>
window.print()
</script>