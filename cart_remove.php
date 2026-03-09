<?php
session_start();

if (isset($_GET['id'])) {
    $p_id = $_GET['id'];

    // ตรวจสอบว่ามีสินค้านี้ในตะกร้าจริงไหม
    if (isset($_SESSION['cart'][$p_id])) {
        // ลบสินค้าตัวนี้ออกจาก Array ตะกร้า
        unset($_SESSION['cart'][$p_id]);
    }
}

// ลบเสร็จแล้วให้กลับไปที่หน้าตะกร้าเหมือนเดิม
header("Location: cart.php");
exit();
?>