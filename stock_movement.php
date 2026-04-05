<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once 'config.php';
require_once 'access_control.php';
check_access(['admin','production']);

$flash="";
$flash_type="success";

/* =============================
   HELPER FUNCTIONS
=============================*/

function getItemStock(PDO $conn,$item_id){

$stmt=$conn->prepare("
SELECT quantity
FROM inventory_items
WHERE id=?
");

$stmt->execute([$item_id]);

return (int)$stmt->fetchColumn();

}

function adjustStock(PDO $conn,$item_id,$type,$qty){

if($type=="IN"){

$stmt=$conn->prepare("
UPDATE inventory_items
SET quantity = quantity + ?
WHERE id=?
");

$stmt->execute([$qty,$item_id]);

}else{

$stmt=$conn->prepare("
UPDATE inventory_items
SET quantity = quantity - ?
WHERE id=?
");

$stmt->execute([$qty,$item_id]);

}

}


/* =============================
   SAVE TRANSACTION
=============================*/

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['save_txn'])){

$item_id=(int)$_POST['item_id'];
$type=$_POST['movement_type'];
$qty=(int)$_POST['quantity'];
$wo_id=$_POST['wo_id'] ?: null;
$notes=trim($_POST['notes']);
$user=$_SESSION['username'] ?? 'system';

try{

if($item_id<=0) throw new Exception("Please select item");
if($qty<=0) throw new Exception("Invalid quantity");

$current_stock=getItemStock($conn,$item_id);

if($type=="OUT" && $qty>$current_stock){
throw new Exception("Not enough stock");
}

$conn->beginTransaction();

/* INSERT STOCK MOVEMENT */

$stmt=$conn->prepare("
INSERT INTO stock_movements
(item_id,movement_type,quantity,reference,notes,created_at)
VALUES (?,?,?,?,?,NOW())
");

$reference = $wo_id ? "WO-$wo_id" : null;

$stmt->execute([
$item_id,
$type,
$qty,
$reference,
$notes
]);

/* UPDATE INVENTORY */

adjustStock($conn,$item_id,$type,$qty);

$conn->commit();

header("Location: stock_movements.php?saved=1");
exit;

}catch(Throwable $e){

if($conn->inTransaction()) $conn->rollBack();

$flash=$e->getMessage();
$flash_type="danger";

}

}


/* =============================
   FLASH MESSAGE
=============================*/

if(isset($_GET['saved'])){
$flash="Transaction saved successfully.";
$flash_type="success";
}


/* =============================
   DROPDOWN DATA
=============================*/

$items=$conn->query("
SELECT id,item_name,unit,quantity
FROM inventory_items
WHERE status='active'
ORDER BY item_name
")->fetchAll(PDO::FETCH_ASSOC);

$wo_list=$conn->query("
SELECT id,wo_no,product_name
FROM work_orders
ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);


/* =============================
   TABLE DATA
=============================*/

$rows=$conn->query("
SELECT sm.*,ii.item_name,ii.unit
FROM stock_movements sm
LEFT JOIN inventory_items ii ON ii.id=sm.item_id
ORDER BY sm.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Stock Movements</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f6f8fb;
font-family:Segoe UI;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:70px;
}

.card{
border:none;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.table thead{
background:#f1f3f6;
}

.badge-in{
background:#d4edda;
color:#155724;
padding:6px 10px;
border-radius:6px;
font-size:12px;
}

.badge-out{
background:#f8d7da;
color:#721c24;
padding:6px 10px;
border-radius:6px;
font-size:12px;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">
<div class="container-fluid">

<div class="card mb-3">
<div class="card-body">
<h3 class="mb-0">Stock Movements</h3>
</div>
</div>

<?php if($flash): ?>
<div class="alert alert-<?= $flash_type ?>">
<?= $flash ?>
</div>
<?php endif; ?>

<div class="card">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#newTxnModal">

+ New Transaction

</button>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead>
<tr>
<th>Date</th>
<th>Item</th>
<th>Type</th>
<th>Qty</th>
<th>Reference</th>
<th>Notes</th>
</tr>
</thead>

<tbody>

<?php foreach($rows as $r): ?>

<tr>

<td><?= date("M d Y h:i A",strtotime($r['created_at'])) ?></td>

<td>
<strong><?= htmlspecialchars($r['item_name'] ?? '') ?></strong><br>
<small>ID <?= $r['item_id'] ?> • <?= $r['unit'] ?? '' ?></small>
</td>

<td>
<?php 
$type = strtolower($r['movement_type'] ?? '');

if($type == "stock in" || $type == "in"){ ?>
    <span class="badge bg-success">Stock In</span>
<?php }else{ ?>
    <span class="badge bg-danger">Stock Out</span>
<?php } ?>
</td>

<td><strong><?= $r['quantity'] ?></strong></td>

<td><?= htmlspecialchars($r['reference'] ?? '-') ?></td>

<td><?= htmlspecialchars($r['notes'] ?? 'No notes') ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>
</div>

<!-- NEW TRANSACTION MODAL -->

<div class="modal fade" id="newTxnModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<div class="modal-header">

<h5 class="modal-title">New Transaction</h5>

<button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="row g-3">

<div class="col-md-6">

<label>Item</label>

<select name="item_id" class="form-select" required>

<option value="">Select Item</option>

<?php foreach($items as $it): ?>

<option value="<?= $it['id'] ?>">
<?= htmlspecialchars($it['item_name']) ?> (Stock: <?= $it['quantity'] ?> <?= $it['unit'] ?>)
</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-3">

<label>Type</label>

<select name="movement_type" class="form-select">
<option value="OUT">Stock Out</option>
<option value="IN">Stock In</option>
</select>

</div>

<div class="col-md-3">

<label>Quantity</label>

<input type="number" name="quantity" class="form-control" required>

</div>

<div class="col-md-6">

<label>Work Order</label>

<select name="wo_id" class="form-select">

<option value="">None</option>

<?php foreach($wo_list as $wo): ?>

<option value="<?= $wo['id'] ?>">
<?= htmlspecialchars($wo['wo_no']) ?> - <?= htmlspecialchars($wo['product_name']) ?>
</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-12">

<label>Notes</label>

<textarea name="notes" class="form-control"></textarea>

</div>

</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button class="btn btn-primary" name="save_txn">
Save
</button>

</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>