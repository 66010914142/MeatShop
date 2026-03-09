<?php
ob_start();
session_start();
include_once("config/connectdb.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    
    // รับค่าจากฟอร์มและป้องกัน SQL Injection
    $u_name = mysqli_real_escape_string($conn, $_POST['u_name']);
    $u_phone = mysqli_real_escape_string($conn, $_POST['u_phone']);
    $u_add = mysqli_real_escape_string($conn, $_POST['u_add']);

    // อัปเดตข้อมูลในตาราง user_login (ตรวจสอบชื่อคอลัมน์ให้ตรงกับรูป SQL)
    $sql = "UPDATE user_login SET u_name = '$u_name', u_phone = '$u_phone', u_add = '$u_add' WHERE u_id = '$u_id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ!'); window.location='profile.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: login.php");
    exit();
}
?>