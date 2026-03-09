<?php
include_once("connectdb.php");

// 1. รับค่า id และ status จาก URL
if (isset($_GET['id']) && isset($_GET['status'])) {
    $or_id = mysqli_real_escape_string($conn, $_GET['id']);
    $or_status = mysqli_real_escape_string($conn, $_GET['status']);

    // 2. อัปเดตสถานะในตาราง orders
    $sql = "UPDATE orders SET or_status = '$or_status' WHERE or_id = '$or_id'";

    if (mysqli_query($conn, $sql)) {
        // อัปเดตสำเร็จให้เด้งกลับหน้าจัดการออเดอร์
        echo "<script>
                alert('อัปเดตสถานะออเดอร์ #$or_id เรียบร้อยแล้ว');
                window.location='orders.php';
              </script>";
    } else {
        // กรณี SQL ผิดพลาด
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // หากเข้าไฟล์โดยไม่ส่งค่ามาให้เด้งกลับ
    header("Location: orders.php");
    exit();
}
?>