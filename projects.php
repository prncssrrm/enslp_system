<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";
require_once 'access_control.php';
check_access(['admin','production','engineer']);

function h($s){
    return htmlspecialchars($s ?? '',ENT_QUOTES,'UTF-8');
}

$self = "projects.php";

$msg="";

/* FLASH */
if(isset($_GET['msg'])){
    if($_GET['msg']=="wo_created") $msg="Work Order Created!";
}

/* ADD WORK ORDER */
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['add_wo'])){

    $product_name = trim($_POST['product_name']);
    $client = trim($_POST['wo_client_name']);
    $qty = (int)$_POST['quantity'];
    $selling_price = (float)$_POST['selling_price'];
    $unit_cost = (float)$_POST['cost'];
    $status = $_POST['wo_status'];
    $date_started = $_POST['date_started'];
    $date_completed = $_POST['date_completed'];

    if($status!="Completed"){
        $date_completed = null;
    }

    /* GENERATE WO NUMBER */
    $year = date("Y");
    $last = $conn->query("SELECT id FROM work_orders ORDER BY id DESC LIMIT 1")->fetch();
    $next = (int)($last['id'] ?? 0) + 1;

    $wo_no = "WO-$year-".str_pad($next,4,"0",STR_PAD_LEFT);

    /* INSERT WORK ORDER */
    $stmt = $conn->prepare("
    INSERT INTO work_orders
    (wo_no,product_name,client,qty,status,date_started,date_completed,created_at,selling_price)
    VALUES(?,?,?,?,?,?,?,NOW(),?)
    ");

    $stmt->execute([
        $wo_no,
        $product_name,
        $client,
        $qty,
        $status,
        $date_started,
        $date_completed,
        $selling_price
    ]);

    $wo_id = $conn->lastInsertId();

    /* AUTO ACCOUNTING (if completed) */
    if($status=="Completed"){

        $amount = $qty * $selling_price;
        $desc = "Work Order $wo_no - $product_name";
    
        // ACCOUNTING
        $stmt2 = $conn->prepare("
        INSERT INTO accounting_transactions
        (txn_date,type,category,reference_no,wo_id,description,payment_method,amount)
        VALUES (NOW(),'Income','Work Order',?,?,?,?,?)
        ");
    
        $stmt2->execute([
            $wo_no,
            $wo_id,
            $desc,
            'Cash',
            $amount
        ]);
    
    
    
        // 🔥 CHECK IF ITEM EXIST
        $check = $conn->prepare("SELECT id FROM inventory_items WHERE item_name=? LIMIT 1");
        $check->execute([$product_name]);
        $exist = $check->fetch();
    
        if($exist){
    
            // UPDATE EXISTING ITEM
            $stmt3 = $conn->prepare("
            UPDATE inventory_items 
            SET 
                quantity = quantity + ?, 
                cost = ? 
            WHERE id = ?
            ");
    
            $stmt3->execute([
                $qty,
                $unit_cost,
                $exist['id']
            ]);
    
        } else {
    
            // INSERT NEW ITEM
            $stmt3 = $conn->prepare("
            INSERT INTO inventory_items
            (item_name,category,unit,quantity,cost,selling_price,reorder_level,status,created_at)
            VALUES (?,?,?,?,?,?,?,'active',NOW())
            ");
    
            $stmt3->execute([
                $product_name,
                'Finished Good',
                'pcs',
                $qty,
                $unit_cost,
                $selling_price,
                10
            ]);
        }
    }

    header("Location:$self?msg=wo_created");
    exit();
}

/* FETCH */
$work_orders = $conn->query("SELECT * FROM work_orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
<title>Work Orders</title>

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
    border:none;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}
</style>

</head>

<body>

<?php include "sidebar.php"; ?>
<?php include "header.php"; ?>

<div class="main-content">

<div class="card mb-3">
<div class="card-body">
<h3>Work Orders</h3>
</div>
</div>

<?php if($msg): ?>
<div class="alert alert-success"><?=h($msg)?></div>
<?php endif; ?>

<div class="card">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addWorkOrderModal">

+ Create Work Order

</button>

</div>

<div class="card-body table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>ID</th>
<th>WO No</th>
<th>Product</th>
<th>Client</th>
<th>Qty</th>
<th>Price</th>
<th>Status</th>
<th>Date Started</th>
<th>Date Completed</th>
</tr>
</thead>

<tbody>

<?php if($work_orders): ?>
<?php foreach($work_orders as $wo): ?>

<tr>
<td><?=h($wo['id'])?></td>
<td><strong><?=h($wo['wo_no'])?></strong></td>
<td><?=h($wo['product_name'])?></td>
<td><?=h($wo['client'])?></td>
<td><?=h($wo['qty'])?></td>
<td>₱<?=number_format($wo['selling_price'],2)?></td>

<td>
<?php
$badge="secondary";
if($wo['status']=="Pending") $badge="warning";
if($wo['status']=="In Progress") $badge="primary";
if($wo['status']=="QC") $badge="dark";
if($wo['status']=="Completed") $badge="success";
?>
<span class="badge bg-<?=$badge?>"><?=$wo['status']?></span>
</td>

<td><?=h($wo['date_started'])?></td>
<td><?=h($wo['date_completed'] ?? "-")?></td>

</tr>

<?php endforeach; ?>
<?php else: ?>

<tr>
<td colspan="9" class="text-center">No work orders</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- 🔥 MODAL (IMPORTANT) -->
<div class="modal fade" id="addWorkOrderModal">
<div class="modal-dialog">
<form method="POST" class="modal-content">

<div class="modal-header">
<h5>Create Work Order</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-2">
<label>Product</label>
<input type="text" name="product_name" class="form-control" required>
</div>

<div class="mb-2">
<label>Client</label>
<input type="text" name="wo_client_name" class="form-control" required>
</div>

<div class="mb-2">
<label>Quantity</label>
<input type="number" name="quantity" class="form-control" required>
</div>

<div class="mb-2">
<label>Selling Price</label>
<input type="number" step="0.01" name="selling_price" class="form-control" required>

<div class="mb-2">
<label>Cost per unit</label>
<input type="number" step="0.01" name="cost" class="form-control" required>
</div>

</div>

<div class="mb-2">
<label>Status</label>
<select name="wo_status" class="form-control">
<option>Pending</option>
<option>In Progress</option>
<option>QC</option>
<option>Completed</option>
</select>
</div>

<div class="mb-2">
<label>Date Started</label>
<input type="date" name="date_started" class="form-control" required>
</div>

<div class="mb-2">
<label>Date Completed</label>
<input type="date" name="date_completed" class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary" name="add_wo">Save Work Order</button>
</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include "footer.php"; ?>

</body>
</html>