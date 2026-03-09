<?php
    include_once("check_login.php");
    include_once("connectdb.php");

    $selected_data = null;

    // 1. ตรวจสอบเมื่อมีการเลือกรายชื่อลูกค้าจาก Drop-down
    if (isset($_GET['select_id'])) {
        $select_id = mysqli_real_escape_string($conn, $_GET['select_id']);
        $res = mysqli_query($conn, "SELECT * FROM user_login WHERE u_id = '$select_id'");
        $selected_data = mysqli_fetch_array($res);
    }

    // 2. เมื่อมีการกดปุ่ม "บันทึกการแก้ไข"
    if (isset($_POST['update_customer'])) {
        $u_id    = mysqli_real_escape_string($conn, $_POST['u_id']);
        $u_name  = mysqli_real_escape_string($conn, $_POST['u_name']);
        $u_phone = mysqli_real_escape_string($conn, $_POST['u_phone']);
        $u_add   = mysqli_real_escape_string($conn, $_POST['u_add']);

        // อัปเดตข้อมูลในตาราง user_login (ไม่อัปเดตอีเมลเพื่อป้องกันความซ้ำซ้อน)
        $sql = "UPDATE user_login SET 
                u_name = '$u_name', 
                u_phone = '$u_phone', 
                u_add = '$u_add' 
                WHERE u_id = '$u_id'";
        
        if (mysqli_query($conn, $sql)) {
            $status = "success"; 
            $msg = "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว!";
        } else {
            $status = "error"; 
            $msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }

    // ดึงรายชื่อลูกค้าทั้งหมดมาใส่ใน Drop-down
    $all_customers = mysqli_query($conn, "SELECT u_id, u_name FROM user_login ORDER BY u_name ASC");
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>แก้ไขข้อมูลลูกค้า - MEAT SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 5px solid #ffc107; }
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
                <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link active"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                </a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold text-dark"><i class="fa-solid fa-user-pen text-warning me-2"></i>แก้ไขข้อมูลลูกค้า</h2>
                <a href="customers.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i>ย้อนกลับ
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card mb-4 border-0 shadow-sm p-4 rounded-4">
                        <label class="form-label fw-bold"><i class="fa-solid fa-search me-2"></i>เลือกรายชื่อลูกค้าที่ต้องการแก้ไข</label>
                        <select class="form-select form-select-lg rounded-3" onchange="location.href='?select_id=' + this.value">
                            <option value="">-- ค้นหารายชื่อลูกค้า --</option>
                            <?php while($c = mysqli_fetch_array($all_customers)): ?>
                                <option value="<?php echo $c['u_id']; ?>" <?php echo (isset($select_id) && $select_id == $c['u_id']) ? 'selected' : ''; ?>>
                                    <?php echo $c['u_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="card form-card p-4 p-md-5 bg-white <?php echo (!$selected_data) ? 'opacity-50' : ''; ?>">
                        <form action="" method="POST">
                            <input type="hidden" name="u_id" value="<?php echo $selected_data['u_id'] ?? ''; ?>">

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold label-req">ชื่อ-นามสกุล</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" name="u_name" class="form-control" value="<?php echo $selected_data['u_name'] ?? ''; ?>" required <?php echo (!$selected_data) ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">อีเมล (ไม่สามารถแก้ไขได้)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" class="form-control bg-light" value="<?php echo $selected_data['u_email'] ?? ''; ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold label-req">เบอร์โทรศัพท์</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" name="u_phone" class="form-control" value="<?php echo $selected_data['u_phone'] ?? ''; ?>" required <?php echo (!$selected_data) ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold label-req">ที่อยู่ลูกค้า</label>
                                    <textarea name="u_add" class="form-control" rows="4" required <?php echo (!$selected_data) ? 'disabled' : ''; ?>><?php echo $selected_data['u_add'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <?php if($selected_data): ?>
                            <div class="d-flex gap-3 mt-2">
                                <button type="submit" name="update_customer" class="btn btn-warning py-3 flex-grow-1 rounded-3 shadow fw-bold text-dark">
                                    <i class="fa-solid fa-save me-2"></i>บันทึกการแก้ไขข้อมูล
                                </button>
                                <a href="customers.php" class="btn btn-light py-3 border px-4 rounded-3">ยกเลิก</a>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info text-center">กรุณาเลือกรายชื่อลูกค้าจากด้านบนเพื่อเริ่มการแก้ไข</div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if(isset($status)): ?>
    Swal.fire({
        title: '<?php echo ($status == "success") ? "สำเร็จ!" : "ผิดพลาด!"; ?>',
        text: '<?php echo $msg; ?>',
        icon: '<?php echo $status; ?>',
        confirmButtonColor: '#0d6efd'
    }).then(() => {
        <?php if($status == "success") echo "window.location.href='customers.php';"; ?>
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