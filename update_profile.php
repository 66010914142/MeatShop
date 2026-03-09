<?php
session_start();
include_once("config/connectdb.php");

// ตรวจสอบว่ามีการส่งข้อมูลมาจริง และล็อกอินอยู่
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['u_id'])) {
    
    $u_id = $_SESSION['u_id']; // ใช้ u_id ตัวเล็กให้ตรงกันทั้งระบบ
    
    // รับค่าและป้องกันอักขระพิเศษ
    $u_name  = mysqli_real_escape_string($conn, $_POST['u_name']);
    $u_phone = mysqli_real_escape_string($conn, $_POST['u_phone']);
    $u_add   = mysqli_real_escape_string($conn, $_POST['u_add']);

    // คำสั่ง SQL สำหรับอัปเดต
    $sql = "UPDATE user_login SET 
                u_name = '$u_name', 
                u_phone = '$u_phone', 
                u_add = '$u_add' 
            WHERE u_id = '$u_id'";
    
    if (mysqli_query($conn, $sql)) {
        // บันทึกสำเร็จแล้วเด้งกลับหน้าโปรไฟล์
        echo "<script>alert('อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว'); window.location.href='profile.php';</script>";
    } else {
        // กรณีเกิดข้อผิดพลาดจาก Database
        echo "เกิดข้อผิดพลาดในการบันทึก: " . mysqli_error($conn);
    }
} else {
    // ถ้าไม่ได้มาจากการส่งฟอร์ม ให้ส่งกลับหน้าโปรไฟล์
    header("Location: profile.php");
}
?>
