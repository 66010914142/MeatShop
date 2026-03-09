<?php
include_once("connectdb.php");
include_once("check_login.php");

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ใช้ Prepared Statement เพื่อความปลอดภัยจาก SQL Injection
    $stmt = $conn->prepare("DELETE FROM categories WHERE c_id = ?");
    $stmt->bind_param("s", $id); 

    if ($stmt->execute()) {
        // ลบสำเร็จ แจ้งเตือนและกลับไปหน้าหลัก
        echo "<script>
                alert('ลบข้อมูลเรียบร้อย');
                window.location.href='categories.php'; 
              </script>";
    } else {
        echo "เกิดข้อผิดพลาดในการลบ: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "ไม่พบ ID ที่ต้องการลบ";
}

$conn->close();
?>