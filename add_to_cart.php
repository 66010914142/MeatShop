<?php
session_start();

// รับค่าไอดีสินค้าจาก URL (หน้า index ส่งมาเป็น ?id=...)
if (isset($_GET['id']) && $_GET['id'] != "") {
    $p_id = $_GET['id'];
    
    // รับค่าจำนวน ถ้ากดจากหน้าแรกจะเป็น 1 ถ้าหน้าสินค้าจะส่งค่ามา
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1; 

    // 1. สร้างตะกร้าถ้ายังไม่มี
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // 2. ตรวจสอบและเพิ่มสินค้า (ใช้ $p_id ตัวเล็กให้ตรงกันทั้งหมด)
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id] += $qty;
    } else {
        $_SESSION['cart'][$p_id] = $qty;
    }

    // 3. ไปหน้าตะกร้าสินค้า
    header("Location: cart.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>