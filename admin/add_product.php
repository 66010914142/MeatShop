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
        $p_price = (float)$_POST['p_price']; // ใช้ float สำหรับราคาที่มีทศนิยม
        $c_id = mysqli_real_escape_string($conn, $_POST['C_id']);

        // --- ส่วนจัดการเรื่องรูปภาพ ---
        $p_img_db = ""; // ตัวแปรสำหรับเก็บนามสกุลลง Database
        if($_FILES['p_img']['name'] != "") {
            // 1. ดึงนามสกุลไฟล์ออกมา และแปลงเป็นตัวพิมพ์เล็ก (เช่น jpg, png)
            $ext = strtolower(pathinfo($_FILES['p_img']['name'], PATHINFO_EXTENSION));
            
            // 2. ตั้งชื่อไฟล์จริงบน Server ตามรหัสสินค้า (เช่น DS001.jpg)
            $file_name_to_save = $p_id . "." . $ext;
            
            // 3. กำหนด Folder ปลายทาง (ใช้ 'img' ให้ตรงกับหน้า index)
            $target_dir = "img/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
            
            // 4. อัปโหลดไฟล์ไปที่ Server
            if(move_uploaded_file($_FILES['p_img']['tmp_name'], $target_dir . $file_name_to_save)) {
                // 5. บันทึก "เฉพาะนามสกุล" เพื่อลง Database
                $p_img_db = $ext; 
            }
        }

        // --- ตรวจสอบและบันทึกลง Database ---
        $check = mysqli_query($conn, "SELECT P_id FROM products WHERE P_id = '$p_id'");
        
        if (mysqli_num_rows($check) > 0) {
            // กรณีอัปเดต: ถ้ามีการเลือกรูปใหม่ ให้แก้เฉพาะคอลัมน์ P_img
            $update_img_sql = ($p_img_db != "") ? ", P_img = '$p_img_db'" : "";
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
            // กรณีเพิ่มใหม่: บันทึกข้อมูลทั้งหมดรวมถึงนามสกุลรูปภาพ
            $sql = "INSERT INTO products (P_id, P_name, p_description, P_amonut, P_price, P_img, C_id) 
                    VALUES ('$p_id', '$p_name', '$p_description', '$p_amonut', '$p_price', '$p_img_db', '$c_id')";
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
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 5px solid #0d6efd; }
        .preview-img { max-width: 200px; border-radius: 12px; display: none; margin-top: 15px; border: 3px solid #0d6efd; padding: 5px; background: white; }
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
                <a href="products.php" class="nav-link active"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="logout.php" class="nav-link text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">เพิ่ม/แก้ไข ข้อมูลสินค้า</h2>
            </div>

            <div class="card form-card p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold label-req">รหัสสินค้า (P_id)</label>
                            <input type="text" name="p_id" class="form-control" placeholder="เช่น DS001" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold label-req">ชื่อสินค้า</label>
                            <input type="text" name="p_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold label-req">ประเภทสินค้า</label>
                            <select name="C_id" class="form-select" required>
                                <option value="">เลือกประเภท</option>
                                <?php while($cat = mysqli_fetch_array($res_cat)) { ?>
                                    <option value="<?= $cat['C_id'] ?>"><?= $cat['c_name_th'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold label-req">ราคา</label>
                            <input type="number" name="p_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold label-req">สต็อก</label>
                            <input type="number" name="p_amonut" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">รายละเอียด</label>
                            <textarea name="p_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold label-req">รูปภาพสินค้า</label>
                            <input type="file" name="p_img" class="form-control" accept="image/*" onchange="previewImage(this)" required>
                            <img id="img-preview" class="preview-img">
                        </div>
                    </div>
                    <button type="submit" name="save_product" class="btn btn-primary w-100 py-2 fw-bold">บันทึกข้อมูล</button>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('img-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    <?php if(isset($status)): ?>
    Swal.fire({
        title: '<?= ($status == "success") ? "สำเร็จ" : "พลาด" ?>',
        text: '<?= $msg ?>',
        icon: '<?= $status ?>'
    }).then(() => {
        <?php if($status == "success") echo "window.location.href='products.php';"; ?>
    });
    <?php endif; ?>
</script>
</body>
</html>
