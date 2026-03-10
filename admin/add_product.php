<?php
    // เริ่มต้น Session และเชื่อมต่อฐานข้อมูล
    include_once("check_login.php");
    include_once("connectdb.php");

    // 1. ดึงประเภทสินค้าจากตาราง categories มาใส่ใน Dropdown
    $sql_cat = "SELECT * FROM categories ORDER BY c_name_th ASC";
    $res_cat = mysqli_query($conn, $sql_cat);

    // 2. เมื่อมีการกดปุ่ม "บันทึกข้อมูลสินค้า"
    if (isset($_POST['save_product'])) {
        $p_id = mysqli_real_escape_string($conn, $_POST['p_id']);
        $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
        $p_description = mysqli_real_escape_string($conn, $_POST['p_description']);
        $p_amonut = (int)$_POST['p_amonut'];
        $p_price = (int)$_POST['p_price'];
        $c_id = mysqli_real_escape_string($conn, $_POST['C_id']);

        // --- ส่วนจัดการรูปภาพ: ดึงนามสกุลและบันทึกไฟล์ ---
        $ext = ""; // กำหนดค่าเริ่มต้นเป็นค่าว่าง
        if($_FILES['p_img']['name'] != "") {
            // ดึงนามสกุลไฟล์ เช่น jpg, png
            $ext = strtolower(pathinfo($_FILES['p_img']['name'], PATHINFO_EXTENSION));
            
            // ตรวจสอบ/สร้างโฟลเดอร์ img
            if (!is_dir("img")) { mkdir("img", 0777, true); }
            
            // ใช้ copy ย้ายไฟล์ไปที่โฟลเดอร์ โดยตั้งชื่อไฟล์เป็น [รหัสสินค้า].[นามสกุล]
            copy($_FILES['p_img']['tmp_name'], "img/" . $p_id . "." . $ext);
        }

        // --- ตรวจสอบและบันทึกลง Database ---
        $check = mysqli_query($conn, "SELECT P_id FROM products WHERE P_id = '$p_id'");
        
        if (mysqli_num_rows($check) > 0) {
            // กรณีอัปเดต: ถ้ามีการเลือกรูปใหม่ ($ext ไม่ว่าง) ให้แก้ไขฟิลด์ P_img ด้วย
            $update_img_sql = ($ext != "") ? ", P_img = '$ext'" : "";
            $sql = "UPDATE products SET 
                    P_name = '$p_name', 
                    p_description = '$p_description', 
                    P_amonut = '$p_amonut', 
                    P_price = '$p_price', 
                    C_id = '$c_id' 
                    $update_img_sql 
                    WHERE P_id = '$p_id'";
            $msg_text = "อัปเดตข้อมูลสินค้าเรียบร้อย!";
        } else {
            // กรณีเพิ่มใหม่: ใส่ $ext (เช่น 'jpg') ลงในฟิลด์ P_img โดยตรง
            $sql = "INSERT INTO products (P_id, P_name, p_description, P_amonut, P_price, P_img, C_id) 
                    VALUES ('$p_id', '$p_name', '$p_description', '$p_amonut', '$p_price', '$ext', '$c_id')";
            $msg_text = "บันทึกสินค้าใหม่เรียบร้อย!";
        }

        if (mysqli_query($conn, $sql)) {
            $status = "success"; 
            $msg = $msg_text;
        } else {
            $status = "error"; 
            $msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>จัดการข้อมูลสินค้า - MEAT SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 5px solid #0d6efd; }
        .preview-img { max-width: 200px; border-radius: 12px; display: none; margin-top: 15px; border: 3px solid #0d6efd; padding: 5px; background: white; }
        .text-blue-theme { color: #0d6efd !important; }
        .label-req::after { content: " *"; color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-0 shadow">
            <div class="p-4 text-center fw-bold border-bottom border-secondary">
                <i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP
            </div>
            <div class="nav flex-column nav-pills mt-2">
                <a href="index2.php" class="nav-link"><i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน</a>
                <a href="categories.php" class="nav-link"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link active"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                </a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-blue-theme me-2"></i>กรอกข้อมูลสินค้าให้ครบถ้วน</h2>
                <a href="products.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i>ย้อนกลับ
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card form-card p-4 p-md-5 bg-white">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold label-req">รหัสสินค้า</label>
                                    <input type="text" name="p_id" class="form-control" placeholder="ระบุรหัสสินค้า (เช่น P001)" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold label-req">ชื่อสินค้า</label>
                                    <input type="text" name="p_name" class="form-control" placeholder="ระบุชื่อสินค้า" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold label-req">ประเภทสินค้า</label>
                                    <select name="C_id" class="form-select" required>
                                        <option value="">--- กรุณาเลือกประเภท ---</option>
                                        <?php while($cat = mysqli_fetch_array($res_cat)) { ?>
                                            <option value="<?php echo $cat['C_id']; ?>">
                                                <?php echo $cat['C_id'] . " - " . $cat['c_name_th']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold label-req">ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" placeholder="0.00" step="0.01" required min="0.01">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold label-req">จำนวนสต็อก</label>
                                    <input type="number" name="p_amonut" class="form-control" placeholder="0" required min="1">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold label-req">รายละเอียดสินค้า</label>
                                    <textarea name="p_description" class="form-control" rows="4" placeholder="ระบุสรรพคุณ ขนาด หรือรายละเอียดอื่นๆ" required></textarea>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold label-req">รูปภาพสินค้า</label>
                                    <input type="file" name="p_img" class="form-control" accept="image/*" onchange="previewImage(this)" required>
                                    <div class="text-center">
                                        <img id="img-preview" class="preview-img mx-auto">
                                    </div>
                                    <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info"></i> แนะนำไฟล์ .jpg, .png ขนาดไม่เกิน 2MB</small>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-2">
                                <button type="submit" name="save_product" class="btn btn-primary py-3 flex-grow-1 rounded-3 shadow fw-bold">
                                    <i class="fa-solid fa-save me-2"></i>ยืนยันบันทึกข้อมูลสินค้า
                                </button>
                                <button type="reset" class="btn btn-light py-3 border px-4 rounded-3" onclick="document.getElementById('img-preview').style.display='none'">ล้างค่า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage(input) {
        const preview = document.getElementById('img-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    <?php if(isset($status)): ?>
    Swal.fire({
        title: '<?php echo ($status == "success") ? "ดำเนินการสำเร็จ!" : "เกิดข้อผิดพลาด!"; ?>',
        text: '<?php echo $msg; ?>',
        icon: '<?php echo $status; ?>',
        confirmButtonColor: '#0d6efd'
    }).then(() => {
        <?php if($status == "success") echo "window.location.href='products.php';"; ?>
    });
    <?php endif; ?>

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ยืนยันออกจากระบบ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ออกจากระบบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = 'logout.php'; }
        });
    }
</script>
</body>
</html>
