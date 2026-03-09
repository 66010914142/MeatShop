<?php
    include_once("check_login.php");
    include_once("connectdb.php");

    // --- ส่วนดึงข้อมูล ID เดิมมาแสดงในรายการตัวเลือก ---
    $sql_list = "SELECT C_id, c_name_th FROM categories ORDER BY C_id ASC";
    $res_list = mysqli_query($conn, $sql_list);
    // -------------------------------------------

    if (isset($_POST['save_category'])) {
        $c_id = mysqli_real_escape_string($conn, $_POST['C_id']);
        $c_name_eng = mysqli_real_escape_string($conn, $_POST['c_name_eng']);
        $c_name_th = mysqli_real_escape_string($conn, $_POST['c_name_th']);

        // ตรวจสอบว่ามี ID นี้อยู่แล้วหรือไม่
        $check_sql = "SELECT C_id FROM categories WHERE C_id = '$c_id'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $sql = "UPDATE categories SET c_name_eng = '$c_name_eng', c_name_th = '$c_name_th' WHERE C_id = '$c_id'";
            $msg_text = "อัปเดตข้อมูลประเภทสินค้าเดิมเรียบร้อยแล้ว";
        } else {
            $sql = "INSERT INTO categories (C_id, c_name_eng, c_name_th) VALUES ('$c_id', '$c_name_eng', '$c_name_th')";
            $msg_text = "เพิ่มประเภทสินค้าใหม่เรียบร้อยแล้ว";
        }

        if (mysqli_query($conn, $sql)) {
            $status = "success";
            $msg = $msg_text;
        } else {
            $status = "error";
            $msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
    $current_page = 'categories.php';
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการประเภทสินค้า - MEAT SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 25px; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 5px solid #0d6efd; }
        .info-badge { font-size: 0.8rem; background: #e9ecef; color: #495057; padding: 5px 12px; border-radius: 50px; }
        .text-blue-theme { color: #0d6efd !important; }
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
                <a href="categories.php" class="nav-link active"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                </a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold mb-0 text-dark">จัดการประเภทสินค้า</h2>
                    <span class="info-badge mt-2 d-inline-block">พิมพ์รหัสใหม่ หรือเลือกจากรายการที่มีอยู่ด้านล่าง</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline text-muted">สวัสดีคุณ: <strong><?php echo $_SESSION['a.name']; ?></strong></span>
                    <a href="categories.php" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa-solid fa-arrow-left me-2"></i>ย้อนกลับ
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card form-card bg-white p-4 p-md-5">
                        <form action="" method="POST">
                            
                            <div class="mb-4">
                                <label for="C_id" class="form-label fw-bold">รหัสประเภทสินค้า <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg" list="datalistOptions" id="C_id" name="C_id" placeholder="พิมพ์รหัสใหม่ หรือดับเบิลคลิกเพื่อเลือก..." required>
                                <datalist id="datalistOptions">
                                    <?php 
                                    // รีเซ็ต pointer ของ result set เพื่อวนลูปใหม่
                                    mysqli_data_seek($res_list, 0); 
                                    while($row = mysqli_fetch_array($res_list)) { ?>
                                        <option value="<?php echo $row['C_id']; ?>">
                                            <?php echo $row['c_name_th']; ?>
                                        </option>
                                    <?php } ?>
                                </datalist>
                                <div class="form-text text-primary small mt-2">
                                    <i class="fa-solid fa-circle-info me-1"></i> หากเลือก ID เดิม ระบบจะทำการอัปเดตชื่อข้อมูลนั้น
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="c_name_eng" class="form-label fw-bold">ชื่อภาษาอังกฤษ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" id="c_name_eng" name="c_name_eng" placeholder="เช่น Beef" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="c_name_th" class="form-label fw-bold">ชื่อภาษาไทย <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" id="c_name_th" name="c_name_th" placeholder="เช่น เนื้อวัว" required>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-3">
                                <button type="submit" name="save_category" class="btn btn-primary py-3 flex-grow-1 rounded-3 shadow fw-bold">
                                    <i class="fa-solid fa-save me-2"></i>ยืนยันการบันทึกข้อมูล
                                </button>
                                <button type="reset" class="btn btn-light py-3 border px-4 rounded-3">ล้างค่า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // แจ้งเตือนเมื่อบันทึกข้อมูล
    <?php if(isset($status)): ?>
    Swal.fire({
        title: '<?php echo ($status == "success") ? "สำเร็จ!" : "แจ้งเตือน!"; ?>',
        text: '<?php echo $msg; ?>',
        icon: '<?php echo $status; ?>',
        confirmButtonColor: '#0d6efd'
    }).then(() => {
        <?php if($status == "success") echo "window.location.href='categories.php';"; ?>
    });
    <?php endif; ?>

    // ฟังก์ชันยืนยันการออกจากระบบ
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบหรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) { 
                window.location.href = 'logout.php'; 
            }
        });
    }
</script>
</body>
</html>