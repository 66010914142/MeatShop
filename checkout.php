<?php
session_start();
include_once("config/connectdb.php");

// 1. ตรวจสอบ Login
if (!isset($_SESSION['u_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['u_id'];

// 2. ดึงข้อมูลมาเช็คที่อยู่ซ้ำเพื่อความปลอดภัย
$query = mysqli_query($conn, "SELECT u_add, u_phone FROM user_login WHERE u_id = '$u_id'");
$user = mysqli_fetch_array($query);

if (empty($user['u_add']) || empty($user['u_phone']) || $user['u_add'] == "") {
    echo "<script>alert('ข้อมูลที่อยู่ไม่สมบูรณ์ กรุณาแก้ไขข้อมูลก่อนสั่งซื้อ'); window.location.href='profile.php';</script>";
    exit();
}

// 3. เช็คว่ามีสินค้าในตะกร้าไหม ถ้าไม่มีให้เด้งกลับ
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

include_once("partials/header.php");
$total_price = 0;
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-7">
            <h2 class="fw-bold mb-4"><i class="fa-solid fa-receipt me-2 text-primary"></i>สรุปรายการสั่งซื้อ</h2>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">สินค้า</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-end p-3">รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($_SESSION['cart'] as $p_id => $qty) {
                                $sql = "SELECT * FROM products WHERE P_id = '$p_id'";
                                $rs = mysqli_query($conn, $sql);
                                if ($data = mysqli_fetch_array($rs)) {
                                    $sum = $data['P_price'] * $qty;
                                    $total_price += $sum;
                            ?>
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="img/<?= $data['P_id'] ?>.jpg" width="50" class="rounded me-3" onerror="this.src='https://via.placeholder.com/50'">
                                        <span class="small fw-bold"><?= htmlspecialchars($data['P_name']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center"><?= $qty ?></td>
                                <td class="text-end p-3">฿<?= number_format($sum, 2) ?></td>
                            </tr>
                            <?php 
                                }
                            } 
                            ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold p-3">ยอดชำระเงินทั้งสิ้น:</td>
                                <td class="text-end fw-bold text-danger h4 p-3">฿<?= number_format($total_price, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <h2 class="fw-bold mb-4"><i class="fa-solid fa-money-bill-transfer me-2 text-success"></i>ชำระเงิน</h2>
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="alert alert-info border-0 rounded-3">
                    <p class="mb-1"><strong>ช่องทางชำระเงิน:</strong> ธนาคารกสิกรไทย</p>
                    <p class="mb-1"><strong>เลขที่บัญชี:</strong> 123-4-56789-0</p>
                    <p class="mb-0"><strong>ชื่อบัญชี:</strong> ร้าน MeatShop มมส.</p>
                </div>

                <form action="confirm_order.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label fw-bold">แนบสลิปการโอนเงิน</label>
                        <input type="file" name="slip_img" class="form-control" accept="image/*" required>
                        <div class="form-text text-muted mt-2 small">
                            *กรุณาตรวจสอบยอดเงินให้ถูกต้อง (฿<?= number_format($total_price, 2) ?>)
                        </div>
                    </div>
                    
                    <button type="submit" name="btn_confirm" class="btn btn-success w-full py-3 fw-bold rounded-3 shadow-sm">
                        ยืนยันการสั่งซื้อและส่งสลิป
                    </button>
                    <a href="cart.php" class="btn btn-link text-muted w-full mt-2 text-decoration-none small">
                        กลับไปแก้ไขตะกร้า
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once("partials/footer.php"); ?>
