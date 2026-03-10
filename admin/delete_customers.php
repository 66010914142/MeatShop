<?php
include_once("check_login.php");
include_once("connectdb.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // คำสั่งลบ
    $sql = "DELETE FROM user_login WHERE u_id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        // กรณี Error เช่น มีความสัมพันธ์ Foreign Key กับตารางอื่น
        echo "ไม่สามารถลบได้: ข้อมูลลูกค้านี้อาจมีการใช้งานอยู่ในระบบออเดอร์";
    }
} else {
    echo "ไม่พบรหัสลูกค้าที่ต้องการลบ";
}
?>
