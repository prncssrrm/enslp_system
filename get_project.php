<?php

require_once "config.php";

$id=$_GET['id'];

$stmt=$conn->prepare("
SELECT client,product_name,qty
FROM work_orders
WHERE id=?
");

$stmt->execute([$id]);

$data=$stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);