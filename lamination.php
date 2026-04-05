<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
require_once "config.php";
require_once 'access_control.php';
check_access(['admin','production','engineer']);

$flash="";

/* SAVE LAMINATION */

if(isset($_POST['save_lamination'])){

$work_order_id = $_POST['work_order_id'];
$item_id       = $_POST['item_id'];
$adhesive      = $_POST['adhesive_type'];
$operator      = $_POST['operator'];
$date_lam      = $_POST['date_laminated'];

/* INSERT JOB */

$stmt=$conn->prepare("
INSERT INTO lamination_jobs
(work_order_id,item_id,adhesive_type,operator,date_laminated)
VALUES (?,?,?,?,?)
");

$stmt->execute([
$work_order_id,
$item_id,
$adhesive,
$operator,
$date_lam
]);

/* STOCK MOVEMENT (PROCESS TRACKING) */

$stmt=$conn->prepare("
INSERT INTO stock_movements
(item_id,movement_type,quantity,reference,movement_date)
VALUES (?,?,?,?,NOW())
");

$stmt->execute([
$item_id,
'lamination',
1,
$work_order_id
]);

header("Location: lamination.php?success=1");
exit();

}

/* FLASH */

if(isset($_GET['success'])){
$flash="Lamination job saved.";
}


/* FETCH JOBS */

$jobs=$conn->query("
SELECT l.*,i.item_name,w.wo_no,w.product_name
FROM lamination_jobs l
LEFT JOIN inventory_items i ON l.item_id=i.id
LEFT JOIN work_orders w ON l.work_order_id=w.id
ORDER BY l.id DESC
")->fetchAll(PDO::FETCH_ASSOC);


/* MATERIALS */

$materials=$conn->query("
SELECT id,item_name
FROM inventory_items
WHERE category='Raw Material'
AND status='active'
")->fetchAll(PDO::FETCH_ASSOC);


/* WORK ORDERS */

$workorders=$conn->query("
SELECT id,wo_no,product_name
FROM work_orders
ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>Lamination Jobs</title>

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

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="card shadow-sm mb-3">

<div class="card-body">
<h3>Lamination Jobs</h3>
</div>

</div>

<?php if($flash){ ?>
<div class="alert alert-success"><?=$flash?></div>
<?php } ?>

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addModal">

+ Add Lamination Job

</button>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>Work Order</th>
<th>Material</th>
<th>Adhesive</th>
<th>Operator</th>
<th>Date</th>
</tr>

</thead>

<tbody>

<?php foreach($jobs as $row): ?>

<tr>

<td><?=$row['wo_no']?> - <?=$row['product_name']?></td>
<td><?=$row['item_name']?></td>
<td><?=$row['adhesive_type']?></td>
<td><?=$row['operator']?></td>
<td><?=$row['date_laminated']?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ADD MODAL -->

<div class="modal fade" id="addModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Lamination Job</h5>

<button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>Work Order</label>

<select name="work_order_id" class="form-control" required>

<option value="">Select</option>

<?php foreach($workorders as $wo): ?>

<option value="<?=$wo['id']?>">

<?=$wo['wo_no']?> - <?=$wo['product_name']?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label>Material</label>

<select name="item_id" class="form-control" required>

<option value="">Select</option>

<?php foreach($materials as $m): ?>

<option value="<?=$m['id']?>">

<?=$m['item_name']?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label>Adhesive Type</label>

<input type="text" name="adhesive_type" class="form-control">

</div>

<div class="mb-3">

<label>Operator</label>

<input type="text" name="operator" class="form-control">

</div>

<div class="mb-3">

<label>Date Laminated</label>

<input type="date" name="date_laminated" class="form-control" required>

</div>

</div>

<div class="modal-footer">

<button class="btn btn-primary" name="save_lamination">
Save
</button>

</div>

</form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>