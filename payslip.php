<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("Invalid payslip."); }

function peso($n) { return number_format((float)$n, 2); }
function num($v) { return is_numeric($v) ? (float)$v : 0.0; }

// Fetch payroll + employee + salary fields
$stmt = $conn->prepare("
  SELECT 
    pr.*,
    e.full_name, e.department, e.position,
    e.salary_type, e.salary_amount,
    e.daily_rate, e.monthly_salary
  FROM payroll pr
  JOIN employees e ON e.id = pr.employee_id
  WHERE pr.id = ?
  LIMIT 1
");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { die("Payslip not found."); }

$company = "EnSLP Inc.";

/**
 * ✅ Salary label/value (new system):
 * - Use salary_type + salary_amount
 * - Fallback: if missing, infer from monthly_salary/daily_rate
 */
$salary_type = $row['salary_type'] ?? '';
$salary_amount = num($row['salary_amount'] ?? 0);

if ($salary_type === '' || $salary_amount <= 0) {
  $oldMonthly = num($row['monthly_salary'] ?? 0);
  $oldDaily   = num($row['daily_rate'] ?? 0);
  if ($oldMonthly > 0) { $salary_type = 'Monthly'; $salary_amount = $oldMonthly; }
  else { $salary_type = 'Daily'; $salary_amount = $oldDaily; }
}

$rateLabel = ($salary_type === 'Monthly') ? 'MONTHLY SALARY' : 'DAILY RATE';
$rateSuffix = ($salary_type === 'Monthly') ? '/month' : '/day';

/**
 * ✅ Deductions (show breakdown if exists)
 */
$sss        = num($row['sss'] ?? 0);
$philhealth = num($row['philhealth'] ?? 0);
$pagibig    = num($row['pagibig'] ?? 0);
$other_ded  = num($row['other_deductions'] ?? 0);

// total deductions computed
$final_deductions = $sss + $philhealth + $pagibig + $other_ded;

// gross + net from payroll row
$gross = num($row['gross_pay'] ?? 0);
$net   = num($row['net_pay'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payslip #<?= (int)$row['id'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
  :root{
    --primary:#0d6efd;
    --bg:#f5f8ff;
    --card:#ffffff;
    --border:#e7eefc;
    --text:#111827;
    --muted:#64748b;
  }

  body{
    background:var(--bg);
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color:var(--text);
  }

  .wrap{
    max-width: 860px;
    margin: 28px auto;
    padding: 0 14px;
  }

  /* top buttons */
  .no-print .btn{
    border-radius: 10px !important;
    padding: 8px 12px !important;
    font-weight: 600;
    font-size: .85rem;
  }
  .btn-outline-secondary{
    border:1px solid var(--border) !important;
    color:#334155 !important;
    background:#fff !important;
  }
  .btn-outline-secondary:hover{
    background:#f3f7ff !important;
  }
  .btn-dark{
    background: var(--primary) !important;
    border:none !important;
  }
  .btn-dark:hover{ filter:brightness(.95); }

  /* card */
  .cardx{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:16px;
    padding:22px 22px;
  }

  .muted{ color:var(--muted); }

  .line{
    height:1px;
    background:var(--border);
    margin:16px 0;
  }

  /* header text */
  h4.fw-bold{
    font-weight: 800 !important;
    letter-spacing: .2px;
  }

  /* tables */
  .table.table-sm{
    margin-bottom:0;
  }
  .table.table-sm td{
    padding: 10px 8px;
    border-color:#f1f5ff !important;
    font-size: .92rem;
  }
  .table.table-sm tr:last-child td{
    border-bottom: none;
  }

  /* highlight net pay row */
  .net-row td{
    background:#f3f7ff;
    border-top:1px solid var(--border);
    border-bottom:1px solid var(--border);
    font-size: 1rem;
  }

  /* Print style */
  @media print{
    .no-print{ display:none !important; }
    body{ background:#fff; }
    .wrap{ margin:0; max-width:none; padding:0; }
    .cardx{
      border:none;
      border-radius:0;
      padding:0;
    }
    .line{ background:#ddd; }
  }
</style>

  </style>
</head>
<body>

<div class="wrap">
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="payroll.php" class="btn btn-outline-secondary btn-sm">← Back</a>
    <button class="btn btn-dark btn-sm" onclick="window.print()">Print</button>
  </div>

  <div class="cardx shadow-sm">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h4 class="fw-bold mb-1"><?= htmlspecialchars($company) ?></h4>
        <div class="muted">Payslip</div>
      </div>
      <div class="text-end">
        <div class="fw-semibold">Payslip #<?= (int)$row['id'] ?></div>
        <div class="muted" style="font-size:13px;">
          Period: <?= htmlspecialchars($row['period_start']) ?> to <?= htmlspecialchars($row['period_end']) ?>
        </div>
      </div>
    </div>

    <div class="line"></div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="muted" style="font-size:12px;">EMPLOYEE</div>
        <div class="fw-semibold"><?= htmlspecialchars($row['full_name']) ?></div>
        <div class="muted" style="font-size:13px;">
          <?= htmlspecialchars($row['position'] ?? '') ?> • <?= htmlspecialchars($row['department'] ?? '') ?>
        </div>
      </div>
      <div class="col-md-6 text-md-end">
        <div class="muted" style="font-size:12px;"><?= htmlspecialchars($rateLabel) ?></div>
        <div class="fw-semibold">₱<?= peso($salary_amount) ?> <span class="muted" style="font-size:12px;"><?= htmlspecialchars($rateSuffix) ?></span></div>
      </div>
    </div>

    <div class="line"></div>

    <div class="row">
      <div class="col-md-6">
        <table class="table table-sm mb-0">
          <tr>
            <td class="muted">Days Worked</td>
            <td class="text-end fw-semibold"><?= htmlspecialchars($row['days_worked']) ?></td>
          </tr>
          <tr>
            <td class="muted">Overtime Hours</td>
            <td class="text-end fw-semibold"><?= htmlspecialchars($row['overtime_hours']) ?></td>
          </tr>
          <tr>
            <td class="muted">OT Rate</td>
            <td class="text-end fw-semibold">₱<?= peso($row['overtime_rate']) ?></td>
          </tr>
        </table>
      </div>

      <div class="col-md-6">
        <table class="table table-sm mb-0">
          <tr>
            <td class="muted">Gross Pay</td>
            <td class="text-end fw-semibold">₱<?= peso($gross) ?></td>
          </tr>

          <?php if (($sss + $philhealth + $pagibig) > 0): ?>
            <tr>
              <td class="muted">SSS</td>
              <td class="text-end fw-semibold">₱<?= peso($sss) ?></td>
            </tr>
            <tr>
              <td class="muted">PhilHealth</td>
              <td class="text-end fw-semibold">₱<?= peso($philhealth) ?></td>
            </tr>
            <tr>
              <td class="muted">Pag-IBIG</td>
              <td class="text-end fw-semibold">₱<?= peso($pagibig) ?></td>
            </tr>
          <?php endif; ?>

          <?php if ($other_ded > 0): ?>
            <tr>
              <td class="muted">Other Deductions</td>
              <td class="text-end fw-semibold">₱<?= peso($other_ded) ?></td>
            </tr>
          <?php endif; ?>

          <tr>
            <td class="muted">Deductions</td>
            <td class="text-end fw-semibold">₱<?= peso($final_deductions) ?></td>
          </tr>

          <tr class="net-row">
  <td class="fw-bold">Net Pay</td>
  <td class="text-end fw-bold">₱<?= peso($net) ?></td>
</tr>

        </table>
      </div>
    </div>

    <div class="line"></div>

    <div class="muted" style="font-size:12px;">
    </div>
  </div>
</div>

</body>
</html>
