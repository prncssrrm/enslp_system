<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors',1);

/* SAFE COUNT FUNCTION */

function safeCount(PDO $conn, string $table): int{
    try{
        $stmt=$conn->query("SELECT COUNT(*) total FROM `$table`");
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }catch(Throwable $e){
        return 0;
    }
}

/* MAIN COUNTS */

$workOrders = safeCount($conn,"work_orders");
$inventory  = safeCount($conn,"inventory_items");
$stockTxn   = safeCount($conn,"stock_movements");

/* PRODUCTION COUNTS */

$cutting    = safeCount($conn,"cutting_jobs");
$etching    = safeCount($conn,"etching_jobs");
$lamination = safeCount($conn,"lamination_jobs");
$qc         = safeCount($conn,"inspection_qc");
$packing    = safeCount($conn,"packing_jobs");

/* DELIVERY */

$delivered=0;
$pending=0;

try{
$delivered=$conn->query("
SELECT COUNT(*) FROM deliveries
WHERE status='Delivered'
")->fetchColumn();

$pending=$conn->query("
SELECT COUNT(*) FROM deliveries
WHERE status='Pending'
")->fetchColumn();

}catch(Throwable $e){}

/* SALES */

$totalSales=0;

try{
$totalSales=$conn->query("
SELECT IFNULL(SUM(amount),0)
FROM accounting_transactions
WHERE type='Income'
")->fetchColumn();
}catch(Throwable $e){}

/* LATEST WORK ORDER */

$latestWO=null;

try{
$latestWO=$conn->query("
SELECT wo_no,product_name,status
FROM work_orders
ORDER BY id DESC
LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
}catch(Throwable $e){}

/* LOW STOCK */

$lowStocks=[];

try{
    $lowStocks=$conn->query("
    SELECT item_name,quantity,reorder_level
    FROM inventory_items
    WHERE quantity <= 10
    ORDER BY quantity ASC
    LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Throwable $e){}

/* MONTHLY GRAPH */

$monthlyLabels=[];
$monthlyData=[];

try{

$stmt=$conn->query("
SELECT DATE_FORMAT(created_at,'%b') m,
COUNT(*) total
FROM stock_movements
GROUP BY MONTH(created_at)
ORDER BY MONTH(created_at)
");

foreach($stmt as $r){
$monthlyLabels[]=$r['m'];
$monthlyData[]=$r['total'];
}

}catch(Throwable $e){}

/* TOP ITEMS GRAPH */

$topLabels=[];
$topData=[];

try{

$stmt=$conn->query("
SELECT ii.item_name,SUM(sm.quantity) total
FROM stock_movements sm
LEFT JOIN inventory_items ii ON ii.id=sm.item_id
GROUP BY sm.item_id
ORDER BY total DESC
LIMIT 5
");

foreach($stmt as $r){
$topLabels[]=$r['item_name'];
$topData[]=$r['total'];
}

}catch(Throwable $e){}
?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#f4f6f9;
font-family:Arial;
}

.dashboard-title{
background:#ffffff;
padding:10px 15px;
border:1px solid #ddd;
border-radius:8px 8px 0 0;
margin-bottom:15px;
font-weight:600;
}

.main-content{
margin-left:250px;
padding:25px;
}

.dashboard-container{
background:#ffffff;
border-radius:10px;
padding:20px;
border:1px solid #e0e0e0;
box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

.summary-card{
background:#fff;
border:1px solid #ddd;
border-radius:8px;
padding:12px;
text-align:center;
}

.summary-icon{
font-size:22px;
margin-bottom:5px;
}

.summary-number{
font-size:22px;
font-weight:bold;
}

.summary-label{
font-size:13px;
color:#666;
}

.card{
border-radius:8px;
}

.card-header{
font-size:14px;
font-weight:600;
}

#monthlyChart{
height:180px !important;
}

#topChart{
height:200px !important;
max-width:320px;
}

table{
font-size:13px;
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="dashboard-title">
<h4 class="mb-0">
<i class="bi bi-speedometer2"></i> Dashboard
</h4>
</div>

<div class="dashboard-container">


<!-- KPI SECTION -->

<div class="row g-2 mb-3">

<div class="col-md-3">
<div class="summary-card">
<div class="summary-icon text-primary">
<i class="bi bi-clipboard-check"></i>
</div>
<div class="summary-number text-primary"><?= $workOrders ?></div>
<div class="summary-label">Work Orders</div>
</div>
</div>

<div class="col-md-3">
<div class="summary-card">
<div class="summary-icon text-success">
<i class="bi bi-box-seam"></i>
</div>
<div class="summary-number text-success"><?= $inventory ?></div>
<div class="summary-label">Inventory Items</div>
</div>
</div>

<div class="col-md-3">
<div class="summary-card">
<div class="summary-icon text-warning">
<i class="bi bi-arrow-left-right"></i>
</div>
<div class="summary-number text-warning"><?= $stockTxn ?></div>
<div class="summary-label">Stock Movements</div>
</div>
</div>

<div class="col-md-3">
<div class="summary-card">
<div class="summary-icon text-dark">
<i class="bi bi-cash"></i>
</div>
<div class="summary-number text-dark">₱<?=number_format($totalSales,2)?></div>
<div class="summary-label">Total Revenue</div>
</div>
</div>

</div>


<!-- PRODUCTION KPI -->

<div class="row g-2 mb-3">

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $cutting ?></div>
<div class="summary-label">Cutting</div>
</div>
</div>

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $etching ?></div>
<div class="summary-label">Etching</div>
</div>
</div>

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $lamination ?></div>
<div class="summary-label">Lamination</div>
</div>
</div>

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $qc ?></div>
<div class="summary-label">QC</div>
</div>
</div>

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $packing ?></div>
<div class="summary-label">Packed</div>
</div>
</div>

<div class="col-md-2">
<div class="summary-card">
<div class="summary-number"><?= $delivered ?></div>
<div class="summary-label">Delivered</div>
</div>
</div>

</div>


<!-- CHARTS -->

<div class="row g-2 mb-3">

<div class="col-md-6">
<div class="card">
<div class="card-header">Monthly Stock Activity</div>
<div class="card-body">
<canvas id="monthlyChart"></canvas>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card">
<div class="card-header">Top Inventory Usage</div>
<div class="card-body d-flex justify-content-center">
<canvas id="topChart"></canvas>
</div>
</div>
</div>

</div>


<!-- LOWER CARDS -->

<div class="row g-2">

<div class="col-md-6">

<div class="card">

<div class="card-header">Latest Work Order</div>

<div class="card-body">

<?php if($latestWO): ?>

<b><?=htmlspecialchars($latestWO['wo_no'])?></b><br>
<?=htmlspecialchars($latestWO['product_name'])?><br>
Status: <?=htmlspecialchars($latestWO['status'])?>

<?php else: ?>

No data

<?php endif; ?>

</div>

</div>

</div>


<div class="col-md-6">

<div class="card">

<div class="card-header">Low Stock</div>

<div class="card-body">

<?php if(!$lowStocks): ?>

<span class="text-success">All items OK</span>

<?php else: ?>

<table class="table table-sm">

<tr>
<th>Item</th>
<th>Qty</th>
</tr>

<?php foreach($lowStocks as $ls): ?>

<tr>
<td><?=htmlspecialchars($ls['item_name'])?></td>
<td class="text-danger"><?= (int)$ls['quantity'] ?></td>
</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</div>

</div>

</div>

</div>

</div>

</div>


<script>

new Chart(
document.getElementById('monthlyChart'),
{
type:'line',
data:{
labels:<?=json_encode($monthlyLabels)?>,
datasets:[{
data:<?=json_encode($monthlyData)?>,
borderColor:'#0d6efd',
backgroundColor:'rgba(13,110,253,0.1)',
fill:true
}]
},
options:{responsive:true,maintainAspectRatio:false}
}
);


new Chart(
document.getElementById('topChart'),
{
type:'doughnut',
data:{
labels:<?=json_encode($topLabels)?>,
datasets:[{
data:<?=json_encode($topData)?>,
backgroundColor:[
'#0d6efd','#20c997','#ffc107','#dc3545','#6f42c1'
]
}]
},
options:{responsive:true,maintainAspectRatio:false}
}
);

</script>

<?php include 'footer.php'; ?>

</body>
</html>