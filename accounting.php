<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config.php';
require_once 'access_control.php';
check_access(['admin','accounting']);

/* SUMMARY */
$stmt=$conn->query("
SELECT
COALESCE(SUM(CASE WHEN type='Income' THEN amount ELSE 0 END),0) AS total_income,
COALESCE(SUM(CASE WHEN type='Expense' THEN amount ELSE 0 END),0) AS total_expense
FROM accounting_transactions
");

$sum=$stmt->fetch();
$total_income=$sum['total_income'];
$total_expense=$sum['total_expense'];
$net=$total_income-$total_expense;

/* TRANSACTIONS */

$rows=$conn->query("
SELECT * FROM accounting_transactions
ORDER BY txn_date DESC,id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>

<title>Accounting</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:Segoe UI;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:75px;
}

/* SUMMARY */

.summary-card{
background:#fff;
padding:20px;
border-radius:10px;
box-shadow:0 3px 10px rgba(0,0,0,0.05);
text-align:center;
}

.summary-card h6{
color:#888;
}

.summary-card h3{
font-weight:600;
}

/* QUICK ACTION */

.quick-actions{
display:flex;
gap:10px;
margin-bottom:20px;
}

.quick-btn{
flex:1;
padding:15px;
border-radius:10px;
background:white;
border:1px solid #e3e6ea;
cursor:pointer;
text-align:center;
transition:.2s;
}

.quick-btn:hover{
background:#f1f5ff;
}

/* CARD */

.card{
border:none;
border-radius:10px;
box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<!-- TITLE BOX -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<h3 class="mb-0">Accounting Dashboard</h3>
</div>
</div>

<!-- SUMMARY -->

<div class="row mb-4">

<div class="col-md-4">
<div class="summary-card">
<h6>Total Income</h6>
<h3 class="text-success">₱ <?=number_format($total_income,2)?></h3>
</div>
</div>

<div class="col-md-4">
<div class="summary-card">
<h6>Total Expense</h6>
<h3 class="text-danger">₱ <?=number_format($total_expense,2)?></h3>
</div>
</div>

<div class="col-md-4">
<div class="summary-card">
<h6>Net Balance</h6>
<h3>₱ <?=number_format($net,2)?></h3>
</div>
</div>

</div>

<!-- QUICK ACTION -->

<div class="quick-actions">

<div class="quick-btn" data-bs-toggle="modal" data-bs-target="#incomeModal">
Add Income
</div>

<div class="quick-btn" data-bs-toggle="modal" data-bs-target="#expenseModal">
Add Expense
</div>

</div>

<!-- TRANSACTIONS -->

<div class="card">

<div class="card-header">
Recent Transactions
</div>

<div class="card-body table-responsive">

<table class="table">

<thead>
<tr>
<th>Date</th>
<th>Type</th>
<th>Category</th>
<th>Description</th>
<th>Amount</th>
</tr>
</thead>

<tbody>

<?php foreach($rows as $r): ?>

<tr>

<td><?=$r['txn_date']?></td>

<td>
<?php if($r['type']=="Income"): ?>
<span class="badge bg-success">Income</span>
<?php else: ?>
<span class="badge bg-danger">Expense</span>
<?php endif; ?>
</td>

<td><?=$r['category']?></td>

<td><?=$r['description']?></td>

<td>₱ <?=number_format($r['amount'],2)?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- INCOME MODAL -->

<div class="modal fade" id="incomeModal">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Add Income</h5>
</div>

<div class="modal-body">

<input type="hidden" name="type" value="Income">

<div class="mb-3">
<label>Date</label>
<input type="date" name="txn_date" class="form-control">
</div>

<div class="mb-3">
<label>Category</label>
<input type="text" name="category" class="form-control">
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" name="amount" class="form-control">
</div>

<div class="mb-3">
<label>Description</label>
<input type="text" name="description" class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary" name="save_txn">Save</button>
</div>

</form>

</div>
</div>
</div>

<!-- EXPENSE MODAL -->

<div class="modal fade" id="expenseModal">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<div class="modal-header">
<h5>Add Expense</h5>
</div>

<div class="modal-body">

<input type="hidden" name="type" value="Expense">

<div class="mb-3">
<label>Date</label>
<input type="date" name="txn_date" class="form-control">
</div>

<div class="mb-3">
<label>Category</label>
<input type="text" name="category" class="form-control">
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" name="amount" class="form-control">
</div>

<div class="mb-3">
<label>Description</label>
<input type="text" name="description" class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary" name="save_txn">Save</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>
</body>
</html>
```
