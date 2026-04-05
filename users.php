<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php'; 

// --- ACCESS CONTROL ---
$user_role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';
if (!isset($_SESSION['user_id']) || $user_role !== 'admin') {
    header("Location: index.php");
    exit();
}

// --- DELETE USER ---
if (isset($_GET['delete_user'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->execute([$_GET['delete_user'], $_SESSION['user_id']]);
    header("Location: users.php?status=deleted");
    exit();
}

// --- ADD USER ---
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; 

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    header("Location: users.php?status=added");
    exit();
}

// --- UPDATE USER ---
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $password, $role, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $role, $id]);
    }

    header("Location: users.php?status=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>

/* ===== BODY ===== */
body{
margin:0;
background:#f5f6fa;
font-family:'Segoe UI', sans-serif;
}

/* ===== MAIN CONTENT FIX ===== */
.main-content{
margin-left:260px; /* match sa sidebar mo */
width:calc(100% - 260px);
padding:20px;
margin-top:70px;
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

/* ===== BUTTON ===== */
.btn-primary-custom{
background:#2d7ef7;
color:#fff;
border:none;
padding:10px 15px;
border-radius:6px;
}

.btn-primary-custom:hover{
background:#1c5ed6;
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

/* ===== ACTION BUTTON ===== */
.icon-btn{
border:none;
background:none;
cursor:pointer;
margin-right:5px;
}

</style>
</head>

<body>

<?php include 'sidebar.php'; ?>
<?php include "header.php"; ?>

<div class="main-content">

    <!-- TITLE -->
    <div class="title-card">
        <h3>User Management</h3>
    </div>

    <!-- TABLE -->
    <div class="table-card">

        <div style="margin-bottom:15px;">
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addUserModal">
                + Add User
            </button>
        </div>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $users = $conn->query("SELECT * FROM users ORDER BY id DESC");

                while($row = $users->fetch(PDO::FETCH_ASSOC)):
                ?>

                <tr>

                    <td>
                        <strong><?= htmlspecialchars($row['username']) ?></strong><br>
                        <small>ID: #<?= $row['id'] ?></small>
                    </td>

                    <td><?= htmlspecialchars($row['role']) ?></td>

                    <td>

                        <button class="icon-btn"
                        onclick="openEditModal('<?= $row['id'] ?>','<?= htmlspecialchars($row['username']) ?>','<?= $row['role'] ?>')">
                        <i class="bi bi-pencil"></i>
                        </button>

                        <?php if($row['id'] != $_SESSION['user_id']): ?>

                        <button class="icon-btn"
                        onclick="confirmDelete(<?= $row['id'] ?>,'<?= htmlspecialchars($row['username']) ?>')">
                        <i class="bi bi-trash"></i>
                        </button>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- MODALS (UNCHANGED) -->

<div class="modal fade" id="addUserModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>New User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="mb-3">
<label>Username</label>
<input type="text" name="username" class="form-control" required>
</div>
<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>
<div class="mb-3">
<label>Role</label>
<select name="role" class="form-select" required>
<option value="Admin">Admin</option>
<option value="Accounting">Accounting</option>
<option value="Production">Production</option>
<option value="Engineer">Engineer</option>
<option value="Staff">Staff</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="submit" name="add_user" class="btn btn-primary w-100">
Create Account
</button>
</div>
</form>
</div>
</div>
</div>

<div class="modal fade" id="editUserModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST">
<input type="hidden" name="user_id" id="edit_user_id">
<div class="modal-header">
<h5>Edit User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="mb-3">
<label>Username</label>
<input type="text" name="username" id="edit_username" class="form-control" required>
</div>
<div class="mb-3">
<label>New Password</label>
<input type="password" name="password" class="form-control">
</div>
<div class="mb-3">
<label>Role</label>
<select name="role" id="edit_role" class="form-select" required>
<option value="Admin">Admin</option>
<option value="Accounting">Accounting</option>
<option value="Production">Production</option>
<option value="Engineer">Engineer</option>
<option value="Staff">Staff</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="submit" name="update_user" class="btn btn-primary w-100">
Save Changes
</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openEditModal(id,username,role){
document.getElementById('edit_user_id').value=id;
document.getElementById('edit_username').value=username;
document.getElementById('edit_role').value=role;
new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function confirmDelete(id,name){
Swal.fire({
title:'Delete User?',
text:'Remove '+name+'?',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
confirmButtonText:'Yes delete'
}).then((result)=>{
if(result.isConfirmed){
window.location='users.php?delete_user='+id;
}
});
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>