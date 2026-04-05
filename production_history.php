<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';
require_once 'access_control.php';
check_access(['admin','production']);

$current_page = basename($_SERVER['PHP_SELF']);

try { $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); } catch (Throwable $e) {}

$q = trim($_GET['q'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$hasFinishedAt = false;
try {
    $chk = $conn->query("SHOW COLUMNS FROM work_orders LIKE 'date_completed'")->fetch();
    $hasFinishedAt = $chk ? true : false;
} catch (Throwable $e) { $hasFinishedAt = false; }

$where = [];
$params = [];

if ($q !== '') {
    $where[] = "(wo_no LIKE ? OR product_name LIKE ? OR remarks LIKE ?)";
}

if ($statusFilter !== '') {
    $where[] = "status = ?";
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

$stats = [
  'total' => 0,
  'success' => 0,
  'failed' => 0,
  'yield' => 0
];

try {
    $total = (int)$conn->query("SELECT COUNT(*) FROM work_orders")->fetchColumn();
    $success = (int)$conn->query("SELECT COUNT(*) FROM work_orders WHERE status='Completed'")->fetchColumn();
    $failed  = (int)$conn->query("SELECT COUNT(*) FROM work_orders WHERE status='Failed'")->fetchColumn();
    $yield = ($total > 0) ? round(($success / $total) * 100) : 0;

    $stats = ['total'=>$total,'success'=>$success,'failed'=>$failed,'yield'=>$yield];
} catch (Throwable $e) {}

$rows = [];
try {

    $dateFinishedExpr = $hasFinishedAt
    ? "COALESCE(date_completed, created_at)"
    : "created_at";

    $sql = "
        SELECT
            id,
            wo_no,
            product_name,
            status,
            remarks,
            current_step,
            created_at,
            $dateFinishedExpr AS date_finished
       FROM production_history
        WHERE 1=1
    ";

    /* SEARCH */
    if ($q !== '') {
        $sql .= " AND (wo_no LIKE ? OR product_name LIKE ? OR remarks LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    /* STATUS FILTER (FIXED 🔥) */
    if ($statusFilter !== '') {
        $sql .= " AND LOWER(status) = LOWER(?)";
        $params[] = $statusFilter;
    }

    $sql .= " ORDER BY date_finished DESC, id DESC LIMIT 200";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    echo $e->getMessage(); // para makita error
}

function niceDate($dt) {
    if (!$dt) return '-';
    $ts = strtotime($dt);
    if (!$ts) return '-';
    return date("M d, Y", $ts);
}

function badge($status) {
    $s = strtolower(trim((string)$status));
    if ($s === 'completed') return ['success','COMPLETED'];
    if ($s === 'failed') return ['danger','FAILED'];
    if ($s === 'in progress') return ['primary','IN PROGRESS'];
    return ['secondary', strtoupper($status ?: 'PENDING')];
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Production History</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:Arial;
}

.main-content{
margin-left:260px;
padding:25px;
margin-top:60px;
}

.card{
border-radius:8px;
}

.stat-box{
background:white;
border-radius:8px;
padding:15px;
box-shadow:0 1px 6px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include 'header.php'; ?>

<div class="main-content">

<div class="container-fluid">

<div class="card shadow-sm mb-3">

<div class="card-body">

<h3 class="mb-0">Production History</h3>

</div>

</div>

<div class="row mb-4">

<div class="col-md-3">
<div class="stat-box text-center">
<h6>Total Jobs</h6>
<h4><?= (int)$stats['total'] ?></h4>
</div>
</div>

<div class="col-md-3">
<div class="stat-box text-center">
<h6>Success</h6>
<h4 class="text-success"><?= (int)$stats['success'] ?></h4>
</div>
</div>

<div class="col-md-3">
<div class="stat-box text-center">
<h6>Failed</h6>
<h4 class="text-danger"><?= (int)$stats['failed'] ?></h4>
</div>
</div>

<div class="col-md-3">
<div class="stat-box text-center">
<h6>Yield</h6>
<h4><?= (int)$stats['yield'] ?>%</h4>
</div>
</div>

</div>

<div class="card shadow-sm">

<div class="card-header">

<form class="row g-2" method="GET">

<div class="col-md-6">
<input type="text" name="q" class="form-control"
placeholder="Search WO, product, remarks..."
value="<?= htmlspecialchars($q) ?>">
</div>

<div class="col-md-3">
<select class="form-control" name="status">
<option value="">All Status</option>

<?php foreach (['Pending','In Progress','Completed','Failed'] as $s): ?>

<option value="<?= $s ?>" <?= ($statusFilter===$s)?'selected':'' ?>>
<?= $s ?>
</option>

<?php endforeach; ?>

</select>
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">Filter</button>
</div>

</form>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th>ID</th>
<th>Product</th>
<th>Status</th>
<th>Date Finished</th>
<th>Remarks</th>
</tr>
</thead>

<tbody>

<?php if (!$rows): ?>

<tr>
<td colspan="5" class="text-center">No records found</td>
</tr>

<?php else: ?>

<?php foreach ($rows as $r): ?>

<?php [$b,$label] = badge($r['status'] ?? ''); ?>

<tr>

<td>#<?= (int)$r['id'] ?></td>

<td>
<strong><?= htmlspecialchars($r['product_name'] ?? '-') ?></strong>
<br>
<small>
WO: <?= htmlspecialchars($r['wo_no'] ?? '-') ?>
<?php if (!empty($r['current_step'])): ?>
• Step: <?= htmlspecialchars($r['current_step']) ?>
<?php endif; ?>
</small>
</td>

<td>
<span class="badge bg-<?= $b ?>">
<?= $label ?>
</span>
</td>

<td><?= htmlspecialchars(niceDate($r['date_finished'] ?? null)) ?></td>

<td><?= htmlspecialchars($r['remarks'] ?? 'No remarks') ?></td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
```
