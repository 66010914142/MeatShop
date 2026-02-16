<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า - MeatShop</title>
    
    <link rel="stylesheet" href="../css/styleproduct.css">
</head>
<body>

    <div class="navbar">
        <a href="index.php" class="logo">🥩 MeatShop</a>
        <div class="search-box">
            <input type="text" placeholder="ค้นหาสินค้า...">
            <button>🔍 ค้นหา</button>
        </div>
        <div class="nav-links">
            <a href="cart.php">🛒 ตะกร้าสินค้า (0)</a>
            <a href="login.php">👤 เข้าสู่ระบบ / สมัครสมาชิก</a>
        </div>
    </div>

    <div class="container">
        <a href="index.php" class="btn-back">⬅ กลับไปหน้าสินค้า</a>
        
        <?php
        include_once("../config/connectdb.php");
        
        if(isset($_GET['id']) && $_GET['id'] != "") {
            $id = mysqli_real_escape_string($conn, $_GET['id']);
            $sql = "SELECT * FROM `products` WHERE `P_id` = '$id'";
            $rs = mysqli_query($conn, $sql);
            
            if($rs && mysqli_num_rows($rs) > 0) {
                $data = mysqli_fetch_array($rs);
        ?>
                <div class="product-layout">
                    
                    <div class="product-image-section">
                        <img src="../img/<?php echo $data['P_id']; ?>.<?php echo $data['P_img']; ?>" alt="รูปสินค้า" class="main-image">
                        
                        <div class="thumbnail-gallery">
                            <img src="../img/<?php echo $data['P_id']; ?>.<?php echo $data['P_img']; ?>" class="thumbnail active" alt="รูปย่อย 1">
                            <img src="../img/<?php echo $data['P_id']; ?>.<?php echo $data['P_img']; ?>" class="thumbnail" alt="รูปย่อย 2">
                            <img src="../img/<?php echo $data['P_id']; ?>.<?php echo $data['P_img']; ?>" class="thumbnail" alt="รูปย่อย 3">
                        </div>
                    </div>
                    
                    <div class="product-details">
                        <h1 class="product-name"><?php echo $data['P_name']; ?></h1>
                        
                        <div class="product-price">
                            ฿<?php echo number_format($data['P_price'], 2); ?>
                        </div>
                        
                        <div class="product-desc">
                            <strong>รายละเอียดสินค้า:</strong><br>
                            <?php echo $data['p_description']; ?>
                        </div>
                        
                        <form action="cart.php" method="POST" style="display: contents;">
                            <input type="hidden" name="P_id" value="<?php echo $data['P_id']; ?>">
                            
                            <div class="quantity-section">
                                <span class="qty-label">จำนวน</span>
                                <div class="qty-input-group">
                                    <button type="button" class="qty-btn" onclick="document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value) - 1);">-</button>
                                    <input type="number" id="qty" name="quantity" value="1" min="1" max="<?php echo $data['P_amonut']; ?>">
                                    <button type="button" class="qty-btn" onclick="document.getElementById('qty').value = Math.min(<?php echo $data['P_amonut']; ?>, parseInt(document.getElementById('qty').value) + 1);">+</button>
                                </div>
                                <div class="product-stock">มีสินค้าทั้งหมด <span><?php echo $data['P_amonut']; ?></span> ชิ้น</div>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" name="add_to_cart" class="btn-add-cart">🛒 หยิบใส่ตะกร้า</button>
                                <button type="submit" name="buy_now" class="btn-buy-now">ซื้อสินค้าทันที</button>
                            </div>
                        </form>

                    </div>
                    
                </div>
        <?php
            } else {
                echo "<h3 style='color: red; text-align: center; padding: 50px;'>ไม่พบข้อมูลสินค้านี้ในระบบ</h3>";
            }
        } else {
            echo "<h3 style='text-align: center; padding: 50px;'>กรุณาเลือกสินค้าจากหน้าหลักก่อนครับ</h3>";
        }
        ?>
    </div>
</body>
</html>