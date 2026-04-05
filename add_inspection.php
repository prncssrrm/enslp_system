<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $work_order_id = $_POST['work_order_id'];
    $material_code = $_POST['material_code'];
    $inspector     = $_POST['inspector'];
    $status        = $_POST['status'];
    $remarks       = $_POST['remarks'];

    $conn = new mysqli("localhost","root","","enslp_db");
    $sql = "INSERT INTO inspection_qc (work_order_id, material_code, inspector, status, remarks)
            VALUES ('$work_order_id','$material_code','$inspector','$status','$remarks')";
    $conn->query($sql);
    header("Location: inspection_qc.php");
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
        <label>Inspector</label>
        <input type="text" name="inspector" class="form-control">
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select" required>
            <option value="Passed">Passed</option>
            <option value="Failed">Failed</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
