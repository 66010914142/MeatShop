<?php 
// 1. เปิด Error Reporting (เฉพาะช่วงพัฒนา)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. เรียก Header (ซึ่งมีแท็ก <html> และ <head> อยู่แล้ว)
require_once ("partials/header.php"); 
include_once("config/connectdb.php"); 
?>

<link rel="stylesheet" href="css/styleproduct.css">

<?php
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // ตรวจสอบชื่อตารางให้เป็นตัวเล็กตามฐานข้อมูล
    $sql = "SELECT * FROM `products` WHERE `P_id` = '$id'"; 
    $rs = mysqli_query($conn, $sql);
    
    if($rs && mysqli_num_rows($rs) > 0) {
        $data = mysqli_fetch_assoc($rs);
?>
        <div class="row bg-white p-4 rounded-4 shadow-sm mt-2">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="product-image-section text-center">
                    <img src="img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" 
                         class="img-fluid rounded-3 shadow"
                         onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="ps-md-4">
                    <h1 class="fw-bold text-dark mb-3"><?= htmlspecialchars($data['P_name']) ?></h1>
                    <div class="h2 text-danger fw-bold mb-4">฿<?= number_format($data['P_price'], 2) ?></div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">รายละเอียดสินค้า:</h6>
                        <p class="text-muted"><?= !empty($data['P_description']) ? nl2br(htmlspecialchars($data['P_description'])) : 'ไม่มีข้อมูลรายละเอียด' ?></p>
                    </div>
                    
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="P_id" value="<?= $data['P_id'] ?>">
                        <div class="mb-4">
                            <label class="form-label fw-bold">จำนวน</label>
                            <div class="input-group" style="width: 140px;">
                                <button type="button" class="btn btn-dark" onclick="stepDown()">-</button>
                                <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $data['P_amonut'] ?>" class="form-control text-center">
                                <button type="button" class="btn btn-dark" onclick="stepUp()">+</button>
                            </div>
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-success btn-lg w-100 fw-bold">🛒 หยิบใส่ตะกร้า</button>
                    </form>
                </div>
            </div>
        </div>
<?php
    }
}
?>

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

<?php 
// 3. ปิดท้ายด้วย Footer (ซึ่งจะปิดแท็ก </div>, </main>, </body>, </html> ให้เอง)
require_once ("partials/footer.php"); 
?>