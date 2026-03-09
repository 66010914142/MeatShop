<?php 
// 1. จัดการเรื่อง Session และเชื่อมต่อไฟล์ (ไฟล์นี้อยู่ที่ Root ไม่ต้องมี ../)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ("partials/header.php"); 
include_once("config/connectdb.php"); 
?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>กลับไปหน้าสินค้า
        </a>
    </div>

    <?php
    if(isset($_GET['id']) && $_GET['id'] != "") {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        
        // ใช้ชื่อตาราง products (ตัวเล็ก) ตามฐานข้อมูลจริง
        $sql = "SELECT * FROM `products` WHERE `P_id` = '$id'"; 
        $rs = mysqli_query($conn, $sql);
        
        if($rs && mysqli_num_rows($rs) > 0) {
            $data = mysqli_fetch_assoc($rs);
    ?>
            <div class="row bg-white p-4 rounded-4 shadow-sm border">
                <div class="col-md-6 mb-4 mb-md-0 text-center">
                    <img src="img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" 
                         class="img-fluid rounded-3 shadow" 
                         style="max-height: 450px; object-fit: contain;"
                         onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
                </div>
                
                <div class="col-md-6 ps-md-5">
                    <h1 class="fw-bold text-dark mb-2"><?= htmlspecialchars($data['P_name']) ?></h1>
                    <div class="h2 text-danger fw-bold my-4">฿<?= number_format($data['P_price'], 2) ?></div>
                    
                    <div class="p-3 bg-light rounded-3 mb-4 border-start border-4 border-warning">
                        <h6 class="fw-bold">รายละเอียดสินค้า:</h6>
                        <p class="text-muted mb-0">
                            <?= !empty($data['p_description']) ? nl2br(htmlspecialchars($data['p_description'])) : 'ไม่มีข้อมูลรายละเอียดสินค้า' ?>
                        </p>
                    </div>

                    <form action="cart.php" method="POST">
                        <input type="hidden" name="P_id" value="<?= $data['P_id'] ?>">
                        <div class="mb-4">
                            <label class="form-label fw-bold">เลือกจำนวน</label>
                            <div class="input-group shadow-sm" style="width: 140px;">
                                <button type="button" class="btn btn-dark" onclick="stepDown()">-</button>
                                <input type="number" id="qty" name="quantity" value="1" min="1" 
                                       max="<?= $data['P_amonut'] ?>" class="form-control text-center fw-bold">
                                <button type="button" class="btn btn-dark" onclick="stepUp()">+</button>
                            </div>
                            <small class="text-muted mt-2 d-block">คงเหลือในสต็อก: <?= $data['P_amonut'] ?> ชิ้น</small>
                        </div>
                        <div class="d-grid gap-2">
<form action="add_to_cart.php" method="GET">
    <input type="hidden" name="id" value="<?= $data['P_id'] ?>">
    
    <div class="mb-4">
        <label class="form-label fw-bold">เลือกจำนวน</label>
        <div class="input-group shadow-sm" style="width: 140px;">
            <button type="button" class="btn btn-dark" onclick="stepDown()">-</button>
            <input type="number" id="qty" name="qty" value="1" min="1" 
                   max="<?= $data['P_amonut'] ?>" class="form-control text-center fw-bold">
            <button type="button" class="btn btn-dark" onclick="stepUp()">+</button>
        </div>
        <small class="text-muted mt-2 d-block">คงเหลือในสต็อก: <?= $data['P_amonut'] ?> ชิ้น</small>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-lg btn-success shadow-sm">
            <i class="fa-solid fa-cart-plus me-2"></i>เพิ่มลงตะกร้าสินค้า
        </button>
    </div>
</form>
                        </div>
                    </form>
                </div>
            </div>
    <?php
        } else {
            echo "<div class='alert alert-danger text-center p-5'>ไม่พบข้อมูลสินค้านี้ในระบบ</div>";
        }
    }
    ?>
</div>

<script>
function stepUp() {
    var input = document.getElementById('qty');
    var max = parseInt(input.max);
    if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
}
function stepDown() {
    var input = document.getElementById('qty');
    if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
}
</script>
