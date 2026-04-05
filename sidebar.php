<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';

/* HR */
$hr_pages = ['employees.php','payroll.php','attendance.php','thirteenth_month.php'];
$is_hr_open = in_array($current_page,$hr_pages);

/* PRODUCTION */
$production_pages = ['projects.php','production_history.php','stock_movement.php'];
$is_production_open = in_array($current_page,$production_pages);

/* MANUFACTURING */
$manufacturing_pages = ['cutting.php','etching.php','lamination.php','inspection_qc.php'];
$is_manufacturing_open = in_array($current_page,$manufacturing_pages);

/* LOGISTICS */
$logistics_pages = ['packing.php','delivery.php'];
$is_logistics_open = in_array($current_page,$logistics_pages);

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

:root{
--sb-width:260px;
--primary-blue:#0d6efd;
--sidebar-bg:#243b55; /* mas elegant kaysa solid */
}

/* Sidebar gradient para hindi flat */
.sidebar{
background: linear-gradient(180deg, #2c4a6b 0%, #1f2f45 100%);
}

/* Hover effect mas visible */
.nav-link:hover{
background:rgba(255,255,255,0.08);
color:white;
}

/* Active mas pop */
.nav-link.active{
background:linear-gradient(90deg,#0d6efd,#3d8bfd);
color:white;
box-shadow:0 4px 10px rgba(13,110,253,0.3);
}

body{
font-family:'Inter',sans-serif!important;
margin:0!important;
background:#f8f9fa;
}

.sidebar{
position:fixed;
top:0;
left:0;
bottom:0;
width:var(--sb-width);
background:var(--sidebar-bg);
z-index:99999;
display:flex;
flex-direction:column;
padding:15px 0;
}

.sidebar-logo{
width:110px;
height:110px;
object-fit:contain;
background:white;
padding:12px;
border-radius:18px;
display:block;
margin:10px auto;
box-shadow:0 3px 8px rgba(0,0,0,0.25);
}


.brand-text{
color:white;
font-weight:700;
font-size:16px;
letter-spacing:1px;
text-align:center;
}

.sidebar-menu{
flex:1;
overflow-y:auto;
padding:15px;
}

.nav-link{
display:flex;
align-items:center;
color:#adb5bd;
padding:8px 15px;
text-decoration:none;
border-radius:6px;
margin-bottom:2px;
font-size:.85rem;
}

.nav-link i{
width:22px;
margin-right:8px;
}

.nav-link:hover{
background:rgba(255,255,255,.05);
color:white;
}

.nav-link.active{
background:var(--primary-blue);
color:white;
}

.menu-header{
color:#6c757d;
font-size:.65rem;
font-weight:700;
text-transform:uppercase;
margin:12px 0 5px 12px;
}

.user-footer{
margin-top:auto;
padding:15px;
border-top:1px solid #2d3238;
}

.user-name{
color:white;
font-size:.8rem;
font-weight:600;
}

.logout-btn{
color:#dc3545;
font-size:.8rem;
}

.logo-en{color:#39ff14;font-weight:700;}
.logo-slp{color:#00bfff;font-weight:700;}

</style>


<div class="sidebar">

<div class="brand-section text-center">

<img src="/enslp-main/logo.png" class="sidebar-logo">

<div class="brand-text">
<span class="logo-en">En</span><span class="logo-slp">SLP Inc.</span>
</div>

</div>

<div class="sidebar-menu">

<a href="dashboard.php" class="nav-link <?= ($current_page=='dashboard.php')?'active':'' ?>">
<i class="bi bi-speedometer2"></i> Dashboard
</a>




<!-- INVENTORY -->
<a href="inventory.php" class="nav-link <?= ($current_page=='inventory.php')?'active':'' ?>">
<i class="bi bi-box-seam"></i> Inventory
</a>


<!-- PRODUCTION -->

<a class="nav-link d-flex justify-content-between align-items-center <?= $is_production_open?'active':'' ?>"
data-bs-toggle="collapse"
href="#productionMenu">

<span><i class="bi bi-diagram-3"></i> Production</span>
<i class="bi bi-chevron-down small"></i>

</a>

<div class="collapse <?= $is_production_open?'show':'' ?>" id="productionMenu">

<a href="projects.php" class="nav-link ps-4 <?= ($current_page=='projects.php')?'active':'' ?>">
<i class="bi bi-kanban"></i> Work Orders
</a>

<a href="production_history.php" class="nav-link ps-4 <?= ($current_page=='production_history.php')?'active':'' ?>">
<i class="bi bi-clock-history"></i> Production History
</a>

<a href="stock_movement.php" class="nav-link ps-4 <?= ($current_page=='stock_movement.php')?'active':'' ?>">
<i class="bi bi-arrow-left-right"></i> Stock Movement
</a>

</div>


<!-- MANUFACTURING -->

<a class="nav-link d-flex justify-content-between align-items-center <?= $is_manufacturing_open?'active':'' ?>"
data-bs-toggle="collapse"
href="#manufacturingMenu">

<span><i class="bi bi-gear-wide-connected"></i> Manufacturing Process</span>
<i class="bi bi-chevron-down small"></i>

</a>

<div class="collapse <?= $is_manufacturing_open?'show':'' ?>" id="manufacturingMenu">

<a href="cutting.php" class="nav-link ps-4 <?= ($current_page=='cutting.php')?'active':'' ?>">
<i class="bi bi-scissors"></i> Cutting
</a>

<a href="etching.php" class="nav-link ps-4 <?= ($current_page=='etching.php')?'active':'' ?>">
<i class="bi bi-brush"></i> Etching
</a>

<a href="lamination.php" class="nav-link ps-4 <?= ($current_page=='lamination.php')?'active':'' ?>">
<i class="bi bi-layers"></i> Lamination
</a>

<a href="inspection_qc.php" class="nav-link ps-4 <?= ($current_page=='inspection_qc.php')?'active':'' ?>">
<i class="bi bi-search"></i> Inspection QC
</a>

</div>


<!-- ACCOUNTING -->

<a href="accounting.php" class="nav-link <?= ($current_page=='accounting.php')?'active':'' ?>">
<i class="bi bi-calculator"></i> Accounting
</a>



<?php if($user_role !== 'production'): ?>

<!-- LOGISTICS -->

<a class="nav-link d-flex justify-content-between align-items-center <?= $is_logistics_open?'active':'' ?>"
data-bs-toggle="collapse"
href="#logisticsMenu">

<span><i class="bi bi-truck"></i> Logistics</span>
<i class="bi bi-chevron-down small"></i>

</a>

<div class="collapse <?= $is_logistics_open?'show':'' ?>" id="logisticsMenu">

<a href="packing.php" class="nav-link ps-4 <?= ($current_page=='packing.php')?'active':'' ?>">
<i class="bi bi-box2-heart"></i> Packing
</a>

<a href="delivery.php" class="nav-link ps-4 <?= ($current_page=='delivery.php')?'active':'' ?>">
<i class="bi bi-truck"></i> Delivery
</a>

</div>

<?php endif; ?>

<!-- REPORTS -->

<a href="reports.php" class="nav-link <?= ($current_page=='reports.php')?'active':'' ?>">
<i class="bi bi-bar-chart"></i> Reports
</a>

<!-- HR -->
<a class="nav-link d-flex justify-content-between align-items-center <?= $is_hr_open?'active':'' ?>"
data-bs-toggle="collapse"
href="#hrMenu">
<span><i class="bi bi-person-vcard"></i> HR Management</span>
<i class="bi bi-chevron-down small"></i>
</a>

<div class="collapse <?= $is_hr_open?'show':'' ?>" id="hrMenu">

<a href="employees.php" class="nav-link ps-4 <?= ($current_page=='employees.php')?'active':'' ?>">
<i class="bi bi-people"></i> Employees
</a>

<a href="attendance.php" class="nav-link ps-4 <?= ($current_page=='attendance.php')?'active':'' ?>">
<i class="bi bi-calendar-check"></i> Attendance
</a>

<a href="payroll.php" class="nav-link ps-4 <?= ($current_page=='payroll.php')?'active':'' ?>">
<i class="bi bi-receipt-cutoff"></i> Payroll
</a>

<a href="thirteenth_month.php" class="nav-link ps-4 <?= ($current_page=='thirteenth_month.php')?'active':'' ?>">
<i class="bi bi-gift"></i> 13th Month
</a>

</div>

<?php if ($user_role === 'admin'): ?>

<p class="menu-header">Admin</p>

<a href="users.php" class="nav-link <?= ($current_page=='users.php')?'active':'' ?>">
<i class="bi bi-people"></i> Users
</a>

<a href="logs.php" class="nav-link <?= ($current_page=='logs.php')?'active':'' ?>">
<i class="bi bi-clock-history"></i> Logs
</a>

<?php endif; ?>

</div>


<div class="user-footer">

<small class="text-muted d-block">User:</small>
<span class="user-name">
<?= htmlspecialchars(
    ($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') .
    ' (' . ucfirst($_SESSION['role'] ?? 'role') . ')'
) ?>
</span>

<a href="logout.php" class="nav-link logout-btn">
<i class="bi bi-box-arrow-right"></i> Logout
</a>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>