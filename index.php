<?php
// 1. จัดการเรื่อง Error
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once("config/connectdb.php");
include_once("partials/header.php"); // header จะเปิดแท็ก <body> และ <div class="container"> ไว้

// 2. รับค่าจาก URL (แก้ไขจาก cat_id เป็น C_id ให้ตรงกับ Header)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$cat_id = isset($_GET['C_id']) ? mysqli_real_escape_string($conn, trim($_GET['C_id'])) : '';

// 3. ตั้งค่าการแบ่งหน้า
$per_page = 16; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$start = ($page - 1) * $per_page; 

// 4. สร้างเงื่อนไขการค้นหา
$conditions = [];
if ($search !== "") {
    $conditions[] = "P_name LIKE '%$search%'";
}
if ($cat_id !== "") {
    $conditions[] = "C_id = '$cat_id'"; // ใช้ C_id ตามชื่อคอลัมน์ใน DB
}

$where_clause = "";
if (count($conditions) > 0) {
    $where_clause = " WHERE " . implode(" AND ", $conditions);
}

// 5. นับจำนวนและดึงข้อมูล
$sql_total = "SELECT COUNT(*) as total FROM `products` $where_clause";
$rs_total = mysqli_query($conn, $sql_total);
$total_records = ($rs_total) ? mysqli_fetch_assoc($rs_total)['total'] : 0;
$total_pages = ceil($total_records / $per_page); 

$sql = "SELECT * FROM `products` $where_clause LIMIT $start, $per_page";
$rs = mysqli_query($conn, $sql);
?>

<div class="py-4"> <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php 
        if ($rs && mysqli_num_rows($rs) > 0) {
            while($data = mysqli_fetch_array($rs)) { 
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                    <img src="img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" 
                         class="card-img-top" 
                         style="height: 220px; object-fit: contain; padding: 15px; background: #fff;"
                         onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                    
                    <div class="card-body text-center d-flex flex-column">
                        <h6 class="card-title mb-2 text-dark" style="height: 40px; overflow: hidden;"><?= htmlspecialchars($data['P_name']) ?></h6>
                        <p class="text-danger fw-bold h5 mb-1">฿<?= number_format($data['P_price'], 2) ?></p>
                        <p class="small text-muted mb-3">คงเหลือ: <?= $data['P_amonut'] ?> ชิ้น</p>
                        
                        <div class="d-grid gap-2 mt-auto">
                            <a href="product_detail.php?id=<?= $data['P_id'] ?>" class="btn btn-sm btn-outline-primary">รายละเอียด</a>
                            <a href="add_to_cart.php?id=<?= $data['P_id'] ?>" class="btn btn-sm btn-success">ลงตะกร้า</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center py-5'><h4>ไม่พบสินค้าในหมวดหมู่นี้</h4></div>";
        }
        ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php 
            $params = $_GET; unset($params['page']);
            $url_params = http_build_query($params);
            $url_params = ($url_params != "") ? "&" . $url_params : "";
            ?>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $url_params ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php 
// ย้าย footer มาไว้ข้างล่างสุด เพื่อปิดแท็ก HTML หลังจากแสดงสินค้าเสร็จแล้ว
include_once("partials/footer.php"); 
?>