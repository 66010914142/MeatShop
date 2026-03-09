<?php
// แก้ไข Path ให้ถูกต้อง (ถ้าไฟล์นี้อยู่ในโฟลเดอร์ admin ต้องถอยออกไปหา config)
include_once("../config/connectdb.php"); 

if(isset($_GET['or_id'])) {
    $or_id = mysqli_real_escape_string($conn, trim($_GET['or_id']));
    
    // 1. ดึงข้อมูลรายการสินค้า (แก้ไขชื่อตารางเป็น order_Details ตาม DB จริง)
    $sql_items = "SELECT od.*, p.P_name, p.P_id 
                  FROM order_Details od 
                  JOIN products p ON od.P_id = p.P_id 
                  WHERE od.or_id = '$or_id'";
    $res_items = mysqli_query($conn, $sql_items);

    // 2. ดึงข้อมูลรูปสลิปจากตาราง orders
    $order_sql = mysqli_query($conn, "SELECT or_slip_img FROM orders WHERE or_id = '$or_id'");
    $order_data = mysqli_fetch_array($order_sql);

    if(mysqli_num_rows($res_items) > 0) {
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full text-left text-sm border-collapse">';
        echo '<thead class="bg-gray-50 text-gray-600 font-bold border-b">';
        echo '<tr><th class="p-3">สินค้า</th><th class="p-3 text-center">จำนวน</th><th class="p-3 text-right">ราคา/หน่วย</th><th class="p-3 text-right">รวม</th></tr>';
        echo '</thead>';
        echo '<tbody class="divide-y divide-gray-100">';
        
        $grand_total = 0;
        while($item = mysqli_fetch_array($res_items)) {
            // ใช้ชื่อคอลัมน์ quantity และ price_per_unit ตามรูป phpMyAdmin
            $subtotal = $item['quantity'] * $item['price_per_unit'];
            $grand_total += $subtotal;

            // แก้ไข Path รูปสินค้า: ถอยออกจาก admin ไปหา img
            $p_img_path = "../img/" . $item['P_id'] . ".jpg"; 

            echo '<tr class="hover:bg-gray-50">';
            echo '<td class="p-3 flex items-center">';
            echo '<img src="'.$p_img_path.'?t='.time().'" class="w-10 h-10 object-cover rounded mr-3 border shadow-sm" onerror="this.src=\'https://via.placeholder.com/60?text=No+Img\'">';
            echo '<span class="font-medium text-gray-700">' . htmlspecialchars($item['P_name']) . '</span>';
            echo '</td>';
            echo '<td class="p-3 text-center">' . $item['quantity'] . '</td>';
            echo '<td class="p-3 text-right text-gray-500">' . number_format($item['price_per_unit'], 2) . '</td>';
            echo '<td class="p-3 text-right font-bold text-red-600">' . number_format($subtotal, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '<tfoot class="bg-gray-50 font-bold">';
        echo '<tr><td colspan="3" class="p-3 text-right text-gray-600">ราคาสุทธิ</td><td class="p-3 text-right text-lg text-red-600">฿'.number_format($grand_total, 2).'</td></tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';
    } else {
        // แจ้งเตือนถ้าหาข้อมูลใน order_Details ไม่เจอ
        echo '<div class="p-5 text-center text-gray-500">ไม่พบรายการสินค้าในออเดอร์นี้ (SQL Error: '.mysqli_error($conn).')</div>';
    }

    // --- ส่วนแสดงรูปสลิป ---
    if (!empty($order_data['or_slip_img'])) {
        // Path รูปสลิป: ถอยออกจาก admin ไปหา slips
        $slip_path = "../slips/" . $order_data['or_slip_img']; 

        echo '<div class="mt-6 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-center">';
        echo '<p class="text-sm font-bold text-gray-600 mb-3"><i class="fa-solid fa-receipt me-1"></i> หลักฐานการชำระเงิน</p>';
        echo '<a href="'.$slip_path.'" target="_blank">';
        echo '<img src="'.$slip_path.'" class="mx-auto max-h-72 rounded-lg shadow-md border hover:opacity-90 transition" onerror="this.src=\'https://via.placeholder.com/300x400?text=Slip+Not+Found\'">';
        echo '</a>';
        echo '<p class="text-xs text-gray-400 mt-2 italic">* คลิกที่รูปเพื่อดูขนาดใหญ่</p>';
        echo '</div>';
    }
}
?>
