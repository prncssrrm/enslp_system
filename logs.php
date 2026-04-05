<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ===== BODY ===== */
body{
margin:0;
background:#f5f6fa;
font-family:'Segoe UI', sans-serif;
}

/* ===== MAIN CONTENT ===== */
.main-content{
margin-left:260px;
width:calc(100% - 260px);
padding:20px;
margin-top:80px;
}

/* ===== TITLE CARD ===== */
.title-card{
background:#fff;
padding:15px 20px;
border-radius:10px;
margin-bottom:15px;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

/* ===== TABLE CARD ===== */
.table-card{
background:#fff;
border-radius:10px;
padding:15px;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

/* ===== SEARCH ===== */
.search-box{
margin-bottom:15px;
}

.search-box input{
width:250px;
padding:8px 10px;
border-radius:6px;
border:1px solid #ccc;
}

/* ===== TABLE ===== */
table{
width:100%;
border-collapse:collapse;
}

table th{
background:#f1f1f1;
padding:10px;
text-align:left;
}

table td{
padding:10px;
border-top:1px solid #ddd;
}

</style>
</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include "header.php"; ?>

<div class="main-content">

    <!-- TITLE -->
    <div class="title-card">
        <h3>Activity Logs</h3>
    </div>

    <!-- TABLE -->
    <div class="table-card">

        <div class="search-box">
            <input type="text" id="logSearch" placeholder="Search logs...">
        </div>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $query = "SELECT l.*, u.username 
                          FROM inventory_logs l 
                          LEFT JOIN users u ON l.user_id = u.id 
                          ORDER BY l.timestamp DESC";
                $stmt = $conn->query($query);

                if($stmt->rowCount() > 0):
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>

                <tr>
                    <td><?= date('M d, Y h:i A', strtotime($row['timestamp'])) ?></td>
                    <td><strong><?= htmlspecialchars($row['username'] ?? 'System') ?></strong></td>
                    <td><?= htmlspecialchars($row['action_type']) ?></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= number_format($row['quantity']) ?></td>
                    <td><?= htmlspecialchars($row['remarks'] ?? '-') ?></td>
                </tr>

                <?php endwhile; else: ?>

                <tr>
                    <td colspan="6" class="text-center">No logs found</td>
                </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
document.getElementById('logSearch').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? "" : "none";
    });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>