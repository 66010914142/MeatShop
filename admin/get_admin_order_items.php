<?php
include_once("connectdb.php");

if (isset($_GET['or_id'])) {
    $or_id = mysqli_real_escape_string($conn, $_GET['or_id']);
    
    // ตรวจสอบชื่อคอลัมน์ P_id, P_name ให้ตรงกับ Database ของคุณ
    $sql = "SELECT d.*, p.P_name FROM orders_detail d 
            LEFT JOIN products p ON d.P_id = p.P_id 
            WHERE d.or_id = '$or_id'";
    $rs = mysqli_query($conn, $sql);

    echo '<div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="15%">รูปสินค้า</th>
                        <th class="text-start">ชื่อสินค้า</th>
                        <th>ราคา/หน่วย</th>
                        <th>จำนวน</th>
                        <th class="text-end">รวม</th>
                    </tr>
                </thead>
                <tbody>';

    if (mysqli_num_rows($rs) > 0) {
        while ($item = mysqli_fetch_array($rs)) {
            $p_id = $item['P_id'];
            // Path รูป: ถอยออกไปนอกโฟลเดอร์ admin แล้วเข้า img
            $img_path = "../img/" . $p_id . ".jpg"; 
            $display_img = file_exists($img_path) ? $img_path . "?t=" . time() : "https://via.placeholder.com/50?text=No+Img";
            
            $total_row = $item['p_price'] * $item['p_amount'];

            echo '<tr>
                    <td class="text-center">
                        <img src="'.$display_img.'" style="width:60px; height:60px; object-fit:cover; border-radius:10px; border:1px solid #eee;">
                    </td>
                    <td>
                        <div class="fw-bold">'.$item['P_name'].'</div>
                        <div class="text-muted small">ID: #'.$p_id.'</div>
                    </td>
                    <td class="text-center">฿'.number_format($item['p_price'], 2).'</td>
                    <td class="text-center">'.$item['p_amount'].'</td>
                    <td class="text-end fw-bold text-primary">฿'.number_format($total_row, 2).'</td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="5" class="text-center py-4">ไม่พบรายการสินค้าในออเดอร์นี้</td></tr>';
    }

    echo '</tbody></table></div>';
}
?>
