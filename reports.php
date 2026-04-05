<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once "config.php";
require_once 'access_control.php';
check_access(['admin','accounting','staff']);

/* FILTER */
$month = $_GET['month'] ?? 3;
$year  = $_GET['year'] ?? date('Y');

/* SUMMARY */

// WORK ORDERS
$stmt=$conn->prepare("
SELECT COUNT(*) 
FROM work_orders
WHERE MONTH(created_at)=? AND YEAR(created_at)=?
");
$stmt->execute([$month,$year]);
$total_orders=$stmt->fetchColumn();

// REVENUE
$stmt=$conn->prepare("
SELECT COALESCE(SUM(amount),0)
FROM accounting_transactions
WHERE type='Income'
AND MONTH(txn_date)=?
AND YEAR(txn_date)=?
");
$stmt->execute([$month,$year]);
$total_revenue=$stmt->fetchColumn();

// EXPENSE
$stmt=$conn->prepare("
SELECT COALESCE(SUM(amount),0)
FROM accounting_transactions
WHERE type='Expense'
AND MONTH(txn_date)=?
AND YEAR(txn_date)=?
");
$stmt->execute([$month,$year]);
$total_expense=$stmt->fetchColumn();

// DELIVERED
$stmt=$conn->prepare("
SELECT COUNT(*)
FROM deliveries
WHERE status='delivered'
AND MONTH(delivered_date)=?
AND YEAR(delivered_date)=?
");
$stmt->execute([$month,$year]);
$total_delivered=$stmt->fetchColumn();

/* PRODUCTION */
$production=$conn->prepare("
SELECT status, COUNT(*) total
FROM work_orders
WHERE MONTH(created_at)=? AND YEAR(created_at)=?
GROUP BY status
");
$production->execute([$month,$year]);
$prod_data=$production->fetchAll(PDO::FETCH_ASSOC);

/* INVENTORY */
$inventory=$conn->prepare("
SELECT ii.item_name, SUM(sm.quantity) total_used
FROM stock_movements sm
JOIN inventory_items ii ON ii.id=sm.item_id
WHERE sm.movement_type != 'Stock In'
AND MONTH(sm.created_at)=?
AND YEAR(sm.created_at)=?
GROUP BY ii.item_name
ORDER BY total_used DESC
LIMIT 5
");
$inventory->execute([$month,$year]);
$inv_data=$inventory->fetchAll(PDO::FETCH_ASSOC);

/* LOGISTICS */
$stmt=$conn->prepare("
SELECT COUNT(*) FROM packing_jobs
WHERE MONTH(date_packed)=? AND YEAR(date_packed)=?
");
$stmt->execute([$month,$year]);
$total_packed=$stmt->fetchColumn();

/* HR */
$attendance=$conn->prepare("
SELECT status, COUNT(*) total
FROM attendance
WHERE MONTH(att_date)=? AND YEAR(att_date)=?
GROUP BY status
");
$attendance->execute([$month,$year]);
$att_data=$attendance->fetchAll(PDO::FETCH_ASSOC);

$stmt=$conn->prepare("
SELECT COALESCE(SUM(net_pay),0)
FROM payroll
WHERE MONTH(period_end)=? AND YEAR(period_end)=?
");
$stmt->execute([$month,$year]);
$total_payroll=$stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:#f4f6f9;
font-family:Arial;
/* REMOVE overflow:hidden */
}

.main-content{
margin-left:260px;
padding:20px;
margin-top:70px;
/* REMOVE height + overflow */
}

.card{
border-radius:10px;
height:100%;
}

.card-body{
padding:15px;
}

h6{
font-weight:600;
margin-bottom:10px;
}

.summary-card{
text-align:center;
padding:10px;
}

.summary-card h3{
margin:5px 0;
font-size:22px;
}

.summary-card h3{
    margin:5px 0;
    font-size:22px;
    font-weight:bold;
}

/* COLORS */
.text-yellow{ color:#f1c40f; }
.text-green{ color:#27ae60; }
.text-red{ color:#e74c3c; }
.text-blue{ color:#3498db; }
</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<!-- TITLE -->
<div class="card mb-2">
<div class="card-body">
<h5>Monthly Reports</h5>
</div>
</div>

<!-- FILTER -->
<form method="GET" class="row mb-2">

<div class="col-md-3">
<select name="month" class="form-control">
<?php for($m=1;$m<=12;$m++){ ?>
<option value="<?=$m?>" <?=($month==$m)?'selected':''?>>
<?=date("F",mktime(0,0,0,$m,1))?>
</option>
<?php } ?>
</select>
</div>

<div class="col-md-3">
<input type="number" name="year" value="<?=$year?>" class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Filter</button>
</div>

</form>

<!-- SUMMARY -->
<div class="row g-2 mb-2">

<div class="col-md-3">
<div class="card summary-card">
<h6>Work Orders</h6>
<h3 class="text-yellow"><?=$total_orders?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card summary-card">
<h6>Revenue</h6>
<h3 class="text-green">₱<?=number_format($total_revenue,2)?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card summary-card">
<h6>Expenses</h6>
<h3 class="text-red">₱<?=number_format($total_expense,2)?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card summary-card">
<h6>Delivered</h6>
<h3 class="text-blue"><?=$total_delivered?></h3>
</div>
</div>

</div>

<!-- GRID REPORTS -->
<div class="row g-2">

<!-- Production -->
<div class="col-md-6">
<div class="card">
<div class="card-body">
<h6>Production</h6>
<?php foreach($prod_data as $p){ ?>
<p class="mb-1"><?=$p['status']?>: <b><?=$p['total']?></b></p>
<?php } ?>
</div>
</div>
</div>

<!-- Inventory -->
<div class="col-md-6">
<div class="card">
<div class="card-body">
<h6>Inventory Usage</h6>
<?php foreach($inv_data as $i){ ?>
<p class="mb-1"><?=$i['item_name']?> - <b><?=$i['total_used']?></b></p>
<?php } ?>
</div>
</div>
</div>

<!-- Logistics -->
<div class="col-md-6">
<div class="card">
<div class="card-body">
<h6>Logistics</h6>
<p class="mb-1">Packed: <b><?=$total_packed?></b></p>
<p class="mb-1">Delivered: <b><?=$total_delivered?></b></p>
</div>
</div>
</div>

<!-- HR -->
<div class="col-md-6">
<div class="card">
<div class="card-body">
<h6>HR</h6>

<?php foreach($att_data as $a){ ?>
<p class="mb-1"><?=$a['status']?>: <b><?=$a['total']?></b></p>
<?php } ?>

<hr>

<p>Total Payroll: <b>₱<?=number_format($total_payroll,2)?></b></p>

</div>
</div>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>