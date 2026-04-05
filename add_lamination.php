<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $work_order_id = $_POST['work_order_id'];
    $material_code = $_POST['material_code'];
    $adhesive_type = $_POST['adhesive_type'];
    $operator      = $_POST['operator'];

    $conn = new mysqli("localhost","root","","enslp_db");
    $sql = "INSERT INTO lamination_jobs (work_order_id, material_code, adhesive_type, operator)
            VALUES ('$work_order_id','$material_code','$adhesive_type','$operator')";
    $conn->query($sql);
    header("Location: lamination.php");
}
?>

<form method="POST" class="p-4">
    <div class="mb-3">
        <label>Work Order ID</label>
        <input type="text" name="work_order_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Material Code</label>
        <input type="text" name="material_code" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Adhesive Type</label>
        <input type="text" name="adhesive_type" class="form-control">
    </div>
    <div class="mb-3">
        <label>Operator</label>
        <input type="text" name="operator" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
