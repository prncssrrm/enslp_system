<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Filters */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';
$ftype = $_GET['ftype'] ?? 'All';

$where = [];
$params = [];

if ($from !== '') { $where[] = "txn_date >= ?"; $params[] = $from; }
if ($to !== '')   { $where[] = "txn_date <= ?"; $params[] = $to; }
if ($ftype === 'Income' || $ftype === 'Expense') { $where[] = "type = ?"; $params[] = $ftype; }

$whereSql = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";

/* Totals */
$stmt = $conn->prepare("
  SELECT
    COALESCE(SUM(CASE WHEN type='Income' THEN amount ELSE 0 END),0) AS total_income,
    COALESCE(SUM(CASE WHEN type='Expense' THEN amount ELSE 0 END),0) AS total_expense
  FROM accounting_transactions
  $whereSql
");
$stmt->execute($params);
$sum = $stmt->fetch();
$total_income = (float)$sum['total_income'];
$total_expense = (float)$sum['total_expense'];
$net = $total_income - $total_expense;

/* Monthly */
$stmt = $conn->prepare("
  SELECT
    DATE_FORMAT(txn_date, '%Y-%m') AS ym,
    COALESCE(SUM(CASE WHEN type='Income' THEN amount ELSE 0 END),0) AS income,
    COALESCE(SUM(CASE WHEN type='Expense' THEN amount ELSE 0 END),0) AS expense
  FROM accounting_transactions
  $whereSql
  GROUP BY ym
  ORDER BY ym DESC
");
$stmt->execute($params);
$monthly = $stmt->fetchAll();

/* Rows */
$stmt = $conn->prepare("
  SELECT *
  FROM accounting_transactions
  $whereSql
  ORDER BY txn_date DESC, id DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$rangeLabel = "All Dates";
if ($from && $to) $rangeLabel = "$from to $to";
elseif ($from) $rangeLabel = "From $from";
elseif ($to) $rangeLabel = "Up to $to";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Accounting Report</title>
<style>
body{ font-family: Arial, sans-serif; margin:20px; }
.header{ display:flex; justify-content:space-between; align-items:flex-start; gap:20px; }
h1{ margin:0; }
.small{ color:#555; font-size:12px; }
.boxes{ display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; margin:16px 0; }
.box{ border:1px solid #ddd; border-radius:10px; padding:12px; }
.val{ font-size:20px; font-weight:700; margin-top:4px; }
table{ width:100%; border-collapse:collapse; margin-top:10px; }
th,td{ padding:8px; border-bottom:1px solid #eee; text-align:left; font-size:13px; }
.btns{ display:flex; gap:10px; }
button{ padding:10px 14px; border-radius:10px; border:none; background:#111; color:#fff; cursor:pointer; }
@media print{
  .btns{ display:none; }
  body{ margin:0; }
}
</style>
</head>
<body>

<div class="header">
  <div>
    <h1>Accounting Report</h1>
    <div class="small">Date Range: <b><?= htmlspecialchars($rangeLabel) ?></b></div>
    <div class="small">Type Filter: <b><?= htmlspecialchars($ftype) ?></b></div>
    <div class="small">Generated: <b><?= date('Y-m-d H:i') ?></b></div>
  </div>

  <div class="btns">
    <button onclick="window.print()">Print / Save as PDF</button>
    <button onclick="window.close()">Close</button>
  </div>
</div>

<div class="boxes">
  <div class="box">
    <div class="small">Total Income</div>
    <div class="val">₱ <?= number_format($total_income, 2) ?></div>
  </div>
  <div class="box">
    <div class="small">Total Expense</div>
    <div class="val">₱ <?= number_format($total_expense, 2) ?></div>
  </div>
  <div class="box">
    <div class="small">Net</div>
    <div class="val">₱ <?= number_format($net, 2) ?></div>
  </div>
</div>

<h3>Monthly Summary</h3>
<table>
  <thead>
    <tr>
      <th>Month</th>
      <th>Income</th>
      <th>Expense</th>
      <th>Net</th>
    </tr>
  </thead>
  <tbody>
    <?php if(empty($monthly)): ?>
      <tr><td colspan="4" style="color:#777;">No data.</td></tr>
    <?php endif; ?>
    <?php foreach($monthly as $m): ?>
      <?php $mnet = (float)$m['income'] - (float)$m['expense']; ?>
      <tr>
        <td><?= htmlspecialchars($m['ym']) ?></td>
        <td>₱ <?= number_format((float)$m['income'], 2) ?></td>
        <td>₱ <?= number_format((float)$m['expense'], 2) ?></td>
        <td><b>₱ <?= number_format($mnet, 2) ?></b></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3 style="margin-top:18px;">Transactions</h3>
<table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Type</th>
      <th>Category</th>
      <th>Ref</th>
      <th>WO</th>
      <th>Description</th>
      <th>Method</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php if(empty($rows)): ?>
      <tr><td colspan="8" style="color:#777;">No transactions.</td></tr>
    <?php endif; ?>

    <?php foreach($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['txn_date']) ?></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['category']) ?></td>
        <td><?= htmlspecialchars($r['reference_no'] ?? '') ?></td>
        <td><?= $r['wo_id'] ? 'WO '.$r['wo_id'] : '' ?></td>
        <td><?= htmlspecialchars($r['description'] ?? '') ?></td>
        <td><?= htmlspecialchars($r['payment_method'] ?? '') ?></td>
        <td><b>₱ <?= number_format((float)$r['amount'], 2) ?></b></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
