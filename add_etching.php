<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $work_order_id = $_POST['work_order_id'];
    $material_code = $_POST['material_code'];
    $design        = $_POST['design'];
    $operator      = $_POST['operator'];

    $conn = new mysqli("localhost","root","","enslp_db");
    $sql = "INSERT INTO etching_jobs (work_order_id, material_code, design, operator)
            VALUES ('$work_order_id','$material_code','$design','$operator')";
    $conn->query($sql);
    header("Location: etching.php");
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
        <label>Design</label>
        <input type="text" name="design" class="form-control">
    </div>
    <div class="mb-3">
        <label>Operator</label>
        <input type="text" name="operator" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
