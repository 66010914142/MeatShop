<?php
session_start();
include_once("config/connectdb.php");

// 1. ตรวจสอบ Login
if (!isset($_SESSION['u_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['u_id'];

// 2. ดึงข้อมูลมาเช็คซ้ำเพื่อความปลอดภัย
$query = mysqli_query($conn, "SELECT u_add, u_phone FROM user_login WHERE u_id = '$u_id'");
$user = mysqli_fetch_array($query);

if (empty($user['u_add']) || empty($user['u_phone']) || $user['u_add'] == "") {
    echo "<script>alert('ข้อมูลที่อยู่ไม่สมบูรณ์ กรุณาแก้ไขข้อมูลก่อนสั่งซื้อ'); window.location.href='profile.php';</script>";
    exit();
}

// หากข้อมูลครบถ้วน จะแสดงหน้าเลือกวิธีการชำระเงินและอัปโหลดสลิปต่อไป
include_once("partials/header.php");
?>
<div class="container py-5">
    <h2 class="fw-bold mb-4">ดำเนินการสั่งซื้อ</h2>
    </div>
<?php include_once("partials/footer.php"); ?>
