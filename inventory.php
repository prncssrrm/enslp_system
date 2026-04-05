<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';
require_once 'access_control.php';
check_access(['admin','engineer']);

$flash="";

/* SEARCH + CATEGORY */
$q = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';

/* ADD ITEM */
if(isset($_POST['add_item'])){

    $item_name = $_POST['item_name'] ?? '';
    $category_post = $_POST['category'] ?? '';
    $unit = $_POST['unit'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $cost = (float)($_POST['cost'] ?? 0);
    $selling_price = (float)($_POST['selling_price'] ?? 0);
    $reorder_level = (int)($_POST['reorder_level'] ?? 10);

    try{
        $stmt=$conn->prepare("
        INSERT INTO inventory_items
        (item_name,category,unit,quantity,cost,selling_price,reorder_level,status,created_at)
        VALUES (?,?,?,?,?,?,?,'active',NOW())
        ");

        $stmt->execute([
            $item_name,
            $category_post,
            $unit,
            $quantity,
            $cost,
            $selling_price,
            $reorder_level
        ]);

        header("Location: inventory.php?added=1");
        exit;

    }catch(Exception $e){
        die("ADD ERROR: ".$e->getMessage());
    }
}

/* RESTOCK */
if(isset($_POST['restock_item'])){
    $id = $_POST['id'];
    $add_qty = (int)$_POST['add_quantity'];

    $stmt = $conn->prepare("
        UPDATE inventory_items 
        SET quantity = quantity + ? 
        WHERE id = ?
    ");
    $stmt->execute([$add_qty, $id]);

    header("Location: inventory.php?restocked=1");
    exit;
}

/* DELETE */
if(isset($_POST['delete_item'])){
    $id=$_POST['id'];

    $stmt=$conn->prepare("UPDATE inventory_items SET status='inactive' WHERE id=?");
    $stmt->execute([$id]);

    header("Location: inventory.php?deleted=1");
    exit;
}

/* FLASH */
if(isset($_GET['added'])) $flash="Item added successfully.";
if(isset($_GET['updated'])) $flash="Item updated successfully.";
if(isset($_GET['deleted'])) $flash="Item removed successfully.";
if(isset($_GET['restocked'])) $flash="Item restocked successfully.";

/* FETCH ITEMS */
if($q!=""){
    $stmt=$conn->prepare("SELECT * FROM inventory_items WHERE item_name LIKE ? AND status='active'");
    $stmt->execute(["%$q%"]);
}elseif($category!=""){
    $stmt=$conn->prepare("SELECT * FROM inventory_items WHERE category=? AND status='active'");
    $stmt->execute([$category]);
}else{
    $stmt=$conn->query("SELECT * FROM inventory_items WHERE status='active' ORDER BY item_name ASC");
}

$items=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Inventory</title>

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
border-radius:8px;
border:1px solid #e6e6e6;
}

.table thead{
background:#f8f9fa;
}

.btn-edit{
background:#0f766e;
color:white;
border:none;
}

.btn-delete{
background:#dc3545;
color:white;
border:none;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">
<div class="container-fluid">

<div class="card shadow-sm mb-3">
<div class="card-body">
<h3 class="mb-0">Inventory Management</h3>
</div>
</div>

<?php if($flash){ ?>
<div class="alert alert-success"><?=$flash?></div>
<?php } ?>

<div class="card shadow-sm mb-3">
<div class="card-body">

<form method="GET" class="d-flex align-items-center flex-wrap gap-2">

<div class="d-flex align-items-center gap-2 flex-wrap">

    <!-- ADD BUTTON -->
    <?php if(strtolower($_SESSION['role']) != 'engineer'): ?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    + Add Item
</button>
<?php endif; ?>

    <!-- SEARCH -->
    <input 
    type="text"
    name="q"
    value="<?=htmlspecialchars($q)?>"
    class="form-control"
    placeholder="Search item"
    style="width:220px"
    >

    <button class="btn btn-secondary">Search</button>
    <a href="inventory.php" class="btn btn-light">Clear</a>

</div>

</form>
<div class="mt-3">

<a href="inventory.php" class="btn btn-outline-secondary btn-sm">All</a>
<a href="inventory.php?category=Raw Material" class="btn btn-outline-primary btn-sm">Raw Material</a>
<a href="inventory.php?category=Component" class="btn btn-outline-primary btn-sm">Component</a>
<a href="inventory.php?category=Supply" class="btn btn-outline-primary btn-sm">Supply</a>
<a href="inventory.php?category=Finished Good" class="btn btn-outline-primary btn-sm">Finished Good</a>

</div>

</div>
</div>

<div class="card shadow-sm">
<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>
<tr>
<th>Item</th>
<th>Category</th>
<th>Unit</th>
<th>Quantity</th>
<th>Cost</th>
<th>Selling</th>
<th>Value</th>
<th>Reorder</th>
<th>Status</th>
<th width="220">Action</th>
</tr>
</thead>

<tbody>

<?php foreach($items as $it){

$cost = (float)($it['cost'] ?? 0);

if($cost == 0){
    $cost = 1; // fallback (optional lang)
}
$selling = (float)($it['selling_price'] ?? 0);
$qty = (int)($it['quantity'] ?? 0);

$value = $cost * $qty;
$low = ($qty <= (int)$it['reorder_level']);
?>

<tr>

<td><?=$it['item_name']?></td>
<td><?=$it['category']?></td>
<td><?=$it['unit']?></td>
<td><?=$it['quantity']?></td>

<td>₱<?=number_format((float)($it['cost'] ?? 0),2)?></td>
<td>₱<?=number_format((float)($it['selling_price'] ?? 0),2)?></td>
<td>₱<?=number_format((float)$value,2)?></td>

<td><?=$it['reorder_level']?></td>

<td>
<?php if($low){ ?>
<span class="badge bg-danger">Low</span>
<?php } else { ?>
<span class="badge bg-success">OK</span>
<?php } ?>
</td>

<td>

<?php if(strtolower($_SESSION['role']) != 'engineer'): ?>

<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#restockModal"
data-id="<?=$it['id']?>"
data-name="<?=$it['item_name']?>"
>
Restock
</button>

<button
class="btn btn-edit btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
data-id="<?=$it['id']?>"
data-name="<?=$it['item_name']?>"
data-category="<?=$it['category']?>"
data-unit="<?=$it['unit']?>"
data-qty="<?=$it['quantity']?>"
data-cost="<?=$it['cost']?>"
data-sell="<?=$it['selling_price']?>"
data-reorder="<?=$it['reorder_level']?>"
>
Edit
</button>

<form method="POST" style="display:inline;">
<input type="hidden" name="id" value="<?=$it['id']?>">
<button type="submit" name="delete_item" class="btn btn-delete btn-sm"
onclick="return confirm('Delete this item?')">
Delete
</button>
</form>

<?php else: ?>

<span class="badge bg-secondary">View Only</span>

<?php endif; ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
</div>
</div>

</div>
</div>

<!-- RESTOCK MODAL -->
<div class="modal fade" id="restockModal">
<div class="modal-dialog">
<form method="POST" class="modal-content">

<div class="modal-header">
<h5>Restock Item</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id" id="restock_id">

<div class="mb-3">
<label>Item</label>
<input type="text" id="restock_name" class="form-control" disabled>
</div>

<div class="mb-3">
<label>Add Quantity</label>
<input type="number" name="add_quantity" class="form-control" required>
</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-warning" name="restock_item">Restock</button>
</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const restockModal = document.getElementById('restockModal');

restockModal.addEventListener('show.bs.modal', function(event){

let btn = event.relatedTarget;

document.getElementById('restock_id').value = btn.dataset.id;
document.getElementById('restock_name').value = btn.dataset.name;

});

</script>

<!-- ADD ITEM MODAL -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<form method="POST" class="modal-content">

<div class="modal-header">
<h5>Add Item</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-2">
<label>Item Name</label>
<input type="text" name="item_name" class="form-control" required>
</div>

<div class="mb-2">
<label>Category</label>
<select name="category" class="form-control" required>
<option value="Raw Material">Raw Material</option>
<option value="Component">Component</option>
<option value="Supply">Supply</option>
<option value="Finished Good">Finished Good</option>
</select>
</div>

<div class="mb-2">
<label>Unit</label>
<input type="text" name="unit" class="form-control" required>
</div>

<div class="mb-2">
<label>Quantity</label>
<input type="number" name="quantity" class="form-control" required>
</div>

<div class="mb-2">
<label>Cost</label>
<input type="number" step="0.01" name="cost" class="form-control" required>
</div>

<div class="mb-2">
<label>Selling Price</label>
<input type="number" step="0.01" name="selling_price" class="form-control" required>
</div>

<div class="mb-2">
<label>Reorder Level</label>
<input type="number" name="reorder_level" class="form-control" value="10">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" name="add_item">Add Item</button>
</div>

</form>
</div>
</div>


</body>
</html>