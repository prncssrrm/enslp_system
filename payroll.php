<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';
require_once 'access_control.php';
check_access(['admin']);

$q = trim($_GET['q'] ?? '');

function num($v){ return is_numeric($v) ? (float)$v : 0.0; }
function money2($v){ return number_format((float)$v, 2); }

function monthlyBaseForContrib(string $salary_type, float $salary_amount): float {
  if ($salary_type === 'Monthly') return $salary_amount;
  return $salary_amount * 26;
}

/* ================= SAVE ================= */
if (isset($_POST['add_payroll'])) {

    $employee_id   = (int)($_POST['employee_id'] ?? 0);
    $period_start  = $_POST['period_start'] ?? null;
    $period_end    = $_POST['period_end'] ?? null;
    $days_worked   = (float)($_POST['days_worked'] ?? 0);
    $ot_hours      = (float)($_POST['overtime_hours'] ?? 0);
    $ot_rate       = (float)($_POST['overtime_rate'] ?? 0);
    $allowances       = (float)($_POST['allowances'] ?? 0);
    $other_deductions = (float)($_POST['other_deductions'] ?? 0);

    $stmt = $conn->prepare("
      SELECT salary_type, salary_amount, daily_rate, monthly_salary
      FROM employees WHERE id = ? LIMIT 1
    ");
    $stmt->execute([$employee_id]);
    $emp = $stmt->fetch();

    $salary_type   = $emp['salary_type'] ?? '';
    $salary_amount = num($emp['salary_amount'] ?? 0);

    if ($salary_type === '' || $salary_amount <= 0) {
      $oldMonthly = num($emp['monthly_salary'] ?? 0);
      $oldDaily   = num($emp['daily_rate'] ?? 0);
      if ($oldMonthly > 0) { $salary_type = 'Monthly'; $salary_amount = $oldMonthly; }
      else { $salary_type = 'Daily'; $salary_amount = $oldDaily; }
    }

    $overtime_pay = $ot_hours * $ot_rate;

    if ($salary_type === 'Daily') {
      $basic_pay = $salary_amount * $days_worked;
    } else {
      $basic_pay = $salary_amount / 2;
    }

    $monthly_base = monthlyBaseForContrib($salary_type, $salary_amount);

   /* CHECK IF 1-15 CUT OFF */
$day = date('d', strtotime($period_start));

if ($day <= 15) {
    // MAY DEDUCTIONS
    $philhealth = round($monthly_base * 0.025, 2);
    $pagibig    = round(min($monthly_base * 0.02, 100), 2);
    $sss        = round($monthly_base * 0.045, 2);
} else {
    // WALANG DEDUCTIONS
    $philhealth = 0;
    $pagibig    = 0;
    $sss        = 0;
}

    $gross = $basic_pay + $overtime_pay + $allowances;
    $total_deductions = $sss + $philhealth + $pagibig + $other_deductions;
    $net = $gross - $total_deductions;

    $ins = $conn->prepare("
        INSERT INTO payroll 
        (employee_id, period_start, period_end, days_worked, overtime_hours, overtime_rate,
         allowances, sss, philhealth, pagibig, other_deductions,
         basic_pay, overtime_pay, gross_pay, net_pay, pay_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'REGULAR')
    ");
    $ins->execute([
        $employee_id, $period_start, $period_end,
        $days_worked, $ot_hours, $ot_rate,
        $allowances, $sss, $philhealth, $pagibig, $other_deductions,
        $basic_pay, $overtime_pay, $gross, $net


    ]);

    /* AUTO RECORD TO ACCOUNTING (EXPENSE) */
$acc = $conn->prepare("
INSERT INTO accounting_transactions
(type, txn_date, category, description, amount)
VALUES ('Expense', ?, 'Payroll', ?, ?)
");

$desc = "Salary - Employee ID: " . $employee_id . 
        " (" . $period_start . " to " . $period_end . ")";

$acc->execute([
    $period_end,
    $desc,
    $net
]);

    header("Location: payroll.php");
    exit();
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete'] ?? 0);
    $del = $conn->prepare("DELETE FROM payroll WHERE id = ?");
    $del->execute([$id]);
    header("Location: payroll.php");
    exit();
}

/* ================= EMPLOYEES ================= */
$emps = $conn->query("
  SELECT id, full_name, department
  FROM employees ORDER BY full_name ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payroll</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>

body{
background:#f4f6f9;
font-family:Arial;
margin:0;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:60px;
}

/* CARD */
.card{
border-radius:8px;
}

/* TABLE */
.table thead{
background:#f0f3f7;
}

/* BUTTONS */
.btn-blue{
background:#0d6efd;
color:#fff;
border:none;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="container-fluid">

<div class="card shadow-sm mb-3">
<div class="card-body d-flex justify-content-between align-items-center">

<h3 class="mb-0">Payroll</h3>

</div>
</div>

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#addPayrollModal">
+ New Payroll
</button>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>Employee</th>
<th>Department</th>
<th>Cutoff</th>
<th>Gross</th>
<th>Net</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$stmt = $conn->query("
SELECT pr.*, e.full_name, e.department
FROM payroll pr
JOIN employees e ON e.id = pr.employee_id
ORDER BY pr.id DESC
");

while($row=$stmt->fetch()):
?>

<tr>

<td><?= htmlspecialchars($row['full_name']) ?></td>

<td><?= htmlspecialchars($row['department']) ?></td>

<td><?= $row['period_start']?> - <?= $row['period_end']?></td>

<td>₱ <?= money2($row['gross_pay'])?></td>

<td><b>₱ <?= money2($row['net_pay'])?></b></td>

<td>

<a href="payslip.php?id=<?= $row['id']?>" class="btn btn-success btn-sm">
Print
</a>

<a href="?delete=<?= $row['id']?>" onclick="return confirm('Delete payroll?')" class="btn btn-danger btn-sm">
Delete
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>
</div>

</div>
</div>

<!-- ADD PAYROLL MODAL -->

<div class="modal fade" id="addPayrollModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Payroll</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">
<label>Employee</label>
<select name="employee_id" class="form-control" required>
<option value="">Select Employee</option>
<?php foreach($emps as $e): ?>
<option value="<?= $e['id'] ?>">
<?= $e['full_name']?> (<?= $e['department']?>)
</option>
<?php endforeach; ?>
</select>
</div>

<div class="mb-3">
<label>Period Start</label>
<input type="date" name="period_start" class="form-control" required>
</div>

<div class="mb-3">
<label>Period End</label>
<input type="date" name="period_end" class="form-control" required>
</div>

<div class="mb-3">
<label>Days Worked</label>
<input type="number" name="days_worked" class="form-control" required>
</div>

<div class="mb-3">
<label>Overtime Hours</label>
<input type="number" name="overtime_hours" value="0" class="form-control">
</div>

<div class="mb-3">
<label>Overtime Rate</label>
<input type="number" name="overtime_rate" value="0" class="form-control">
</div>

<div class="mb-3">
<label>Allowances</label>
<input type="number" name="allowances" value="0" class="form-control">
</div>

<div class="mb-3">
<label>Other Deductions</label>
<input type="number" name="other_deductions" value="0" class="form-control">
</div>

</div>

<div class="modal-footer">

<button type="submit" name="add_payroll" class="btn btn-primary">
Save Payroll
</button>

</div>

</form>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<?php include 'footer.php'; ?>
</body>
</html>
```
