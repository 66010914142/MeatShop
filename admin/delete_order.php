<?php
include_once("check_login.php");
include_once("connectdb.php");

if (isset($_GET['id'])) {
    // รับค่า id ของออเดอร์
    $or_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ดึงชื่อไฟล์รูปสลิปเก็บไว้ก่อนลบข้อมูล (เพื่อเอาไปลบไฟล์ใน Folder)
    $sql_img = "SELECT or_slip_img FROM orders WHERE or_id = '$or_id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_array($res_img);
    $file_name = $row_img['or_slip_img'] ?? '';

    // เริ่ม Transaction เพื่อความปลอดภัย
    mysqli_begin_transaction($conn);

    try {
        // ขั้นตอนที่ 1: ลบจากตารางลูก (order_Details) **ใช้ D ตัวใหญ่ตามรูปของคุณ**
        $sql_detail = "DELETE FROM order_Details WHERE or_id = '$or_id'";
        mysqli_query($conn, $sql_detail);

        // ขั้นตอนที่ 2: ลบจากตารางแม่ (orders)
        $sql_order = "DELETE FROM orders WHERE or_id = '$or_id'";
        mysqli_query($conn, $sql_order);

        // ยืนยันการลบ
        mysqli_commit($conn);

        // 3. ลบไฟล์รูปสลิปในโฟลเดอร์ slips (ถ้ามี)
        if (!empty($file_name)) {
            // เช็คพาธให้ดีนะครับว่า slips อยู่ระดับเดียวกับไฟล์นี้หรืออยู่นอก admin
            $path = "../slips/" . $file_name; 
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // ลบเสร็จแล้วกลับหน้าหลัก
        echo "<script>window.location.href='orders.php';</script>";

    } catch (Exception $e) {
        // หากพลาด ให้ Rollback ข้อมูลกลับมา
        mysqli_rollback($conn);
        echo "<script>
                alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');
                window.location.href='orders.php';
              </script>";
    }
} else {
    header("location:orders.php");
}
?>
