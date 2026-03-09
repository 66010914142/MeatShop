<?php
session_start();
// ตรวจสอบ Path: หากไฟล์นี้อยู่ในโฟลเดอร์ root ให้ใช้ config/connectdb.php
// แต่ถ้าอยู่ในโฟลเดอร์ย่อย ให้เช็ค Path อีกครั้ง
include_once("config/connectdb.php");

if (!isset($_SESSION['u_id'])) {
    die("<div class='p-5 text-center text-red-500'>กรุณาเข้าสู่ระบบ</div>");
}

if (isset($_GET['or_id'])) {
    $or_id = mysqli_real_escape_string($conn, trim($_GET['or_id']));
    $u_id = $_SESSION['u_id'];
    
    // 1. ดึงข้อมูลรายการสินค้า โดยเช็ค u_id จากตาราง orders เพื่อความปลอดภัย (Security)
    // ใช้ชื่อตาราง order_Details และคอลัมน์ quantity, price_per_unit ตาม DB ของคุณ
    $sql = "SELECT d.*, p.P_name 
            FROM order_Details d 
            JOIN products p ON d.P_id = p.P_id 
            JOIN orders o ON d.or_id = o.or_id
            WHERE d.or_id = '$or_id' AND o.u_id = '$u_id'";
    
    $rs = mysqli_query($conn, $sql);

    if ($rs && mysqli_num_rows($rs) > 0) {
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full text-left text-sm border-collapse">';
        echo '<thead class="bg-gray-50 text-gray-600 font-bold border-b">';
        echo '<tr>
                <th class="p-3">สินค้า</th>
                <th class="p-3 text-center">จำนวน</th>
                <th class="p-3 text-right">ราคา/หน่วย</th>
                <th class="p-3 text-right">รวม</th>
              </tr>';
        echo '</thead>';
        echo '<tbody class="divide-y divide-gray-100">';
        
        $grand_total = 0;
        while ($item = mysqli_fetch_array($rs)) {
            $subtotal = $item['quantity'] * $item['price_per_unit'];
            $grand_total += $subtotal;
            
            // Path รูปภาพสินค้า (ตรวจสอบว่าโฟลเดอร์ img อยู่ที่ root ของโปรเจกต์)
            $p_img = "img/" . $item['P_id'] . ".jpg";

            echo '<tr class="hover:bg-gray-50 transition">';
            echo '<td class="p-3 flex items-center">';
            echo '<img src="'.$p_img.'?t='.time().'" class="w-10 h-10 object-cover rounded mr-3 border shadow-sm" onerror="this.src=\'https://via.placeholder.com/60?text=No+Img\'">';
            echo '<span class="font-medium text-gray-700">' . htmlspecialchars($item['P_name']) . '</span>';
            echo '</td>';
            echo '<td class="p-3 text-center">' . $item['quantity'] . ' ชิ้น</td>';
            echo '<td class="p-3 text-right text-gray-500">' . number_format($item['price_per_unit'], 2) . '</td>';
            echo '<td class="p-3 text-right font-bold text-blue-600">฿' . number_format($subtotal, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '<tfoot class="bg-gray-50 font-bold">';
        echo '<tr>
                <td colspan="3" class="p-3 text-right text-gray-600">ราคาสุทธิ</td>
                <td class="p-3 text-right text-lg text-blue-600">฿'.number_format($grand_total, 2).'</td>
              </tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';
    } else {
        // หากไม่พบข้อมูล ให้แสดง Error เพื่อช่วย Debug
        echo '<div class="p-10 text-center text-gray-400">';
        echo '<i class="fa-solid fa-circle-exclamation mb-2 text-2xl"></i><br>';
        echo 'ไม่พบรายละเอียดสินค้าในออเดอร์นี้';
        if (!$rs) { echo '<br><small class="text-red-300">SQL Error: ' . mysqli_error($conn) . '</small>'; }
        echo '</div>';
    }
}
?>
