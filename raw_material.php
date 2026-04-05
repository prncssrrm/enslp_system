<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost","root","","enslp_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// SAVE RAW MATERIAL
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$item_code = $_POST['item_code'];
$item_name = $_POST['item_name'];
$supplier  = $_POST['supplier'];
$quantity  = $_POST['quantity'];
$unit      = $_POST['unit'];
$location  = $_POST['location'];

$sql = "INSERT INTO inventory 
(item_code,item_name,supplier,quantity,unit,location,category)
VALUES 
('$item_code','$item_name','$supplier','$quantity','$unit','$location','raw_material')";

$conn->query($sql);

header("Location: raw_material.php");
exit();

}


// FETCH DATA
$result = $conn->query("SELECT * FROM inventory WHERE category='raw_material' ORDER BY date_added DESC");

?>

<!DOCTYPE html>
<html>

<head>

<title>Raw Materials</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:Arial;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:60px;
}

.card{
border-radius:8px;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="container-fluid">

<!-- TITLE BOX -->
<div class="card shadow-sm mb-3">
<div class="card-body">
<h3 class="mb-0">Raw Materials</h3>
</div>
</div>


<!-- MAIN CARD -->

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addMaterialModal">

+ Add Raw Material

</button>

</div>


<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>

<tr>

<th>Item Code</th>
<th>Item Name</th>
<th>Supplier</th>
<th>Quantity</th>
<th>Unit</th>
<th>Location</th>
<th>Date Added</th>

</tr>

</thead>

<tbody>

<?php if ($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($row['item_code']) ?></td>

<td><strong><?= htmlspecialchars($row['item_name']) ?></strong></td>

<td><?= htmlspecialchars($row['supplier']) ?></td>

<td><?= htmlspecialchars($row['quantity']) ?></td>

<td><?= htmlspecialchars($row['unit']) ?></td>

<td><?= htmlspecialchars($row['location']) ?></td>

<td><?= htmlspecialchars($row['date_added']) ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="7" class="text-center">
No raw materials found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>



<!-- ADD RAW MATERIAL MODAL -->

<div class="modal fade" id="addMaterialModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Raw Material</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>



<div class="modal-body">

<div class="mb-3">

<label>Item Code</label>

<input type="text" name="item_code" class="form-control" required>

</div>



<div class="mb-3">

<label>Item Name</label>

<input type="text" name="item_name" class="form-control" required>

</div>



<div class="mb-3">

<label>Supplier</label>

<input type="text" name="supplier" class="form-control">

</div>



<div class="row">

<div class="col-md-6 mb-3">

<label>Quantity</label>

<input type="number" name="quantity" class="form-control" required>

</div>



<div class="col-md-6 mb-3">

<label>Unit</label>

<input type="text" name="unit" class="form-control">

</div>

</div>



<div class="mb-3">

<label>Location</label>

<input type="text" name="location" class="form-control">

</div>

</div>



<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

Cancel

</button>



<button type="submit" class="btn btn-primary">

Save Raw Material

</button>

</div>

</form>

</div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>

</html>