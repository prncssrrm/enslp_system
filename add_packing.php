<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $work_order_id   = $_POST['work_order_id'];
    $product_code    = $_POST['product_code'];
    $quantity_packed = $_POST['quantity_packed'];
    $packer          = $_POST['packer'];

    $conn = new mysqli("localhost","root","","enslp_db");
    $sql = "INSERT INTO packing_jobs (work_order_id, product_code, quantity_packed, packer)
            VALUES ('$work_order_id','$product_code','$quantity_packed','$packer')";
    $conn->query($sql);
    header("Location: packing.php");
}
?>

<form method="POST" class="p-4">
    <div class="mb-3">
        <label>Work Order ID</label>
        <input type="text" name="work_order_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Product Code</label>
        <input type="text" name="product_code" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Quantity Packed</label>
        <input type="number" name="quantity_packed" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Packer</label>
        <input type="text" name="packer" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
