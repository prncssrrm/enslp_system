<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php'; 
require_once 'access_control.php';
check_access(['admin']);

$q = trim($_GET['q'] ?? '');

function money2($n){ return number_format((float)$n, 2); }

/* ADD EMPLOYEE */
if (isset($_POST['add_employee'])) {

$name=$_POST['full_name'];
$pos=$_POST['position'];
$dept=$_POST['department'];
$status=$_POST['employment_status'];
$date=$_POST['date_hired'];
$type=$_POST['salary_type'];
$salary=$_POST['salary_amount'];
$con=$_POST['contact_no'];

$stmt=$conn->prepare("INSERT INTO employees
(full_name,position,department,employment_status,date_hired,salary_type,salary_amount,contact_no)
VALUES (?,?,?,?,?,?,?,?)");

$stmt->execute([$name,$pos,$dept,$status,$date,$type,$salary,$con]);

header("Location: employees.php");
exit();
}

/* UPDATE */
if (isset($_POST['update_employee'])) {

$id=$_POST['employee_id'];
$name=$_POST['full_name'];
$pos=$_POST['position'];
$dept=$_POST['department'];
$status=$_POST['employment_status'];
$date=$_POST['date_hired'];
$type=$_POST['salary_type'];
$salary=$_POST['salary_amount'];
$con=$_POST['contact_no'];

$stmt=$conn->prepare("
UPDATE employees
SET full_name=?,position=?,department=?,employment_status=?,date_hired=?,salary_type=?,salary_amount=?,contact_no=?
WHERE id=?");

$stmt->execute([$name,$pos,$dept,$status,$date,$type,$salary,$con,$id]);

header("Location: employees.php");
exit();
}

/* DELETE */
if(isset($_GET['delete'])){
$id=$_GET['delete'];

$stmt=$conn->prepare("DELETE FROM employees WHERE id=?");
$stmt->execute([$id]);

header("Location: employees.php");
exit();
}
?>
<!DOCTYPE html>
<html>
<head>

<title>Employees</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>

body{
background:#f4f6f9;
font-family:Arial;
margin:0;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:60px;
}

/* CARD */
.card{
border-radius:8px;
}

/* TABLE */
.table thead{
background:#f0f3f7;
}

/* BUTTON */
.btn-blue{
background:#0d6efd;
color:white;
border:none;
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
<div class="card-body d-flex justify-content-between align-items-center">

<h3 class="mb-0">Employees</h3>

</div>
</div>

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#addModal">
+ Add Employee
</button>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>Name</th>
<th>Position</th>
<th>Department</th>
<th>Status</th>
<th>Date Hired</th>
<th>Salary Type</th>
<th>Salary</th>
<th>Contact</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$get=$conn->query("SELECT * FROM employees ORDER BY id DESC");

while($row=$get->fetch()){
?>

<tr>

<td><?=$row['full_name']?></td>
<td><?=$row['position']?></td>
<td><?=$row['department']?></td>
<td><?=$row['employment_status']?></td>
<td><?=$row['date_hired']?></td>
<td><?=$row['salary_type']?></td>
<td>₱ <?=money2($row['salary_amount'])?></td>
<td><?=$row['contact_no']?></td>

<td>

<button 
class="btn btn-edit btn-sm"
data-bs-toggle="modal"
data-bs-target="#edit<?=$row['id']?>">

Edit
</button>

<a href="?delete=<?=$row['id']?>" 
onclick="return confirm('Delete employee?')" 
class="btn btn-delete btn-sm">

Delete

</a>

</td>

</tr>


<!-- EDIT MODAL -->

<div class="modal fade" id="edit<?=$row['id']?>">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<input type="hidden" name="employee_id" value="<?=$row['id']?>">

<div class="modal-header">

<h5>Edit Employee</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">
<label>Name</label>
<input type="text" name="full_name" class="form-control" value="<?=$row['full_name']?>" required>
</div>

<div class="mb-3">
<label>Position</label>
<input type="text" name="position" class="form-control" value="<?=$row['position']?>">
</div>

<div class="mb-3">
<label>Department</label>
<input type="text" name="department" class="form-control" value="<?=$row['department']?>">
</div>

<div class="mb-3">
<label>Status</label>
<select name="employment_status" class="form-control">

<option <?=$row['employment_status']=="Regular"?'selected':''?>>Regular</option>
<option <?=$row['employment_status']=="Probationary"?'selected':''?>>Probationary</option>
<option <?=$row['employment_status']=="Contractual"?'selected':''?>>Contractual</option>
<option <?=$row['employment_status']=="Part-time"?'selected':''?>>Part-time</option>

</select>
</div>

<div class="mb-3">
<label>Date Hired</label>
<input type="date" name="date_hired" class="form-control" value="<?=$row['date_hired']?>">
</div>

<div class="mb-3">
<label>Salary Type</label>
<select name="salary_type" class="form-control">

<option <?=$row['salary_type']=="Daily"?'selected':''?>>Daily</option>
<option <?=$row['salary_type']=="Monthly"?'selected':''?>>Monthly</option>

</select>
</div>

<div class="mb-3">
<label>Salary Amount</label>
<input type="number" name="salary_amount" class="form-control" value="<?=$row['salary_amount']?>">
</div>

<div class="mb-3">
<label>Contact</label>
<input type="text" name="contact_no" class="form-control" value="<?=$row['contact_no']?>">
</div>

</div>

<div class="modal-footer">

<button type="submit" name="update_employee" class="btn btn-primary">
Update
</button>

</div>

</form>

</div>

</div>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>


<!-- ADD MODAL -->

<div class="modal fade" id="addModal">

<div class="modal-dialog">

<form method="POST" class="modal-content">

<div class="modal-header">

<h5>Add Employee</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">
<label>Name</label>
<input type="text" name="full_name" class="form-control" required>
</div>

<div class="mb-3">
<label>Position</label>
<select name="position" id="position" class="form-control" required onchange="updateDepartment()">
    <option value="">Select Position</option>
    <option value="Production Operator">Production Operator</option>
    <option value="Accountant">Accountant</option>
    <option value="Staff">Staff</option>
    <option value="Engineer">Engineer</option>
    <option value="Nurse">Nurse</option>
</select>
</div>

<div class="mb-3">
<label>Department</label>
<select name="department" id="department" class="form-control" required>
    <option value="">Select Department</option>
</select>
</div>

<div class="mb-3">
<label>Status</label>
<select name="employment_status" class="form-control">
<option>Regular</option>
<option>Probationary</option>
<option>Contractual</option>
<option>Part-time</option>
</select>
</div>

<div class="mb-3">
<label>Date Hired</label>
<input type="date" name="date_hired" class="form-control">
</div>

<div class="mb-3">
<label>Salary Type</label>
<select name="salary_type" class="form-control">
<option>Daily</option>
<option>Monthly</option>
</select>
</div>

<div class="mb-3">
<label>Salary Amount</label>
<input type="number" name="salary_amount" class="form-control">
</div>

<div class="mb-3">
<label>Contact</label>
<input type="text" name="contact_no" class="form-control">
</div>

</div>

<div class="modal-footer">

<button type="submit" name="add_employee" class="btn btn-primary">
Save Employee
</button>

</div>

</form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateDepartment() {
    const position = document.getElementById("position").value;
    const department = document.getElementById("department");

    let options = [];

    if (position === "Production Operator") {
        options = ["Production"];
    } 
    else if (position === "Accountant") {
        options = ["Accounting"];
    } 
    else if (position === "Staff") {
        options = ["HR", "Admin"];
    } 
    else if (position === "Engineer") {
        options = ["Engineering", "Production"];
    } 
    else if (position === "Nurse") {
        options = ["Medical", "HR"];
    }

    department.innerHTML = '<option value="">Select Department</option>';

    options.forEach(function(dep) {
        department.innerHTML += `<option value="${dep}">${dep}</option>`;
    });
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>
```
