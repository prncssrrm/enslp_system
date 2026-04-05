<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once "config.php";
require_once 'access_control.php';
check_access(['admin','production','engineer']);

$flash="";

/* SAVE ETCHING JOB */

if(isset($_POST['add_etching'])){

$work_order_id = $_POST['work_order_id'];
$item_id       = $_POST['item_id'];
$design        = $_POST['design'];
$operator      = $_POST['operator'];
$date_etched   = $_POST['date_etched'];

/* INSERT */

$stmt=$conn->prepare("
INSERT INTO etching_jobs
(work_order_id,item_id,design,operator,date_etched)
VALUES (?,?,?,?,?)
");

$stmt->execute([
$work_order_id,
$item_id,
$design,
$operator,
$date_etched
]);

/* STOCK MOVEMENT (PROCESS TRACKING) */

$stmt=$conn->prepare("
INSERT INTO stock_movements
(item_id,movement_type,quantity,reference,movement_date)
VALUES (?,?,?,?,NOW())
");

$stmt->execute([
$item_id,
'etching',
1,
$work_order_id
]);

header("Location: etching.php?success=1");
exit;

}

/* FLASH */

if(isset($_GET['success'])){
$flash="Etching job saved successfully.";
}

/* FETCH ETCHING JOBS */

$stmt=$conn->query("
SELECT e.*,i.item_name,w.wo_no,w.product_name
FROM etching_jobs e
LEFT JOIN inventory_items i ON e.item_id=i.id
LEFT JOIN work_orders w ON e.work_order_id=w.id
ORDER BY e.id DESC
");

$jobs=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* MATERIALS */

$stmt=$conn->query("
SELECT id,item_name
FROM inventory_items
WHERE category='Raw Material'
AND status='active'
ORDER BY item_name
");

$materials=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* WORK ORDERS */

$stmt=$conn->query("
SELECT id,wo_no,product_name
FROM work_orders
ORDER BY id DESC
");

$workorders=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>Etching Jobs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:Arial;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:70px;
}

.card{
border-radius:10px;
}

</style>

</head>

<body>

<div class="d-flex">

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content flex-grow-1">

<div class="card shadow-sm mb-3">
<div class="card-body">
<h3 class="mb-0">Etching Jobs</h3>
</div>
</div>

<?php if($flash){ ?>
<div class="alert alert-success"><?=$flash?></div>
<?php } ?>

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addEtchingModal">

+ Add Etching Job

</button>

</div>

<div class="card-body p-0">

<table class="table table-bordered mb-0">

<thead class="table-light">

<tr>
<th>Work Order</th>
<th>Material</th>
<th>Design</th>
<th>Operator</th>
<th>Date Etched</th>
</tr>

</thead>

<tbody>

<?php if($jobs){ ?>

<?php foreach($jobs as $row){ ?>

<tr>

<td><?=$row['wo_no']?> - <?=$row['product_name']?></td>
<td><?=$row['item_name']?></td>
<td><?=$row['design']?></td>
<td><?=$row['operator']?></td>
<td><?=$row['date_etched']?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>
<td colspan="5" class="text-center p-3">
No etching jobs found
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>


<!-- ADD MODAL -->

<div class="modal fade" id="addEtchingModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">
<h5>Add Etching Job</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">


<div class="mb-3">
<label>Work Order</label>

<select name="work_order_id" class="form-control" required>

<option value="">Select Work Order</option>

<?php foreach($workorders as $wo){ ?>

<option value="<?=$wo['id']?>">

<?=$wo['wo_no']?> - <?=$wo['product_name']?>

</option>

<?php } ?>

</select>

</div>


<div class="mb-3">
<label>Material</label>

<select name="item_id" class="form-control" required>

<option value="">Select Material</option>

<?php foreach($materials as $m){ ?>

<option value="<?=$m['id']?>">

<?=$m['item_name']?>

</option>

<?php } ?>

</select>

</div>


<div class="mb-3">
<label>Design</label>
<input type="text" name="design" class="form-control">
</div>


<div class="mb-3">
<label>Operator</label>
<input type="text" name="operator" class="form-control">
</div>


<div class="mb-3">
<label>Date Etched</label>
<input type="date" name="date_etched" class="form-control" required>
</div>


</div>


<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button type="submit" name="add_etching" class="btn btn-primary">
Save
</button>

</div>

</form>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>