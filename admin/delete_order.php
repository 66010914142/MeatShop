<?php
include_once("check_login.php");
include_once("connectdb.php");

if (isset($_GET['id'])) {
    $or_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. ค้นหาชื่อไฟล์รูปภาพสลิปก่อนลบข้อมูล เพื่อนำไปลบไฟล์จริงในเครื่อง
    $sql_img = "SELECT or_slip_img FROM orders WHERE or_id = '$or_id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_array($res_img);
    $file_name = $row_img['or_slip_img'];

    // 2. เริ่มการลบ (ใช้ Transaction เพื่อป้องกันข้อมูลค้างหากลบไม่สำเร็จ)
    mysqli_begin_transaction($conn);

    try {
        // ลบข้อมูลในตารางลูกก่อน (เช่น order_details) เพื่อป้องกัน Error Foreign Key
        // ปรับชื่อตารางตามที่คุณตั้งไว้ใน Database
        mysqli_query($conn, "DELETE FROM order_details WHERE or_id = '$or_id'");

        // ลบข้อมูลในตารางหลัก
        $sql_del = "DELETE FROM orders WHERE or_id = '$or_id'";
        
        if (mysqli_query($conn, $sql_del)) {
            // ลบไฟล์รูปสลิปออกจากโฟลเดอร์ (ถ้ามี)
            if (!empty($file_name)) {
                $path = "../slips/" . $file_name;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            mysqli_commit($conn);
            echo "<script>
                    window.location.href='orders.php';
                  </script>";
        } else {
            throw new Exception("ลบไม่สำเร็จ");
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>
                alert('ไม่สามารถลบข้อมูลได้: " . $e->getMessage() . "');
                window.location.href='orders.php';
              </script>";
    }
} else {
    header("location:orders.php");
}
?>
