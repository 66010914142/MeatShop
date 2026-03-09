<?php
include_once("check_login.php");
include_once("connectdb.php");

// --- 1. Logic การลบข้อมูล (AJAX GET) ---
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    // ตรวจสอบชื่อตารางและคอลัมน์ตามรูปภาพ
    $sql_delete = "DELETE FROM categories WHERE C_id = '$id'"; 
    
    if (mysqli_query($conn, $sql_delete)) {
        echo "success";
    } else {
        echo "ไม่สามารถลบได้: ข้อมูลนี้อาจถูกใช้งานอยู่ในตารางอื่น (เช่น ตารางสินค้า)";
    }
    exit; 
}

// --- 2. Logic การอัปเดตข้อมูล (AJAX POST) ---
if (isset($_POST['action']) && $_POST['action'] == 'update_category') {
    $old_id = mysqli_real_escape_string($conn, $_POST['old_c_id']); 
    $new_id = mysqli_real_escape_string($conn, $_POST['c_id']);     
    $c_en   = mysqli_real_escape_string($conn, $_POST['c_name_eng']);
    $c_th   = mysqli_real_escape_string($conn, $_POST['c_name_th']);
    
    // อัปเดตข้อมูลตามชื่อคอลัมน์ C_id, c_name_eng, c_name_th
    $sql_update = "UPDATE categories SET 
                    C_id       = '$new_id',
                    c_name_eng = '$c_en', 
                    c_name_th  = '$c_th' 
                  WHERE C_id = '$old_id'";
                  
    echo (mysqli_query($conn, $sql_update)) ? "success" : "error";
    exit; 
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการประเภทสินค้า - Meat Shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; padding-left: 25px; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 20px; }
        .card { border-radius: 15px; border: none; }
        .id-badge { min-width: 65px; padding: 6px 12px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; text-align: center; display: inline-block; }
        .bg-soft-blue { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .btn-action { background-color: #fff; border: 1px solid #eee; transition: 0.2s; border-radius: 8px; }
        .btn-action:hover { background-color: #f8f9fa; transform: translateY(-1px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-0 shadow">
            <div class="p-4 text-center fw-bold border-bottom border-secondary"><i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP</div>
            <div class="nav flex-column nav-pills mt-2">
                <a href="index2.php" class="nav-link"><i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน</a>
                <a href="categories.php" class="nav-link active"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h2 class="fw-bold"><i class="fa-solid fa-layer-group me-2 text-primary"></i> จัดการประเภทสินค้า</h2>
                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline text-muted">สวัสดีคุณ: <strong><?php echo $_SESSION['a.name']; ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['a.name']; ?>&background=0D6EFD&color=fff" alt="User" class="rounded-circle" width="35">
                </div>
            </div>

            <div class="card shadow-sm overflow-hidden"> 
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-4">
                        <h4 class="fw-bold mb-0">รายการประเภทสินค้า</h4>
                        <a href="add_categories.php" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fa-solid fa-plus-circle me-1"></i> เพิ่มประเภทสินค้า</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center"> 
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">รหัสประเภท</th>
                                    <th class="text-start">ชื่อประเภท (English)</th>
                                    <th class="text-start">ชื่อประเภท (ภาษาไทย)</th>
                                    <th width="15%">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM categories ORDER BY C_id ASC";
                                $rs = mysqli_query($conn, $sql);
                                if ($rs && mysqli_num_rows($rs) > 0):
                                    while ($data = mysqli_fetch_assoc($rs)):
                                ?>
                                <tr>
                                    <td><span class="id-badge bg-soft-blue"><?php echo $data['C_id']; ?></span></td>
                                    <td class="text-start fw-bold"><?php echo htmlspecialchars($data['c_name_eng']); ?></td>
                                    <td class="text-start text-secondary"><?php echo htmlspecialchars($data['c_name_th']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-action text-warning me-1" 
                                                onclick="openEditModal('<?php echo $data['C_id']; ?>', '<?php echo addslashes($data['c_name_eng']); ?>', '<?php echo addslashes($data['c_name_th']); ?>')">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-action text-danger" 
                                           onclick="confirmDelete(event, '<?php echo $data['C_id']; ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="4" class="py-5 text-muted">ไม่พบข้อมูลประเภทสินค้า</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>แก้ไขประเภทสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="old_c_id" name="old_c_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัสประเภทสินค้า (C_id)</label>
                        <input type="text" class="form-control rounded-3 bg-light" id="edit_c_id" name="c_id" required>
                        <small class="text-muted">เช่น dc06, fc03 เป็นต้น</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อประเภท (English)</label>
                        <input type="text" class="form-control rounded-3" id="edit_c_name_eng" name="c_name_eng" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อประเภท (ภาษาไทย)</label>
                        <input type="text" class="form-control rounded-3" id="edit_c_name_th" name="c_name_th" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditModal(id, nameEn, nameTh) {
        document.getElementById('old_C_id').value = id;      
        document.getElementById('edit_C_id').value = id;     
        document.getElementById('edit_c_name_eng').value = nameEn;
        document.getElementById('edit_c_name_th').value = nameTh;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update_category');
        
        fetch('categories.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-4' } }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถบันทึกได้' });
            }
        });
    };

    function confirmDelete(event, id) {
        event.preventDefault();
        Swal.fire({
            title: 'ยืนยันการลบ?', 
            text: "คุณต้องการลบข้อมูลรหัส " + id + " ใช่หรือไม่?",
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonText: 'ลบข้อมูล', 
            cancelButtonText: 'ยกเลิก',
            customClass: { confirmButton: 'btn btn-danger rounded-pill px-4 me-2', cancelButton: 'btn btn-light rounded-pill px-4' },
            buttonsStyling: false
        }).then((result) => { 
            if (result.isConfirmed) { 
                fetch('categories.php?delete_id=' + encodeURIComponent(id))
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === "success") {
                        Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', text: 'ข้อมูลถูกลบเรียบร้อยแล้ว', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-4' } }).then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'ไม่สามารถลบได้', text: data });
                    }
                });
            } 
        });
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบหรือไม่?', icon: 'question', showCancelButton: true, confirmButtonText: 'ยืนยัน', cancelButtonText: 'ยกเลิก',
            customClass: { confirmButton: 'btn btn-primary rounded-pill px-4 me-2', cancelButton: 'btn btn-light rounded-pill px-4' },
            buttonsStyling: false 
        }).then((result) => { if (result.isConfirmed) { window.location.href = 'logout.php'; } });
    }
</script>
</body>
</html>