<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once "config.php";
require_once 'access_control.php';
check_access(['admin','production','engineer']);

$flash="";

/* SAVE INSPECTION */

if(isset($_POST['add_inspection'])){

$work_order_id = $_POST['work_order_id'];
$item_id       = $_POST['item_id']; // finished product
$inspector     = $_POST['inspector'];
$status        = $_POST['status'];
$remarks       = $_POST['remarks'];
$passed_qty    = $_POST['passed_qty'];

/* SAVE QC RECORD */

$stmt=$conn->prepare("
INSERT INTO inspection_qc
(work_order_id,item_id,inspector,status,remarks,passed_qty,date_inspected)
VALUES (?,?,?,?,?,?,NOW())
");

$stmt->execute([
$work_order_id,
$item_id,
$inspector,
$status,
$remarks,
$passed_qty
]);

/* IF PASSED → STOCK IN FINISHED PRODUCT */

if($status == "Passed"){

    $stmt=$conn->prepare("
    INSERT INTO stock_movements
    (item_id,movement_type,quantity,reference,notes,movement_date)
    VALUES (?,?,?,?,?,NOW())
    ");

    $stmt->execute([
        $item_id,
        'Stock In',
        $passed_qty,
        $work_order_id,
        'QC STOCK IN'
    ]);



    // UPDATE INVENTORY
    $stmt=$conn->prepare("
    UPDATE inventory_items
    SET quantity = quantity + ?
    WHERE id = ?
    ");

    $stmt->execute([
    $passed_qty,
    $item_id
    ]);

    // UPDATE WORK ORDER STATUS
    $stmt=$conn->prepare("
    UPDATE work_orders
    SET status='Completed'
    WHERE id=?
    ");
    $stmt->execute([$work_order_id]);

}

header("Location: inspection_qc.php?success=1");
exit;

}


/* FLASH */

if(isset($_GET['success'])){
$flash="Inspection saved successfully.";
}


/* FETCH INSPECTIONS */

$stmt=$conn->query("
SELECT q.*,i.item_name,w.wo_no,w.product_name
FROM inspection_qc q
LEFT JOIN inventory_items i ON q.item_id = i.id
LEFT JOIN work_orders w ON q.work_order_id = w.id
ORDER BY q.id DESC
");

$inspections=$stmt->fetchAll(PDO::FETCH_ASSOC);


/* FINISHED PRODUCTS (IMPORTANT: dapat finished items lang) */

$stmt=$conn->query("
SELECT id,item_name
FROM inventory_items
WHERE status='active'
AND category = 'Finished Good'
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

<title>Inspection QC</title>

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
<h3>Inspection QC</h3>
</div>
</div>

<?php if($flash){ ?>
<div class="alert alert-success"><?=$flash?></div>
<?php } ?>

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addInspectionModal">

+ Add Inspection

</button>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>
<tr>
<th>Work Order</th>
<th>Product</th>
<th>Inspector</th>
<th>Status</th>
<th>Passed Qty</th>
<th>Remarks</th>
<th>Date</th>
</tr>
</thead>

<tbody>

<?php if($inspections){ ?>

<?php foreach($inspections as $row){ ?>

<tr>

<td><?=$row['wo_no']?></td>
<td><?=$row['item_name']?></td>
<td><?=$row['inspector']?></td>

<td>
<?php if($row['status']=="Passed"){ ?>
<span class="badge bg-success">Passed</span>
<?php }else{ ?>
<span class="badge bg-danger">Failed</span>
<?php } ?>
</td>

<td><?=$row['passed_qty']?></td>
<td><?=$row['remarks']?></td>
<td><?=$row['date_inspected']?></td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>
<td colspan="7" class="text-center">
No inspections found
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ADD MODAL -->

<div class="modal fade" id="addInspectionModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Inspection</h5>

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

<label>Finished Product</label>

<select name="item_id" class="form-control" required>

<option value="">Select Product</option>

<?php foreach($materials as $m){ ?>

<option value="<?=$m['id']?>">

<?=$m['item_name']?>

</option>

<?php } ?>

</select>

</div>


<div class="mb-3">

<label>Inspector</label>

<input type="text" name="inspector" class="form-control">

</div>


<div class="mb-3">

<label>Status</label>

<select name="status" class="form-select">

<option value="Passed">Passed</option>
<option value="Failed">Failed</option>

</select>

</div>


<div class="mb-3">

<label>Passed Quantity</label>
<input type="number" name="passed_qty" class="form-control" required>

</div>


<div class="mb-3">

<label>Remarks</label>

<textarea name="remarks" class="form-control"></textarea>

</div>

</div>


<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button type="submit" name="add_inspection" class="btn btn-success">
Save Inspection
</button>

</div>

</form>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>