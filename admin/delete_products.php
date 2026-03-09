<?php
include_once("connectdb.php");
include_once("check_login.php");

if(isset($_GET['id'])){
    $p_id = $_GET['id'];
    
    // คำสั่ง SQL สำหรับลบข้อมูล
    $sql = "DELETE FROM products WHERE P_id = '$p_id'";
    
    if(mysqli_query($conn, $sql)){
        // ลบสำเร็จ ให้เด้งกลับไปหน้าเดิม (products.php)
        header("Location: products.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>