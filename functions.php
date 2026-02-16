<?php
// ฟังก์ชันกำหนด URL หลักของเว็บ (ช่วยให้ Path รูปและลิงก์ไม่เพี้ยน)
function base_url($path = '') {
    return 'http://localhost/MeatShop/' . $path;
}

// ฟังก์ชันนับจำนวนสินค้าในตะกร้า
function cart_count() {
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
?>