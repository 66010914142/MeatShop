<?php 
// เปิดการแจ้งเตือน Error เพื่อดูสาเหตุที่แท้จริงหากยังเข้าไม่ได้
ini_set('display_errors', 1);
error_reporting(E_ALL);

// แก้ไข Path: ตัด ../ ออก เพราะไฟล์อยู่ในโฟลเดอร์หลักแล้ว
require_once ("partials/header.php"); 
include_once("config/connectdb.php"); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า - MeatShop</title>
    <link rel="stylesheet" href="css/styleproduct.css">
</head>
<body>
    <div class="container mt-5">
        <a href="index.php" class="btn btn-outline-secondary mb-4">⬅ กลับไปหน้าสินค้า</a>
        
        <?php
        if(isset($_GET['id']) && $_GET['id'] != "") {
            $id = mysqli_real_escape_string($conn, $_GET['id']);
            
            // ตรวจสอบชื่อตาราง (ถ้าใน DB เป็น products ตัวเล็ก ให้แก้เป็นตัวเล็กครับ)
            $sql = "SELECT * FROM `Products` WHERE `P_id` = '$id'"; 
            $rs = mysqli_query($conn, $sql);
            
            if($rs && mysqli_num_rows($rs) > 0) {
                $data = mysqli_fetch_array($rs);
        ?>
                <div class="product-layout">
                    <div class="product-image-section">
                        <img src="img/<?php echo $data['P_id']; ?>.<?php echo $data['P_img']; ?>" alt="รูปสินค้า" class="main-image">
                    </div>
                    
                    <div class="product-details">
                        <h1 class="product-name"><?php echo $data['P_name']; ?></h1>
                        
                        <div class="product-price">
                            ฿<?php echo number_format($data['P_price'], 2); ?>
                        </div>
                        
                        <div class="product-desc">
                            <strong>รายละเอียดสินค้า:</strong><br>
                            <?php echo isset($data['P_description']) ? $data['P_description'] : 'ไม่มีรายละเอียดสินค้า'; ?>
                        </div>
                        
                        <form action="cart.php" method="POST" class="mt-4">
                            <input type="hidden" name="P_id" value="<?php echo $data['P_id']; ?>">
                            
                            <div class="quantity-section mb-3">
                                <span class="qty-label">จำนวน</span>
                                <div class="input-group" style="width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="stepDown()">-</button>
                                    <input type="number" id="qty" name="quantity" value="1" min="1" max="<?php echo $data['P_amonut']; ?>" class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="stepUp()">+</button>
                                </div>
                                <div class="mt-2 text-muted small">คงเหลือในสต็อก: <?php echo $data['P_amonut']; ?> ชิ้น</div>
                            </div>
                            
                            <div class="action-buttons d-grid gap-2">
                                <button type="submit" name="add_to_cart" class="btn btn-success btn-lg">🛒 หยิบใส่ตะกร้า</button>
                                <button type="submit" name="buy_now" class="btn btn-primary btn-lg">ซื้อสินค้าทันที</button>
                            </div>
                        </form>
                    </div>
                </div>
        <?php
            } else {
                echo "<div class='alert alert-warning text-center'>ไม่พบข้อมูลสินค้านี้ในระบบ</div>";
            }
        }
        ?>
    </div>

    <script>
    function stepUp() {
        var input = document.getElementById('qty');
        if (parseInt(input.value) < parseInt(input.max)) input.value = parseInt(input.value) + 1;
    }
    function stepDown() {
        var input = document.getElementById('qty');
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    }
    </script>
</body>
</html>