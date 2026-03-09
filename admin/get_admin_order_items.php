<?php
include_once("connectdb.php");
$or_id = mysqli_real_escape_string($conn, $_GET['or_id']);

$sql = "SELECT od.*, p.P_name, p.P_img 
        FROM order_details od 
        JOIN products p ON od.P_id = p.P_id 
        WHERE od.or_id = '$or_id'";
$rs = mysqli_query($conn, $sql);

echo '<table class="table table-striped mb-0">';
echo '<thead class="table-secondary"><tr><th>สินค้า</th><th class="text-center">จำนวน</th><th class="text-end">ราคา/หน่วย</th><th class="text-end">รวม</th></tr></thead>';
echo '<tbody>';
while($row = mysqli_fetch_array($rs)) {
    $total = $row['quantity'] * $row['price_per_unit'];
    echo '<tr>';
    echo '<td><img src="../img/'.$row['P_id'].'.'.$row['P_img'].'" width="40" class="rounded me-2">'.$row['P_name'].'</td>';
    echo '<td class="text-center">'.$row['quantity'].'</td>';
    echo '<td class="text-end">'.number_format($row['price_per_unit'], 2).'</td>';
    echo '<td class="text-end fw-bold">'.number_format($total, 2).'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
?>