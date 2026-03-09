<?php
include_once("check_login.php");
include_once("connectdb.php");

// --- 1. Logic การอัปเดตข้อมูล (AJAX POST) ---
if (isset($_POST['action']) && $_POST['action'] == 'update_product') {
    $id    = mysqli_real_escape_string($conn, $_POST['p_id']);
    $name  = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = mysqli_real_escape_string($conn, $_POST['p_price']);
    $desc  = mysqli_real_escape_string($conn, $_POST['p_description']);
    $amount= mysqli_real_escape_string($conn, $_POST['p_amount']);
    $cat_id= mysqli_real_escape_string($conn, $_POST['c_id']); 
    
    if (isset($_FILES['p_image']) && $_FILES['p_image']['error'] == 0) {
        $allowed  = array("jpg", "jpeg", "png", "webp");
        $filename = $_FILES["p_image"]["name"];
        $ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = $id . ".jpg"; 
            $path     = "../img/" . $new_name; //
            if(file_exists($path)) { @unlink($path); }
            move_uploaded_file($_FILES["p_image"]["tmp_name"], $path);
        }
    }
    
    $sql_update = "UPDATE products SET 
                    P_name        = '$name', 
                    P_price       = '$price', 
                    p_description = '$desc', 
                    P_amonut      = '$amount',
                    C_id          = '$cat_id' 
                  WHERE P_id = '$id'";
                  
    echo (mysqli_query($conn, $sql_update)) ? "success" : "error";
    exit; 
}

// --- 2. รับค่าค้นหาและหมวดหมู่ ---
$filter_cat = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, $_GET['cat_id']) : '';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : ''; // รับค่าค้นหา

$sql_categories = "SELECT * FROM categories ORDER BY c_name_th ASC";
$res_categories = mysqli_query($conn, $sql_categories);
$categories_array = [];
while($row = mysqli_fetch_array($res_categories)) {
    $categories_array[] = $row;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสินค้า - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 20px; }
        .product-img { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd; padding: 2px; background: #fff; cursor: pointer; transition: 0.2s; }
        .product-img:hover { transform: scale(1.1); border: 2px solid #0d6efd !important; }
        .id-badge-blue { min-width: 65px; padding: 6px 12px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; text-align: center; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .btn-action { background-color: #fff; border: 1px solid #eee; transition: 0.2s; }
        .btn-action:hover { background-color: #f8f9fa; transform: translateY(-1px); }
        .search-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        #preview_img { width: 120px; height: 120px; object-fit: cover; border-radius: 15px; border: 2px solid #ffc107; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0 shadow">
            <div class="p-4 text-center fw-bold border-bottom border-secondary"><i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP</div>
            <div class="nav flex-column nav-pills mt-2">
                <a href="index2.php" class="nav-link"><i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน</a>
                <a href="categories.php" class="nav-link"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link active"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h2 class="fw-bold"><i class="fa-solid fa-users-gear me-2 text-primary"></i> จัดการสินค้า</h2>
            </div>

            <div class="search-card p-3 mb-4">
                <form action="products.php" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อสินค้า..." value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="cat_id" class="form-select">
                            <option value="">--- ทุกหมวดหมู่ ---</option>
                            <?php foreach($categories_array as $cat): ?>
                                <option value="<?php echo $cat['c_id']; ?>" <?php echo ($filter_cat == $cat['c_id']) ? 'selected' : ''; ?>>
                                    <?php echo $cat['c_name_th']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">ค้นหา</button>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <a href="add_product.php" class="btn btn-success rounded-pill px-4"><i class="fa-solid fa-plus-circle me-1"></i> เพิ่มสินค้า</a>
                    </div>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="12%">ID</th>
                                <th width="10%">รูปภาพ</th>
                                <th class="text-start">ข้อมูลสินค้า</th>
                                <th width="15%">ราคา</th>
                                <th width="12%">สต็อก</th>
                                <th width="15%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // สร้างเงื่อนไขการกรอง (WHERE Clause)
                            $conditions = [];
                            if ($filter_cat != "") { $conditions[] = "p.C_id = '$filter_cat'"; }
                            if ($search_query != "") { $conditions[] = "p.P_name LIKE '%$search_query%'"; }
                            
                            $where_sql = count($conditions) > 0 ? " WHERE " . implode(" AND ", $conditions) : "";
                            
                            $sql = "SELECT p.*, c.c_name_th FROM products p 
                                    LEFT JOIN categories c ON p.C_id = c.c_id 
                                    $where_sql ORDER BY p.P_id ASC";
                            $rs = mysqli_query($conn, $sql);
                            
                            if ($rs && mysqli_num_rows($rs) > 0):
                                while ($data = mysqli_fetch_array($rs)):
                                    $p_id = $data['P_id'];
                                    // Path รูปภาพที่ซันต้องการ
                                    $img_path = "../img/" . $p_id . ".jpg"; 
                                    $display_img = file_exists($img_path) ? $img_path."?t=".time() : "https://via.placeholder.com/60?text=No+Img";
                            ?>
                            <tr class="text-center">
                                <td><span class="id-badge-blue">#<?php echo $p_id; ?></span></td>
                                <td>
                                    <img src="<?php echo $display_img; ?>" class="product-img shadow-sm" 
                                         onclick="viewImage('<?php echo $display_img; ?>', '<?php echo addslashes($data['P_name']); ?>')">
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold"><?php echo $data['P_name']; ?></div>
                                    <span class="badge bg-light text-muted border">หมวด: <?php echo $data['c_name_th'] ?? 'ทั่วไป'; ?></span>
                                </td>
                                <td class="fw-bold text-primary">฿<?php echo number_format($data['P_price'], 2); ?></td>
                                <td class="fw-bold"><?php echo $data['P_amonut']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-action text-warning" 
                                            onclick="openEditModal('<?php echo $p_id; ?>', '<?php echo addslashes($data['P_name']); ?>', '<?php echo $data['P_price']; ?>', '<?php echo addslashes($data['p_description']); ?>', '<?php echo $data['P_amonut']; ?>', '<?php echo $data['C_id']; ?>', '<?php echo $display_img; ?>')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="#" class="btn btn-sm btn-action text-danger" onclick="confirmDelete(event, 'delete_products.php?id=<?php echo $p_id; ?>')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">ไม่พบข้อมูลสินค้าที่ค้นหา</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">แก้ไขข้อมูลสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_p_id" name="p_id">
                    <div class="text-center mb-3">
                        <img id="preview_img" src="">
                        <div>
                            <label for="p_image" class="btn btn-outline-warning btn-sm rounded-pill px-3"><i class="fa-solid fa-image me-1"></i> เปลี่ยนรูปภาพ</label>
                            <input type="file" id="p_image" name="p_image" class="d-none" accept="image/*" onchange="previewFile(this)">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ประเภทสินค้า</label>
                        <select class="form-select" id="edit_c_id" name="c_id" required>
                            <?php foreach($categories_array as $cat): ?>
                                <option value="<?php echo $cat['c_id']; ?>"><?php echo $cat['c_name_th']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อสินค้า</label>
                        <input type="text" class="form-control" id="edit_p_name" name="p_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รายละเอียด</label>
                        <textarea class="form-control" id="edit_p_description" name="p_description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label fw-semibold">ราคา (฿)</label><input type="number" class="form-control" id="edit_p_price" name="p_price" step="0.01" required></div>
                        <div class="col-6 mb-3"><label class="form-label fw-semibold">สต็อก</label><input type="number" class="form-control" id="edit_p_amount" name="p_amount" required></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditModal(id, name, price, desc, amount, cat_id, img) {
        document.getElementById('edit_p_id').value = id;
        document.getElementById('edit_p_name').value = name;
        document.getElementById('edit_p_price').value = price;
        document.getElementById('edit_p_description').value = desc;
        document.getElementById('edit_p_amount').value = amount;
        document.getElementById('edit_c_id').value = cat_id;
        document.getElementById('preview_img').src = img;
        document.getElementById('p_image').value = "";
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function previewFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('preview_img').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'update_product');
        fetch('products.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', showConfirmButton: false, timer: 1500 }).then(() => location.reload());
            } else { Swal.fire('Error', 'ไม่สามารถบันทึกได้', 'error'); }
        });
    };

    function confirmDelete(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'ยืนยันการลบสินค้า?', text: "ข้อมูลจะถูกลบอย่างถาวร!", icon: 'warning',
            showCancelButton: true, confirmButtonText: 'ลบข้อมูล', cancelButtonText: 'ยกเลิก',
            customClass: { confirmButton: 'btn btn-danger rounded-pill px-4 py-2 me-2', cancelButton: 'btn btn-light rounded-pill px-4 py-2' },
            buttonsStyling: false
        }).then((result) => { if (result.isConfirmed) { window.location.href = url; } });
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบหรือไม่?', icon: 'question', showCancelButton: true, confirmButtonText: 'ยืนยัน', cancelButtonText: 'ยกเลิก',
            customClass: { confirmButton: 'btn btn-primary rounded-pill px-4 py-2 me-2', cancelButton: 'btn btn-light rounded-pill px-4 py-2' },
            buttonsStyling: false 
        }).then((result) => { if (result.isConfirmed) { window.location.href = 'logout.php'; } })
    }

    function viewImage(imgUrl, productName) {
        Swal.fire({ title: productName, imageUrl: imgUrl, showConfirmButton: false, showCloseButton: true });
    }
</script>
</body>
</html>