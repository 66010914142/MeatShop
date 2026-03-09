<?php
session_start();
include_once("config/connectdb.php");

// ตรวจสอบว่ามีการกดปุ่มส่งหลักฐานมาจริงไหม
if (isset($_POST['btn_pay'])) {
    
    // 1. รับค่า or_id จากฟอร์ม
    $or_id = mysqli_real_escape_string($conn, $_POST['or_id']);
    
    // 2. จัดการเรื่องการอัปโหลดไฟล์รูปภาพสลิป
    if (isset($_FILES['slip_img']) && $_FILES['slip_img']['error'] == 0) {
        
        $ext = pathinfo($_FILES['slip_img']['name'], PATHINFO_EXTENSION);
        $allowed_types = array('jpg', 'jpeg', 'png');

        if (in_array(strtolower($ext), $allowed_types)) {
            
            // ตั้งชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
            $new_file_name = "SLIP_" . $or_id . "_" . time() . "." . $ext;
            $upload_path = "slips/" . $new_file_name;

            if (move_uploaded_file($_FILES['slip_img']['tmp_name'], $upload_path)) {
                
                // 3. อัปเดตข้อมูลลงฐานข้อมูลตาราง orders
                // แก้ไข: บันทึกชื่อไฟล์สลิป (or_slip_img) แต่คงสถานะ (or_status) ไว้เป็นค่าเดิม หรือระบุเป็น 'รอชำระเงิน'
                $sql_update = "UPDATE orders SET 
                               or_slip_img = '$new_file_name' 
                               WHERE or_id = '$or_id'";

                if (mysqli_query($conn, $sql_update)) {
                    // แจ้งเตือนสำเร็จและกลับไปหน้าโปรไฟล์
                    echo "<script>
                            alert('ส่งหลักฐานการโอนเงินเรียบร้อยแล้ว! กรุณารอแอดมินตรวจสอบความถูกต้อง');
                            window.location='profile.php';
                          </script>";
                } else {
                    echo "เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล: " . mysqli_error($conn);
                }
                
            } else {
                echo "<script>alert('ไม่สามารถอัปโหลดไฟล์ไปยังเซิร์ฟเวอร์ได้'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('อนุญาตเฉพาะไฟล์ .jpg, .jpeg และ .png เท่านั้น'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('กรุณาเลือกไฟล์สลิปที่ต้องการอัปโหลด'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>