<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- LOGIC HANDLERS ---
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: suppliers.php?status=success");
    exit();
}

if (isset($_POST['save_supplier'])) {
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_person, phone, email, supplied_items, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['supplier_name'], $_POST['contact_person'], $_POST['phone'], $_POST['email'], $_POST['supplied_items'], $_POST['address']]);
    header("Location: suppliers.php?status=success");
    exit();
}

if (isset($_POST['update_supplier'])) {
    $stmt = $conn->prepare("UPDATE suppliers SET supplier_name=?, contact_person=?, phone=?, email=?, supplied_items=?, address=? WHERE id=?");
    $stmt->execute([$_POST['supplier_name'], $_POST['contact_person'], $_POST['phone'], $_POST['email'], $_POST['supplied_items'], $_POST['address'], $_POST['supplier_id']]);
    header("Location: suppliers.php?status=success");
    exit();
}

$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY supplier_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers Directory - ENSLP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --sidebar-width: 260px; --dark-gray: #333; --light-gray: #666; }
        body { background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #333; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; width: calc(100% - var(--sidebar-width)); }
        
        /* Industrial Gray Buttons */
        .btn-action { background-color: #1a1a1a; border: 1px solid #1a1a1a; color: #fff; font-size: 0.85rem; padding: 8px 15px; border-radius: 4px; transition: 0.3s; }
        .btn-action:hover { background-color: #444; color: white; border-color: #444; }
        .btn-gray-outline { color: #666; border: 1px solid #ddd; background: transparent; }
        .btn-gray-outline:hover { background: #f5f5f5; color: #333; border-color: #ccc; }
        
        /* Alisin ang blue outline sa inputs */
        .form-control:focus { border-color: #ccc; box-shadow: none; outline: none; }
        
        .content-card { background: white; border: 1px solid #eee; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .table thead th { background: #fafafa; color: #888; font-size: 0.7rem; text-transform: uppercase; padding: 15px; }
        .table td { vertical-align: middle; padding: 15px; font-size: 0.9rem; border-bottom: 1px solid #f8f8f8; }
        .supplied-tag { display: inline-block; background: #f8f9fa; border: 1px solid #eee; padding: 2px 8px; border-radius: 3px; font-size: 0.75rem; color: #555; margin-right: 4px; }
        
        .search-container { position: relative; width: 280px; }
        .search-container .bi-search { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #888; }
        .search-container input { padding-left: 35px; font-size: 0.85rem; border-radius: 4px; border: 1px solid #ddd; }
        
        @media (max-width: 768px) { .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold">Suppliers Directory</h4>
            <p class="text-muted small mb-0">Manage your material sources and vendor contact information.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <div class="search-container">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search supplier or item...">
            </div>
            <button class="btn btn-action shadow-sm" onclick="prepareAdd()">
                <i class="bi bi-plus-lg me-2"></i>Add New Supplier
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suppliersTable">
                <thead>
                    <tr>
                        <th class="ps-4">Supplier / Address</th>
                        <th>Contact Person</th>
                        <th>Details</th>
                        <th>Supplies</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $suppliers->fetch()): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold"><?= htmlspecialchars($row['supplier_name']) ?></span><br>
                            <small class="text-muted"><?= htmlspecialchars($row['address']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['contact_person'] ?: '---') ?></td>
                        <td style="font-size: 0.8rem;">
                            <div><i class="bi bi-telephone"></i> <?= htmlspecialchars($row['phone']) ?></div>
                            <div class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($row['email'] ?: 'N/A') ?></div>
                        </td>
                        <td>
                            <?php 
                                $items = explode(',', $row['supplied_items']);
                                foreach($items as $item) if(!empty(trim($item))) echo '<span class="supplied-tag">'.htmlspecialchars(trim($item)).'</span>';
                            ?>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-gray-outline btn-edit" 
                                    data-id="<?= $row['id'] ?>"
                                    data-name="<?= htmlspecialchars($row['supplier_name']) ?>"
                                    data-contact="<?= htmlspecialchars($row['contact_person']) ?>"
                                    data-phone="<?= htmlspecialchars($row['phone']) ?>"
                                    data-email="<?= htmlspecialchars($row['email']) ?>"
                                    data-items="<?= htmlspecialchars($row['supplied_items']) ?>"
                                    data-address="<?= htmlspecialchars($row['address']) ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-gray-outline text-danger" 
                                    onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['supplier_name'], ENT_QUOTES) ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h6 class="fw-bold mb-0" id="modalTitle">Add New Supplier</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="supplier_id" id="supplier_id">
                <div class="modal-body py-0">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">SUPPLIER NAME</label>
                        <input type="text" name="supplier_name" id="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="small fw-bold text-muted">CONTACT PERSON</label>
                            <input type="text" name="contact_person" id="contact" class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label class="small fw-bold text-muted">PHONE</label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">EMAIL ADDRESS</label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">SUPPLIES (Comma separated)</label>
                        <input type="text" name="supplied_items" id="items" class="form-control form-control-sm" placeholder="e.g. Steel, Cement">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">OFFICE ADDRESS</label>
                        <textarea name="address" id="address" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="submit" name="update_supplier" id="btnUpdate" class="btn btn-action w-100 py-2 d-none">Update Supplier Details</button>
                    <button type="submit" name="save_supplier" id="btnSave" class="btn btn-action w-100 py-2">Save Supplier to Directory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sModal = new bootstrap.Modal(document.getElementById('supplierModal'));

    // Search function
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#suppliersTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(term) ? "" : "none";
        });
    });

    function prepareAdd() {
        document.getElementById('modalTitle').innerText = "Add New Supplier";
        document.getElementById('supplier_id').value = "";
        document.querySelector('form').reset();
        document.getElementById('btnSave').classList.remove('d-none');
        document.getElementById('btnUpdate').classList.add('d-none');
        sModal.show();
    }

    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = "Edit Supplier";
            const d = this.dataset;
            document.getElementById('supplier_id').value = d.id;
            document.getElementById('name').value = d.name;
            document.getElementById('contact').value = d.contact;
            document.getElementById('phone').value = d.phone;
            document.getElementById('email').value = d.email;
            document.getElementById('items').value = d.items;
            document.getElementById('address').value = d.address;
            document.getElementById('btnSave').classList.add('d-none');
            document.getElementById('btnUpdate').classList.remove('d-none');
            sModal.show();
        });
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Delete Record?',
            text: "Remove " + name + " from suppliers?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "suppliers.php?delete=" + id;
        })
    }
</script>
</body>
</html>