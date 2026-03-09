<?php
session_start();
include_once("config/connectdb.php");

if (!isset($_SESSION['u_id'])) {
    die("<div class='p-5 text-center text-red-500'>กรุณาเข้าสู่ระบบ</div>");
}

if (isset($_GET['or_id'])) {
    $or_id = mysqli_real_escape_string($conn, trim($_GET['or_id']));
    $u_id = $_SESSION['u_id'];
    
    // 1. ดึงข้อมูลรายการสินค้า (Join products)
    $sql = "SELECT d.*, p.P_name 
            FROM order_Details d 
            JOIN products p ON d.P_id = p.P_id 
            WHERE d.or_id = '$or_id'";
    $rs = mysqli_query($conn, $sql);

    // 2. ดึงข้อมูลรูปสลิปจากตาราง orders (เช็คว่าเป็นของ User คนนี้จริง)
    $order_sql = mysqli_query($conn, "SELECT or_slip_img FROM orders WHERE or_id = '$or_id' AND u_id = '$u_id'");
    $order_data = mysqli_fetch_array($order_sql);

    if ($rs && mysqli_num_rows($rs) > 0) {
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full text-left text-sm border-collapse">';
        echo '<thead class="bg-gray-50 text-gray-600 font-bold border-b">';
        echo '<tr><th class="p-3">สินค้า</th><th class="p-3 text-center">จำนวน</th><th class="p-3 text-right">ราคา/หน่วย</th><th class="p-3 text-right">รวม</th></tr>';
        echo '</thead>';
        echo '<tbody class="divide-y divide-gray-100">';
        
        $grand_total = 0;
        while ($item = mysqli_fetch_array($rs)) {
            $subtotal = $item['quantity'] * $item['price_per_unit'];
            $grand_total += $subtotal;
            $p_img = "img/" . $item['P_id'] . ".jpg";

            echo '<tr class="hover:bg-gray-50">';
            echo '<td class="p-3 flex items-center">';
            echo '<img src="'.$p_img.'" class="w-10 h-10 object-cover rounded mr-3 border shadow-sm" onerror="this.src=\'https://via.placeholder.com/60?text=No+Img\'">';
            echo '<span class="font-medium text-gray-700">' . htmlspecialchars($item['P_name']) . '</span>';
            echo '</td>';
            echo '<td class="p-3 text-center">' . $item['quantity'] . '</td>';
            echo '<td class="p-3 text-right text-gray-500">' . number_format($item['price_per_unit'], 2) . '</td>';
            echo '<td class="p-3 text-right font-bold text-blue-600">' . number_format($subtotal, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '<tfoot class="bg-gray-50 font-bold">';
        echo '<tr><td colspan="3" class="p-3 text-right text-gray-600">ราคาสุทธิ</td><td class="p-3 text-right text-lg text-blue-600">฿'.number_format($grand_total, 2).'</td></tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';

        // --- ส่วนที่เพิ่ม: แสดงรูปสลิปที่ผู้ใช้แนบ ---
        if (!empty($order_data['or_slip_img'])) {
            $slip_path = "slips/" . $order_data['or_slip_img']; 
            echo '<div class="mt-6 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-center">';
            echo '<p class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider"><i class="fa-solid fa-receipt me-1"></i> หลักฐานการชำระเงินที่แนบมา</p>';
            echo '<a href="'.$slip_path.'" target="_blank" class="inline-block">';
            echo '<img src="'.$slip_path.'" class="mx-auto max-h-64 rounded-lg shadow-sm border border-white hover:scale-[1.02] transition-transform duration-200" onerror="this.src=\'https://via.placeholder.com/300x400?text=Slip+Not+Found\'">';
            echo '</a>';
            echo '<p class="text-[10px] text-gray-400 mt-2 italic">* คลิกที่รูปเพื่อขยายใหญ่</p>';
            echo '</div>';
        }

    } else {
        echo '<div class="p-10 text-center text-gray-400">ไม่พบรายการสินค้า (SQL Error: '.mysqli_error($conn).')</div>';
    }
}
?>
