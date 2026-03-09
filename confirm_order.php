<?php
session_start();
include_once("config/connectdb.php");

// 1. ตรวจสอบการ Login
if (!isset($_SESSION['u_id'])) { 
    die("กรุณาล็อกอินก่อนสั่งซื้อ"); 
}

if (isset($_POST['btn_confirm']) && isset($_FILES['slip_img'])) {
    $u_id = $_SESSION['u_id']; 
    $or_date = date("Y-m-d H:i:s");
    $or_id = "ORD" . date("YmdHis"); 
    $total_price = 0;

    // 1. จัดการอัปโหลดไฟล์สลิป
    $ext = strtolower(pathinfo($_FILES['slip_img']['name'], PATHINFO_EXTENSION));
    $new_file_name = "SLIP_" . $or_id . "." . $ext;
    $upload_dir = "slips/";
    
    if (!is_dir($upload_dir)) { 
        mkdir($upload_dir, 0777, true); 
    }
    
    $upload_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($_FILES['slip_img']['tmp_name'], $upload_path)) {
        
        // 2. คำนวณยอดรวมสุทธิจากตะกร้า
        foreach ($_SESSION['cart'] as $p_id => $qty) {
            $p_query = mysqli_query($conn, "SELECT P_price FROM products WHERE P_id = '$p_id'");
            $p_row = mysqli_fetch_array($p_query);
            if ($p_row) { 
                $total_price += ($p_row['P_price'] * $qty); 
            }
        }

        // 3. บันทึกลงตาราง orders
        $sql_order = "INSERT INTO orders (or_id, u_id, or_total_amount, or_status, or_date, or_slip_img) 
                      VALUES ('$or_id', '$u_id', '$total_price', 'รอชำระเงิน', '$or_date', '$new_file_name')";

        if (mysqli_query($conn, $sql_order)) {
            // 4. บันทึกรายการสินค้าลง order_Details (ตัว D ใหญ่ตาม Database ของคุณ)
            foreach ($_SESSION['cart'] as $p_id => $qty) {
                $p_query = mysqli_query($conn, "SELECT P_price FROM products WHERE P_id = '$p_id'");
                $p_row = mysqli_fetch_array($p_query);
                
                if ($p_row) {
                    $price = $p_row['P_price'];
                    
                    // แก้ไขชื่อตารางเป็น order_Details ตามรูปโครงสร้าง DB ที่คุณส่งมา
                    $sql_detail = "INSERT INTO order_Details (or_id, P_id, quantity, price_per_unit) 
                                   VALUES ('$or_id', '$p_id', '$qty', '$price')";
                    
                    mysqli_query($conn, $sql_detail);
                    
                    // ตัดสต็อกสินค้า (ใช้คอลัมน์ P_amonut ตามที่คุณสะกดไว้ใน DB)
                    mysqli_query($conn, "UPDATE products SET P_amonut = P_amonut - $qty WHERE P_id = '$p_id'");
                }
            }
            
            unset($_SESSION['cart']);
            echo "<script>alert('สั่งซื้อและส่งสลิปเรียบร้อย! #$or_id'); window.location='profile.php';</script>";
        } else {
            echo "Error Database: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('อัปโหลดสลิปไม่สำเร็จ (Check Folder Permission)'); window.history.back();</script>";
    }
} else {
    header("Location: cart.php");
}
?>
