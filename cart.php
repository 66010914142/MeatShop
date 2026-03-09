<?php
session_start();
include_once("config/connectdb.php");
include_once("partials/header.php");

// ตรวจสอบว่ามีตะกร้าหรือยัง
$total_price = 0;
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>ตะกร้าสินค้าของคุณ</h2>
    
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="p-3">สินค้า</th>
                        <th class="text-center p-3">ราคา/หน่วย</th>
                        <th class="text-center p-3">จำนวน</th>
                        <th class="text-center p-3">รวม</th>
                        <th class="text-center p-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($_SESSION['cart'])) {
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
                                <img src="img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" width="60" class="rounded shadow-sm me-3" onerror="this.src='https://via.placeholder.com/60'">
                                <span class="fw-bold text-dark"><?= htmlspecialchars($data['P_name']) ?></span>
                            </div>
                        </td>
                        <td class="text-center">฿<?= number_format($data['P_price'], 2) ?></td>
                        <td class="text-center"><?= $qty ?></td>
                        <td class="text-center fw-bold text-primary">฿<?= number_format($sum, 2) ?></td>
                        <td class="text-center">
                            <a href="cart_remove.php?id=<?= $p_id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบสินค้านี้ใช่ไหม?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                            }
                        }
                    } else { ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted h5">ไม่มีสินค้าในตะกร้า</td></tr>
                    <?php } ?>
                </tbody>
                <?php if ($total_price > 0): ?>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold p-3">ยอดรวมสุทธิ:</td>
                        <td class="text-center fw-bold text-danger h4 p-3">฿<?= number_format($total_price, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="index.php" class="btn btn-outline-secondary px-4 py-2">
            <i class="fa-solid fa-arrow-left me-2"></i>เลือกซื้อเพิ่ม
        </a>
        <?php if ($total_price > 0): ?>
            <a href="checkout.php" class="btn btn-success px-5 py-2 fw-bold shadow-sm">
                ชำระเงิน <i class="fa-solid fa-chevron-right ms-2"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include_once("partials/footer.php"); ?>
