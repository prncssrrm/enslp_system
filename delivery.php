<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
require_once "config.php";
require_once 'access_control.php';
check_access(['admin','accounting','staff']);

/* GENERATE DR NUMBER */
function generateDR($conn){
$stmt=$conn->query("SELECT COUNT(*) FROM deliveries");
$n=$stmt->fetchColumn()+1;
return "DR-".date("Y")."-".str_pad($n,4,"0",STR_PAD_LEFT);
}

/* CREATE DELIVERY */
if(isset($_POST['save_delivery'])){

$dr = generateDR($conn);

$wo_id = $_POST['wo_id'];
$delivered_to = $_POST['delivered_to'];
$address = $_POST['address'];
$delivered_date = $_POST['delivered_date'];
$status = "pending";
$remarks = $_POST['remarks'];

$stmt=$conn->prepare("
INSERT INTO deliveries
(dr_no,wo_id,delivered_to,address,delivered_date,status,remarks,created_at)
VALUES (?,?,?,?,?,?,?,NOW())
");

$stmt->execute([
$dr,$wo_id,$delivered_to,$address,$delivered_date,$status,$remarks
]);

header("Location: delivery.php?created=1");
exit;
}

if(isset($_POST['update_status'])){

    $id=$_POST['delivery_id'];
    $new_status=strtolower(trim($_POST['new_status']));
    
    /* GET CURRENT STATUS */
    $stmt=$conn->prepare("SELECT status FROM deliveries WHERE id=?");
    $stmt->execute([$id]);
    $current=strtolower($stmt->fetchColumn());
    
    /* STRICT FLOW */
    if($current == "pending" && $new_status != "out for delivery"){
        header("Location: delivery.php?error=invalid_flow");
        exit;
    }
    
    if($current == "out for delivery" && $new_status != "delivered"){
        header("Location: delivery.php?error=invalid_flow");
        exit;
    }
    
    /* UPDATE STATUS */
    $stmt=$conn->prepare("UPDATE deliveries SET status=? WHERE id=?");
    $stmt->execute([$new_status,$id]);
    
    if($new_status=="delivered"){

        /* GET WO ID */
        $stmt=$conn->prepare("SELECT wo_id FROM deliveries WHERE id=?");
        $stmt->execute([$id]);
        $row=$stmt->fetch();
        $wo_id=$row['wo_id'];
    
        /* GET WORK ORDER */
        $stmt=$conn->prepare("
        SELECT wo_no, product_name, qty, selling_price
        FROM work_orders
        WHERE id=?
        ");
        $stmt->execute([$wo_id]);
        $wo=$stmt->fetch();
    
        if(!$wo){
            die("Work order not found");
        }
    
        /* DEDUCT INVENTORY */
        $stmt_out = $conn->prepare("
        UPDATE inventory_items
        SET quantity = quantity - ?
        WHERE item_name = ?
        ");
    
        $stmt_out->execute([
            $wo['qty'],
            $wo['product_name']
        ]);
    
        /* COMPUTE */
        $total = $wo['qty'] * $wo['selling_price'];
        $desc = "Delivery ".$wo['wo_no']." - ".$wo['product_name'];
    
        /* CHECK DUPLICATE */
        $check=$conn->prepare("
        SELECT id FROM accounting_transactions
        WHERE wo_id=? AND category='Delivery'
        LIMIT 1
        ");
        $check->execute([$wo_id]);
        $exists=$check->fetch();
    
        /* INSERT ACCOUNTING */
        if(!$exists){
    
            $stmt2=$conn->prepare("
            INSERT INTO accounting_transactions
            (txn_date,type,category,reference_no,wo_id,description,payment_method,amount)
            VALUES (NOW(),'Income','Delivery',?,?,?,?,?)
            ");
    
            $stmt2->execute([
                $wo['wo_no'],
                $wo_id,
                $desc,
                'Delivery',
                $total
            ]);
        }
    }
    
    header("Location: delivery.php?updated=1");
    exit;
    }

/* FETCH WORK ORDERS */
$workOrders=$conn->query("
SELECT id,wo_no,product_name,client
FROM work_orders
WHERE status!='Completed'
ORDER BY id DESC
")->fetchAll();

/* FETCH DELIVERIES */
$deliveries=$conn->query("
SELECT d.*,w.wo_no,w.product_name,w.client
FROM deliveries d
LEFT JOIN work_orders w
ON d.wo_id=w.id
ORDER BY d.id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Delivery Management</title>

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

<?php include "sidebar.php"; ?>
<?php include "header.php"; ?>

<div class="main-content">

<div class="card shadow-sm mb-3">
<div class="card-body">
<h3>Delivery Management</h3>
</div>
</div>

<?php if(isset($_GET['created'])){ ?>
<div class="alert alert-success">Delivery created.</div>
<?php } ?>

<?php if(isset($_GET['updated'])){ ?>
<div class="alert alert-success">Status updated.</div>
<?php } ?>

<div class="card shadow-sm">

<div class="card-header">
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deliveryModal">
+ Create Delivery
</button>
</div>

<div class="card-body table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>DR No</th>
<th>Work Order</th>
<th>Customer</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php if(!$deliveries){ ?>
<tr>
<td colspan="6" class="text-center">No deliveries yet</td>
</tr>
<?php } ?>

<?php foreach($deliveries as $d){ ?>

<tr>

<td><?=$d['dr_no']?></td>
<td><?=$d['wo_no']?> - <?=$d['product_name']?></td>
<td><?=$d['client']?></td>
<td><?=$d['delivered_date']?></td>

<td>
<?php
$status = strtolower(trim($d['status']));

if($status=="delivered"){
echo '<span class="badge bg-success">Delivered</span>';
}
elseif($status=="out for delivery"){
echo '<span class="badge bg-warning text-dark">Out for Delivery</span>';
}
else{
echo '<span class="badge bg-secondary">Pending</span>';
}
?>
</td>

<td>

<?php if($status!="delivered"){ ?>

<form method="POST" style="display:flex;gap:5px">

<input type="hidden" name="delivery_id" value="<?=$d['id']?>">

<select name="new_status" class="form-control form-control-sm">

<option value="pending" <?=($status=="pending")?'selected':''?>>Pending</option>

<option value="out for delivery" <?=($status=="out for delivery")?'selected':''?>>
Out for Delivery
</option>

<option value="delivered" <?=($status=="pending")?'disabled':''?>>
Delivered
</option>

</select>

<button class="btn btn-sm btn-primary" name="update_status">
Update
</button>

</form>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- MODAL -->
<div class="modal fade" id="deliveryModal">
<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">
<h5>Create Delivery</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Work Order</label>
<select name="wo_id" id="wo_select" class="form-control" required>
<option value="">Select Work Order</option>

<?php foreach($workOrders as $wo){ ?>
<option value="<?=$wo['id']?>" data-client="<?=$wo['client']?>">
<?=$wo['wo_no']?> - <?=$wo['product_name']?> - <?=$wo['client']?>
</option>
<?php } ?>

</select>
</div>

<div class="mb-3">
<label>Delivered To</label>
<input type="text" id="delivered_to" name="delivered_to" class="form-control">
</div>

<div class="mb-3">
<label>Address</label>
<input type="text" name="address" class="form-control">
</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="delivered_date" value="<?=date('Y-m-d')?>" class="form-control">
</div>

<div class="mb-3">
<label>Status</label>
<input type="text" class="form-control" value="Pending" readonly>
</div>

<div class="mb-3">
<label>Remarks</label>
<textarea name="remarks" class="form-control"></textarea>
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary" name="save_delivery">Save Delivery</button>
</div>

</form>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("wo_select").addEventListener("change", function(){
let selected = this.options[this.selectedIndex];
let client = selected.getAttribute("data-client");
document.getElementById("delivered_to").value = client;
});
</script>

<?php include "footer.php"; ?>

</body>
</html>