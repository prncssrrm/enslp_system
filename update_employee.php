<?php
include 'config.php';

if(isset($_POST['update_employee'])){

$id=$_POST['id'];
$name=$_POST['full_name'];
$pos=$_POST['position'];
$dept=$_POST['department'];
$status=$_POST['employment_status'];
$date=$_POST['date_hired'];
$type=$_POST['salary_type'];
$salary=$_POST['salary_amount'];
$con=$_POST['contact_no'];

$stmt=$conn->prepare("UPDATE employees SET
full_name=?,
position=?,
department=?,
employment_status=?,
date_hired=?,
salary_type=?,
salary_amount=?,
contact_no=?
WHERE id=?");

$stmt->execute([$name,$pos,$dept,$status,$date,$type,$salary,$con,$id]);

header("Location: employees.php");
}
?>