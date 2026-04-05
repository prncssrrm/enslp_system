<?php
session_start();
include 'config.php';
require_once 'access_control.php';
check_access(['admin']);

if(isset($_POST['save_attendance'])){

$emp=$_POST['employee_id'];
$date=$_POST['att_date'];
$in=$_POST['time_in'];
$out=$_POST['time_out'];
$status=$_POST['status'];
$remarks=$_POST['remarks'];

$stmt=$conn->prepare("INSERT INTO attendance
(employee_id,att_date,time_in,time_out,status,remarks)
VALUES (?,?,?,?,?,?)");

$stmt->execute([$emp,$date,$in,$out,$status,$remarks]);

header("Location: attendance.php");
exit();
}

if(isset($_GET['delete'])){
$id=$_GET['delete'];

$stmt=$conn->prepare("DELETE FROM attendance WHERE id=?");
$stmt->execute([$id]);

header("Location: attendance.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Attendance</title>

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

/* CARD STYLE SAME AS EMPLOYEES */
.card{
border-radius:8px;
}

/* TABLE HEADER */
.table thead{
background:#f8f9fa;
}

/* BUTTONS */
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
<div class="card-body d-flex justify-content-between align-items-center">

<h3 class="mb-0">Attendance Management</h3>

</div>
</div>

<div class="card shadow-sm">

<div class="card-header">

<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
+ Add Attendance
</button>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>Employee</th>
<th>Date</th>
<th>Time In</th>
<th>Time Out</th>
<th>Status</th>
<th>Remarks</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php

$get=$conn->query("
SELECT a.*,e.full_name
FROM attendance a
JOIN employees e ON e.id=a.employee_id
ORDER BY a.id DESC
");

while($row=$get->fetch()){
?>

<tr>

<td><?=$row['full_name']?></td>
<td><?=$row['att_date']?></td>
<td><?=$row['time_in']?></td>
<td><?=$row['time_out']?></td>
<td><?=$row['status']?></td>
<td><?=$row['remarks']?></td>

<td>

<a href="?delete=<?=$row['id']?>" onclick="return confirm('Delete attendance?')" class="btn btn-delete btn-sm">
Delete
</a>

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


<!-- ADD ATTENDANCE MODAL -->

<div class="modal fade" id="addModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Attendance</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>Employee</label>

<select name="employee_id" class="form-control">

<?php
$emp=$conn->query("SELECT * FROM employees");

while($e=$emp->fetch()){
?>

<option value="<?=$e['id']?>"><?=$e['full_name']?></option>

<?php } ?>

</select>

</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="att_date" class="form-control">
</div>

<div class="mb-3">
<label>Time In</label>
<input type="time" name="time_in" class="form-control">
</div>

<div class="mb-3">
<label>Time Out</label>
<input type="time" name="time_out" class="form-control">
</div>

<div class="mb-3">
<label>Status</label>
<select name="status" class="form-control">
<option>Present</option>
<option>Absent</option>
<option>On Leave</option>
</select>
</div>

<div class="mb-3">
<label>Remarks</label>
<input type="text" name="remarks" class="form-control">
</div>

</div>

<div class="modal-footer">

<button type="submit" name="save_attendance" class="btn btn-primary">
Save
</button>

</div>

</form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>
</body>
</html>
```
