<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';
require_once 'access_control.php';
check_access(['admin']);

$year = (int)($_GET['year'] ?? date('Y'));
$message = "";

/* GENERATE 13TH MONTH */
if (isset($_POST['generate_13th'])) {

    $year = (int)$_POST['year'];
    $generated_by = $_SESSION['user_id'] ?? null;

    $emps = $conn->query("
        SELECT id, full_name
        FROM employees
        WHERE salary_type='Monthly'
        ORDER BY full_name ASC
    ")->fetchAll();

    foreach ($emps as $e) {

        $employee_id = $e['id'];

        $stmt = $conn->prepare("
            SELECT SUM(basic_pay) as total_basic
            FROM payroll
            WHERE employee_id = ?
            AND YEAR(period_start) = ?
        ");

        $stmt->execute([$employee_id,$year]);
        $row = $stmt->fetch();

        $total_basic = (float)($row['total_basic'] ?? 0);

        if ($total_basic <= 0) continue;

        $thirteenth = $total_basic / 12;

        $check = $conn->prepare("
            SELECT id
            FROM thirteenth_month
            WHERE employee_id=? AND year=?
        ");

        $check->execute([$employee_id,$year]);

        if ($check->rowCount() == 0) {

            $ins = $conn->prepare("
                INSERT INTO thirteenth_month
                (employee_id,year,total_basic_salary,thirteenth_amount,generated_by)
                VALUES (?,?,?,?,?)
            ");

            $ins->execute([
                $employee_id,
                $year,
                $total_basic,
                $thirteenth,
                $generated_by
            ]);
        }
    }

    $message = "13th Month Pay generated successfully.";
}

/* STATS */

$st = $conn->prepare("
SELECT COUNT(*) as cnt,
COALESCE(SUM(thirteenth_amount),0) as total
FROM thirteenth_month
WHERE year=?
");

$st->execute([$year]);
$srow = $st->fetch();

$stats_count = (int)$srow['cnt'];
$stats_total = (float)$srow['total'];

$emp_monthly_count = $conn->query("
SELECT COUNT(*) as c
FROM employees
WHERE salary_type='Monthly'
")->fetch()['c'];
?>
<!DOCTYPE html>
<html>
<head>
<title>13th Month Pay</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
body{background:#f8f9fa;font-family:Arial;}
.main-content{
margin-left:260px;
padding:25px;
margin-top:70px;
}
.header-card{background:#fff;border:1px solid #ddd;border-radius:6px;padding:15px;margin-bottom:20px;}
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;margin-bottom:20px;}
.stat{background:#fff;border:1px solid #ddd;border-radius:6px;padding:15px;text-align:center;}
.stat .icon{font-size:22px;color:#0d6efd;margin-bottom:5px;}
.stat .k{font-size:12px;color:#6c757d;text-transform:uppercase;font-weight:600;}
.stat .v{font-size:20px;font-weight:700;}
.cardx{background:#fff;border:1px solid #ddd;border-radius:6px;padding:20px;}
.btn-primaryx{background:#198754;border:none;border-radius:6px;padding:10px;font-weight:600;color:#fff;}
.table-wrap{background:#fff;border:1px solid #ddd;border-radius:6px;overflow:hidden;}
.year-pill{background:#eef2ff;padding:4px 10px;border-radius:20px;font-size:12px;}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="header-card">
<h4>13th Month Pay</h4>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="stats-grid">

<div class="stat">
<i class="bi bi-people icon"></i>
<div class="k">Employees (Monthly)</div>
<div class="v"><?= $emp_monthly_count ?></div>
</div>

<div class="stat">
<i class="bi bi-file-earmark-text icon"></i>
<div class="k">Generated</div>
<div class="v"><?= $stats_count ?></div>
</div>

<div class="stat">
<i class="bi bi-cash icon"></i>
<div class="k">Total Amount</div>
<div class="v">₱ <?= number_format($stats_total,2) ?></div>
</div>

</div>

<div class="row">

<div class="col-md-4">

<div class="cardx">

<form method="POST">

<label>Select Year</label>

<input type="number" name="year" class="form-control mb-3" value="<?= $year ?>">

<button type="submit" name="generate_13th" class="btn-primaryx w-100">
Generate 13th Month
</button>

</form>

</div>

</div>

<div class="col-md-8">

<div class="table-wrap">

<table class="table table-hover">

<thead>
<tr>
<th>Employee</th>
<th>Year</th>
<th class="text-end">Amount</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php

$list = $conn->prepare("
SELECT t.*, e.full_name
FROM thirteenth_month t
JOIN employees e ON e.id = t.employee_id
ORDER BY t.year DESC
");

$list->execute();

if($list->rowCount()>0){

while($r=$list->fetch()){
?>

<tr>

<td><?= $r['full_name'] ?></td>

<td>
<span class="year-pill"><?= $r['year'] ?></span>
</td>

<td class="text-end">
₱ <?= number_format($r['thirteenth_amount'],2) ?>
</td>

<td>

<button class="btn btn-sm btn-primary viewBreakdown"
data-id="<?= $r['employee_id'] ?>"
data-year="<?= $r['year'] ?>"
data-name="<?= $r['full_name'] ?>">
<i class="bi bi-search"></i>
</button>

<a href="print_13th.php?id=<?= $r['id'] ?>" class="btn btn-success btn-sm">
<i class="bi bi-printer"></i>
</a>

<a href="delete_13th.php?id=<?= $r['id'] ?>"
onclick="return confirm('Delete record?')"
class="btn btn-danger btn-sm">
<i class="bi bi-trash"></i>
</a>

</td>

</tr>

<?php } } else { ?>

<tr>
<td colspan="4" class="text-center">
No 13th month generated yet
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<!-- BREAKDOWN MODAL -->

<div class="modal fade" id="breakdownModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">13th Month Breakdown</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<h6 id="empName"></h6>

<table class="table table-bordered">

<thead>
<tr>
<th>Payroll Period</th>
<th class="text-end">Basic Pay</th>
</tr>
</thead>

<tbody id="breakdownBody"></tbody>

</table>

<div class="text-end">

<strong>Total Basic: ₱ <span id="totalBasic">0</span></strong><br>
<strong>13th Month: ₱ <span id="thirteenthAmount">0</span></strong>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll(".viewBreakdown").forEach(btn=>{

btn.addEventListener("click",function(){

let emp=this.dataset.id
let year=this.dataset.year
let name=this.dataset.name

document.getElementById("empName").innerText=name

fetch("thirteenth_breakdown.php?emp="+emp+"&year="+year)

.then(res=>res.json())

.then(data=>{

let body=document.getElementById("breakdownBody")
body.innerHTML=""

let total=0

data.rows.forEach(r=>{

total+=parseFloat(r.basic_pay)

body.innerHTML+=`
<tr>
<td>${r.period_start} - ${r.period_end}</td>
<td class="text-end">₱ ${parseFloat(r.basic_pay).toFixed(2)}</td>
</tr>
`

})

document.getElementById("totalBasic").innerText=total.toFixed(2)
document.getElementById("thirteenthAmount").innerText=(total/12).toFixed(2)

new bootstrap.Modal(document.getElementById("breakdownModal")).show()

})

})

})

</script>

<?php include 'footer.php'; ?>
</body>
</html>