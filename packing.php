<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once "config.php";
require_once 'access_control.php';
check_access(['admin','accounting','staff']);

$flash="";


/* SAVE PACKING */

if(isset($_POST['add_packing'])){

$work_order_id   = $_POST['work_order_id'];
$item_id         = $_POST['item_id'];
$quantity_packed = $_POST['quantity_packed'];
$packer          = $_POST['packer'];

/* INSERT PACKING */

$stmt=$conn->prepare("
INSERT INTO packing_jobs
(work_order_id,item_id,quantity_packed,packer)
VALUES (?,?,?,?)
");

$stmt->execute([
$work_order_id,
$item_id,
$quantity_packed,
$packer
]);

/* UPDATE WORK ORDER STATUS TO PACKED */

$stmt=$conn->prepare("
UPDATE work_orders
SET status='Packed'
WHERE id=?
");

$stmt->execute([$work_order_id]);

/* ADD FINISHED GOODS TO INVENTORY */

$stmt=$conn->prepare("
UPDATE inventory_items
SET quantity = quantity + ?
WHERE id=?
");

$stmt->execute([
$quantity_packed,
$item_id
]);

/* STOCK MOVEMENT */

$stmt=$conn->prepare("
INSERT INTO stock_movements
(item_id,movement_type,quantity,reference,movement_date)
VALUES (?,?,?,?,NOW())
");

$stmt->execute([
$item_id,
'packing',
$quantity_packed,
$work_order_id
]);

header("Location: packing.php?success=1");
exit;

}


/* FLASH */

if(isset($_GET['success'])){
$flash="Packing job saved successfully.";
}


/* FETCH PACKING JOBS */

$stmt=$conn->query("
SELECT p.*,i.item_name,w.wo_no,w.product_name
FROM packing_jobs p
LEFT JOIN inventory_items i ON p.item_id=i.id
LEFT JOIN work_orders w ON p.work_order_id=w.id
ORDER BY p.id DESC
");

$jobs=$stmt->fetchAll(PDO::FETCH_ASSOC);


/* FINISHED GOODS */

$stmt=$conn->query("
SELECT id,item_name
FROM inventory_items
WHERE category='Finished Good'
AND status='active'
");

$products=$stmt->fetchAll(PDO::FETCH_ASSOC);


/* WORK ORDERS */

$stmt=$conn->query("
SELECT id,wo_no,product_name
FROM work_orders
WHERE status = 'Ready for Packing'
ORDER BY id DESC
");

$workorders=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>

<head>

<title>Packing Jobs</title>

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
<h3>Packing Jobs</h3>
</div>
</div>

<?php if($flash){ ?>
<div class="alert alert-success"><?=$flash?></div>
<?php } ?>

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addPackingModal">

+ Add Packing Job

</button>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>
<tr>
<th>Work Order</th>
<th>Product</th>
<th>Quantity</th>
<th>Packer</th>
<th>Date</th>
</tr>
</thead>

<tbody>

<?php if($jobs){ ?>

<?php foreach($jobs as $row){ ?>

<tr>

<td><?=$row['wo_no']?> - <?=$row['product_name']?></td>
<td><?=$row['item_name']?></td>
<td><?=$row['quantity_packed']?></td>
<td><?=$row['packer']?></td>
<td><?=$row['date_packed']?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>
<td colspan="5" class="text-center">
No packing jobs found
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ADD MODAL -->

<div class="modal fade" id="addPackingModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Packing Job</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">


<div class="mb-3">

<label>Work Order</label>

<select name="work_order_id" class="form-control" required>

<option value="">Select</option>

<?php foreach($workorders as $wo){ ?>

<option value="<?=$wo['id']?>">

<?=$wo['wo_no']?> - <?=$wo['product_name']?>

</option>

<?php } ?>

</select>

</div>


<div class="mb-3">

<label>Product</label>

<select name="item_id" class="form-control" required>

<option value="">Select Product</option>

<?php foreach($products as $p){ ?>

<option value="<?=$p['id']?>">

<?=$p['item_name']?>

</option>

<?php } ?>

</select>

</div>


<div class="mb-3">

<label>Quantity Packed</label>

<input type="number" name="quantity_packed" class="form-control" required>

</div>


<div class="mb-3">

<label>Packer</label>

<input type="text" name="packer" class="form-control">

</div>

</div>


<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button type="submit" name="add_packing" class="btn btn-success">
Save Packing Job
</button>

</div>

</form>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>