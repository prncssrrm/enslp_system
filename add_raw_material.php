<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $supplier  = $_POST['supplier'];
    $quantity  = $_POST['quantity'];
    $unit      = $_POST['unit'];
    $location  = $_POST['location'];

    $conn = new mysqli("localhost","root","","enslp_db");

    $sql = "INSERT INTO inventory (item_code,item_name,supplier,quantity,unit,location,category)
            VALUES ('$item_code','$item_name','$supplier','$quantity','$unit','$location','raw_material')";
    $conn->query($sql);

    header("Location: raw_material.php");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Raw Material</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:Arial;
}

.main-content{
margin-left:260px;
padding:30px;
}

/* CARD FORM */
.form-card{
background:white;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
padding:25px;
max-width:700px;
}

.form-title{
font-weight:600;
margin-bottom:20px;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<div class="form-card">

<h4 class="form-title">Add Raw Material</h4>

<form method="POST">

<div class="mb-3">
<label class="form-label">Item Code</label>
<input type="text" name="item_code" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Item Name</label>
<input type="text" name="item_name" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Supplier</label>
<input type="text" name="supplier" class="form-control">
</div>

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Quantity</label>
<input type="number" name="quantity" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Unit</label>
<input type="text" name="unit" class="form-control">
</div>

</div>

<div class="mb-3">
<label class="form-label">Location</label>
<input type="text" name="location" class="form-control">
</div>

<div class="mt-3">

<button type="submit" class="btn btn-primary">
Save Raw Material
</button>

<a href="raw_material.php" class="btn btn-secondary">
Cancel
</a>

</div>

</form>

</div>

</div>

</body>
</html>
```
