<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- LOGIC: START JOB ---
if (isset($_POST['start_job'])) {
    $p_name = trim($_POST['project_name']);
    if (!empty($p_name)) {
        $stmt = $conn->prepare("INSERT INTO production_jobs (product_name, status, date_started) VALUES (?, 'PENDING', NOW())");
        $stmt->execute([$p_name]);
        header("Location: index.php"); 
        exit();
    }
}

// --- LOGIC: UPDATE STATUS (NORMAL) ---
if (isset($_POST['update_status'])) {
    $id = $_POST['job_id'];
    $new_status = $_POST['new_status'];
    $current_date = date('Y-m-d H:i:s');

    if (in_array($new_status, ['COMPLETED', 'Delivery'])) {
        $stmt = $conn->prepare("UPDATE production_jobs SET status = ?, date_completed = ? WHERE id = ?");
        $stmt->execute([$new_status, $current_date, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE production_jobs SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
    }
    header("Location: index.php");
    exit();
}

// --- LOGIC: UPDATE STATUS (FAIL WITH REASON) ---
if (isset($_POST['update_status_fail'])) {
    $id = $_POST['job_id'];
    $reason = trim($_POST['fail_reason']);
    $current_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE production_jobs SET status = 'FAIL', fail_reason = ?, date_completed = ? WHERE id = ?");
    $stmt->execute([$reason, $current_date, $id]);
    header("Location: index.php");
    exit();
}

// --- LOGIC: DELETE ---
if (isset($_GET['delete_job']) && $_SESSION['role'] == 'Admin') {
    $stmt = $conn->prepare("DELETE FROM production_jobs WHERE id = ?");
    $stmt->execute([$_GET['delete_job']]);
    header("Location: index.php");
    exit();
}

// --- LOGIC: INVENTORY STATS ---
$total_items = $conn->query("SELECT COUNT(*) FROM inventory")->fetchColumn() ?: 0;
$low_stock = $conn->query("SELECT COUNT(*) FROM inventory WHERE quantity <= alert_level")->fetchColumn() ?: 0;
$criticalItems = $conn->query("SELECT item_name, quantity, alert_level FROM inventory WHERE quantity <= alert_level ORDER BY quantity ASC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnSLP IMS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { margin: 0; padding: 0; background-color: #f0f2f5; font-family: sans-serif; }
        .main-content { margin-left: 260px; padding: 25px; min-height: 100vh; width: calc(100% - 260px); }
        .dash-card { border: 1px solid #dee2e6 !important; border-radius: 4px !important; background: #fff; box-shadow: none !important; }
        .monitoring-table { background: white; border: 1px solid #dee2e6; border-radius: 4px; padding: 20px; }
        .status-pending { background-color: #6c757d !important; color: white !important; } 
        .status-process { background-color: #0d6efd !important; color: white !important; } 
        .status-qc      { background-color: #fd7e14 !important; color: white !important; }      
        .status-select { cursor: pointer; border-radius: 4px !important; font-weight: bold; font-size: 0.8rem; padding: 5px !important; border: 1px solid #ccc !important; }
        
        /* Minimalist Square Modal */
        .modal-content { border-radius: 0; border: 1px solid #333; }
        .modal-header { background: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 12px 20px; }
        .btn-flat { border-radius: 0; }
        textarea.form-control { border-radius: 0; border: 1px solid #ccc; font-size: 0.95rem; }

        @media (max-width: 768px) { .main-content { margin-left: 0; width: 100%; padding: 15px; } }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="mb-4">
        <h4 class="fw-bold">Main Dashboard</h4>
        <small class="text-muted">User: <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?> | Date: <?php echo date('Y-m-d'); ?></small>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card dash-card">
                <div class="card-body">
                    <small class="text-uppercase fw-bold text-muted small">Total Inventory Items</small>
                    <h2 class="mb-0 fw-bold"><?php echo $total_items; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card dash-card border-danger">
                <div class="card-body">
                    <small class="text-uppercase fw-bold text-danger small">Low Stock Alerts</small>
                    <h2 class="mb-0 fw-bold text-danger"><?php echo $low_stock; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card dash-card p-3 mb-3">
                <label class="fw-bold mb-2 small">Start New Production Job</label>
                <form method="POST" class="d-flex gap-2">
                    <input type="text" name="project_name" class="form-control form-control-sm" placeholder="Product Name..." style="border-radius:0;" required>
                    <button type="submit" name="start_job" class="btn btn-primary btn-sm px-4 fw-bold btn-flat">ADD JOB</button>
                </form>
            </div>

            <div class="monitoring-table">
                <h6 class="fw-bold mb-3 text-uppercase small">Production Monitoring</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="p-2">ID</th>
                                <th class="p-2">Product</th>
                                <th class="p-2" width="180">Status</th>
                                <th class="p-2 text-center">Del</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php
                            $jobs = $conn->query("SELECT * FROM production_jobs WHERE status NOT IN ('FAIL', 'COMPLETED', 'Delivery') ORDER BY id DESC");
                            foreach ($jobs as $row):
                                $statusClass = 'status-pending';
                                if(in_array($row['status'], ['Cutting', 'Etching', 'Lamination'])) $statusClass = 'status-process';
                                if($row['status'] == 'Inspection/QC') $statusClass = 'status-qc';
                            ?>
                            <tr>
                                <td class="p-2">#<?php echo $row['id']; ?></td>
                                <td class="p-2 fw-bold"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td class="p-2">
                                    <form method="POST">
                                        <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">
                                        <select name="new_status" onchange="handleStatusChange(this, '<?php echo $row['id']; ?>')" 
                                                class="form-select form-select-sm status-select <?php echo $statusClass; ?>">
                                            <option value="PENDING" <?php if($row['status'] == 'PENDING') echo 'selected'; ?>>PENDING</option>
                                            <option value="Cutting" <?php if($row['status'] == 'Cutting') echo 'selected'; ?>>1. Cutting</option>
                                            <option value="Etching" <?php if($row['status'] == 'Etching') echo 'selected'; ?>>2. Etching</option>
                                            <option value="Lamination" <?php if($row['status'] == 'Lamination') echo 'selected'; ?>>3. Lamination</option>
                                            <option value="Inspection/QC" <?php if($row['status'] == 'Inspection/QC') echo 'selected'; ?>>4. Inspection/QC</option>
                                            <option value="FAIL" class="text-danger">✘ FAIL</option>
                                            <option value="COMPLETED" class="text-success">✔ COMPLETED</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if($_SESSION['role'] == 'Admin'): ?>
                                        <a href="index.php?delete_job=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Delete?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dash-card p-3">
                <h6 class="fw-bold mb-3 text-uppercase small">Low Stock Watchlist</h6>
                <table class="table table-sm">
                    <thead class="small">
                        <tr><th>Item</th><th class="text-end">Qty</th></tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($criticalItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td class="text-end text-danger fw-bold"><?php echo $item['quantity']; ?></td>
                        </tr>
                        <?php endforeach; if($low_stock == 0) echo "<tr><td colspan='2' class='text-muted'>Clear</td></tr>"; ?>
                    </tbody>
                </table>
                <a href="inventory.php" class="btn btn-outline-dark btn-sm w-100 mt-2 btn-flat">Open Inventory</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="failModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md"> <div class="modal-content shadow">
            <form method="POST">
                <div class="modal-header">
                    <span class="fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Log Failure Details</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="location.reload()"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="job_id" id="modal_job_id">
                    <div class="mb-1">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">State the reason for production failure:</label>
                        <textarea name="fail_reason" class="form-control" rows="6" 
                                  placeholder="Input details here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light btn-sm btn-flat border px-3" data-bs-dismiss="modal" onclick="location.reload()">Cancel</button>
                    <button type="submit" name="update_status_fail" class="btn btn-dark btn-sm btn-flat px-4">Save Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function handleStatusChange(selectElement, jobId) {
    if (selectElement.value === 'FAIL') {
        document.getElementById('modal_job_id').value = jobId;
        var myModal = new bootstrap.Modal(document.getElementById('failModal'));
        myModal.show();
    } else {
        selectElement.form.submit();
    }
}
</script>
</body>
</html>